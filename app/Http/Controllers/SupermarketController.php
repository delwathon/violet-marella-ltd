<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\InventoryLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SupermarketController extends Controller
{
    /**
     * Display the supermarket dashboard.
     */
    public function index()
    {
        $staff = Auth::guard('staff')->user();

        // Get today's sales data
        $todaySales = Sale::whereDate('sale_date', today())->sum('total_amount');
        $todayTransactions = Sale::whereDate('sale_date', today())->count();
        $totalStock = Product::sum('stock_quantity');
        $customersServed = Customer::where('total_orders', '>', 0)->count();

        // Get low stock products
        $lowStockProducts = Product::lowStock()->take(5)->get();

        // Get recent transactions
        $recentTransactions = Sale::with(['customer', 'saleItems.product'])
                                ->latest()
                                ->take(10)
                                ->get();

        // Get top selling products
        $topProducts = Product::withCount(['saleItems as total_sold'])
                            ->orderBy('total_sold', 'desc')
                            ->take(5)
                            ->get();

        return view('supermarket.dashboard', compact(
            'todaySales',
            'todayTransactions',
            'totalStock',
            'customersServed',
            'lowStockProducts',
            'recentTransactions',
            'topProducts'
        ));
    }

    /**
     * Display the POS interface.
     */
    public function pos()
    {
        $categories = Category::active()->ordered()->get();
        $products = Product::active()->with('category')->paginate(20);

        // Get cart from session
        $cart = Session::get('pos_cart', []);

        return view('supermarket.pos', compact('categories', 'products', 'cart'));
    }

    /**
     * Add product to cart.
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->track_stock && $product->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Available: ' . $product->stock_quantity
            ], 400);
        }

        $cart = Session::get('pos_cart', []);
        $cartKey = $request->product_id;

        if (isset($cart[$cartKey])) {
            $newQuantity = $cart[$cartKey]['quantity'] + $request->quantity;

            if ($product->track_stock && $product->stock_quantity < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock. Available: ' . $product->stock_quantity
                ], 400);
            }

            $cart[$cartKey]['quantity'] = $newQuantity;
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $request->quantity,
                'sku' => $product->sku,
                'tax_rate' => $product->tax_rate,
            ];
        }

        Session::put('pos_cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart',
            'cart' => $this->getCartSummary()
        ]);
    }

    /**
     * Update cart item quantity.
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart = Session::get('pos_cart', []);

        if ($product->track_stock && $product->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Available: ' . $product->stock_quantity
            ], 400);
        }

        if (isset($cart[$request->product_id])) {
            $cart[$request->product_id]['quantity'] = $request->quantity;
            Session::put('pos_cart', $cart);
        }

        return response()->json([
            'success' => true,
            'cart' => $this->getCartSummary()
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function removeFromCart($productId)
    {
        $cart = Session::get('pos_cart', []);
        unset($cart[$productId]);
        Session::put('pos_cart', $cart);

        return response()->json([
            'success' => true,
            'cart' => $this->getCartSummary()
        ]);
    }

    /**
     * Process checkout.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'payment_method' => 'required|in:cash,card,transfer,split',
            'amount_paid' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $cart = Session::get('pos_cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        DB::beginTransaction();

        try {
            $staff = Auth::guard('staff')->user();
            $subtotal = 0;
            $taxAmount = 0;

            // Create sale
            $sale = Sale::create([
                'customer_id' => $request->customer_id,
                'staff_id' => $staff->id,
                'subtotal' => 0, // Will be calculated
                'tax_amount' => 0, // Will be calculated
                'discount_amount' => $request->discount_amount ?? 0,
                'total_amount' => 0, // Will be calculated
                'amount_paid' => $request->amount_paid,
                'change_amount' => 0, // Will be calculated
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'payment_status' => 'completed',
                'notes' => $request->notes,
                'sale_date' => now(),
            ]);

            // Create sale items and calculate totals
            foreach ($cart as $item) {
                $product = Product::findOrFail($item['product_id']);
                $itemSubtotal = $item['price'] * $item['quantity'];
                $itemTax = $itemSubtotal * ($item['tax_rate'] / 100);
                $itemTotal = $itemSubtotal + $itemTax;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total_price' => $itemTotal,
                    'tax_amount' => $itemTax,
                ]);

                $subtotal += $itemSubtotal;
                $taxAmount += $itemTax;

                // Update stock
                if ($product->track_stock) {
                    $product->decrement('stock_quantity', $item['quantity']);

                    // Log inventory change
                    InventoryLog::create([
                        'product_id' => $product->id,
                        'staff_id' => $staff->id,
                        'action_type' => 'sale',
                        'quantity_change' => -$item['quantity'],
                        'previous_stock' => $product->stock_quantity + $item['quantity'],
                        'new_stock' => $product->stock_quantity,
                        'reference_number' => $sale->receipt_number,
                        'action_date' => now(),
                    ]);
                }
            }

            // Update sale totals
            $totalAmount = $subtotal + $taxAmount - ($request->discount_amount ?? 0);
            $changeAmount = max(0, $request->amount_paid - $totalAmount);

            $sale->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'change_amount' => $changeAmount,
            ]);

            // Update customer stats if customer exists
            if ($request->customer_id) {
                $customer = Customer::findOrFail($request->customer_id);
                $customer->updateStats($totalAmount);
            }

            DB::commit();

            // Clear cart
            Session::forget('pos_cart');

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully',
                'sale' => $sale->load(['customer', 'saleItems.product']),
                'receipt_number' => $sale->receipt_number
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error processing sale: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search products.
     */
    public function searchProducts(Request $request)
    {
        $query = $request->get('q');
        $categoryId = $request->get('category_id');

        $products = Product::active()
                          ->with('category')
                          ->when($query, function ($q) use ($query) {
                              return $q->search($query);
                          })
                          ->when($categoryId, function ($q) use ($categoryId) {
                              return $q->where('category_id', $categoryId);
                          })
                          ->paginate(20);

        return response()->json($products);
    }

    /**
     * Display products page.
     */
    public function products()
    {
        $categories = Category::active()->ordered()->get();
        $products = Product::with('category')->paginate(20);

        return view('supermarket.products', compact('categories', 'products'));
    }

    /**
     * Display customers page.
     */
    public function customers()
    {
        $customers = Customer::with(['sales' => function ($query) {
            $query->latest()->limit(3);
        }])->paginate(20);

        return view('supermarket.customers', compact('customers'));
    }

    /**
     * Store new customer.
     */
    public function storeCustomer(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'customer_type' => 'required|in:walk-in,regular,wholesale,staff',
        ]);

        $customer = Customer::create($request->all());

        return response()->json([
            'success' => true,
            'customer' => $customer
        ]);
    }

    /**
     * Display sales page.
     */
    public function sales()
    {
        $sales = Sale::with(['customer', 'staff', 'saleItems.product'])
                    ->latest()
                    ->paginate(20);

        return view('supermarket.sales', compact('sales'));
    }

    /**
     * Display specific sale.
     */
    public function showSale(Sale $sale)
    {
        $sale->load(['customer', 'staff', 'saleItems.product', 'payments']);

        return view('supermarket.sale-details', compact('sale'));
    }

    /**
     * Display reports page.
     */
    public function reports()
    {
        // Get date range for reports
        $startDate = request('start_date', now()->startOfMonth());
        $endDate = request('end_date', now()->endOfMonth());

        // Sales summary
        $totalSales = Sale::whereBetween('sale_date', [$startDate, $endDate])
                         ->sum('total_amount');

        $totalTransactions = Sale::whereBetween('sale_date', [$startDate, $endDate])
                               ->count();

        // Top products
        $topProducts = Product::withCount(['saleItems as total_sold'])
                            ->withSum('saleItems as total_revenue', 'total_price')
                            ->orderBy('total_sold', 'desc')
                            ->take(10)
                            ->get();

        // Daily sales chart data
        $dailySales = Sale::selectRaw('DATE(sale_date) as date, SUM(total_amount) as total')
                         ->whereBetween('sale_date', [$startDate, $endDate])
                         ->groupBy('date')
                         ->orderBy('date')
                         ->get();

        return view('supermarket.reports', compact(
            'totalSales',
            'totalTransactions',
            'topProducts',
            'dailySales',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display inventory page.
     */
    public function inventory()
    {
        $products = Product::with('category')->paginate(20);
        $lowStockProducts = Product::lowStock()->get();

        return view('supermarket.inventory', compact('products', 'lowStockProducts'));
    }

    /**
     * Adjust inventory.
     */
    public function adjustInventory(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity_change' => 'required|integer',
            'action_type' => 'required|in:adjustment,purchase,return,damage,expiry',
            'reason' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);
        $staff = Auth::guard('staff')->user();

        // Create inventory log
        $log = InventoryLog::logStockChange(
            $request->product_id,
            $staff->id,
            $request->action_type,
            $request->quantity_change,
            $request->reason
        );

        // Update product stock
        $product->increment('stock_quantity', $request->quantity_change);

        return response()->json([
            'success' => true,
            'message' => 'Inventory adjusted successfully',
            'new_stock' => $product->stock_quantity
        ]);
    }

    /**
     * Get cart summary.
     */
    private function getCartSummary()
    {
        $cart = Session::get('pos_cart', []);
        $subtotal = 0;
        $taxAmount = 0;
        $totalItems = 0;

        foreach ($cart as $item) {
            $itemSubtotal = $item['price'] * $item['quantity'];
            $itemTax = $itemSubtotal * ($item['tax_rate'] / 100);

            $subtotal += $itemSubtotal;
            $taxAmount += $itemTax;
            $totalItems += $item['quantity'];
        }

        $total = $subtotal + $taxAmount;

        return [
            'items' => $cart,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'total_items' => $totalItems,
            'count' => count($cart)
        ];
    }
}
