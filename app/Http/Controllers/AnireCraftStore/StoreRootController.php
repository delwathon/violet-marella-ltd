<?php

namespace App\Http\Controllers\AnireCraftStore;

use App\Http\Controllers\Controller;
use App\Models\StoreProduct;
use App\Models\StoreCategory;
use App\Models\StoreSale;
use App\Models\StoreSaleItem;
use App\Models\StoreCustomer;
use App\Models\StoreInventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class StoreRootController extends Controller
{
    /**
     * Display the POS interface
     */
    public function index(Request $request)
    {
        $user = Auth::guard('user')->user();
        
        // Get today's date
        $today = Carbon::today();
        
        // Today's Sales Statistics
        $todaySales = StoreSale::completed()
            ->whereDate('sale_date', $today)
            ->sum('total_amount');
        
        $todayTransactions = StoreSale::completed()
            ->whereDate('sale_date', $today)
            ->count();
        
        // Total Stock (active products with stock tracking)
        $totalStock = StoreProduct::active()
            ->where('track_stock', true)
            ->sum('stock_quantity');
        
        // Customers Served Today (unique customers)
        $customersServed = StoreSale::whereDate('sale_date', $today)
            ->whereNotNull('store_customer_id')
            ->distinct('store_customer_id')
            ->count('store_customer_id');
        
        // Get all active categories for filtering
        $categories = StoreCategory::active()
            ->ordered()
            ->get();
        
        // Get products with search and filtering - limit to 8
        $query = StoreProduct::active()->with('category');
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }
        
        // Category filter
        if ($request->has('category') && $request->category) {
            $query->where('store_category_id', $request->category);
        }
        
        // Paginate products - limit to 8 per page
        $products = $query->orderBy('name')->paginate(8);
        
        // Recent Transactions (today's transactions)
        $recentTransactions = StoreSale::with(['customer', 'saleItems.product', 'staff'])
            ->whereDate('sale_date', $today)
            ->latest('sale_date')
            ->limit(10)
            ->get();
        
        return view('pages.anire-craft-store.index', compact(
            'user',
            'todaySales',
            'todayTransactions',
            'totalStock',
            'customersServed',
            'categories',
            'products',
            'recentTransactions'
        ));
    }
    
    /**
     * Search products (AJAX endpoint)
     */
    public function searchProducts(Request $request)
    {
        $search = $request->get('q', $request->get('search', ''));
        $categoryId = $request->get('category_id');
        
        $query = StoreProduct::active()->with('category');
        
        if ($search) {
            $query->search($search);
        }
        
        if ($categoryId) {
            $query->where('store_category_id', $categoryId);
        }
        
        // Limit to 8 products
        $products = $query->orderBy('name')->limit(8)->get();
        
        return response()->json([
            'success' => true,
            'data' => $products,
            'total' => $products->count()
        ]);
    }
    
    /**
     * Get product details (AJAX endpoint)
     */
    public function getProduct($id)
    {
        $product = StoreProduct::with('category')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'product' => $product
        ]);
    }
    
    /**
     * Add product to cart (AJAX endpoint)
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:store_products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        
        $product = StoreProduct::findOrFail($request->product_id);
        
        // Check stock availability
        if ($product->track_stock && $product->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available'
            ], 400);
        }
        
        // Get cart from session
        $cart = Session::get('store_cart', []);
        
        // Check if product already exists in cart
        $existingIndex = array_search($request->product_id, array_column($cart, 'product_id'));
        
        if ($existingIndex !== false) {
            // Update quantity
            $cart[$existingIndex]['quantity'] += $request->quantity;
            $cart[$existingIndex]['total_price'] = $cart[$existingIndex]['quantity'] * $cart[$existingIndex]['price'];
        } else {
            // Add new item
            $cart[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $request->quantity,
                'total_price' => $product->price * $request->quantity,
                'tax_rate' => $product->tax_rate ?? 7.5
            ];
        }
        
        Session::put('store_cart', $cart);
        
        return response()->json([
            'success' => true,
            'message' => 'Product added to cart',
            'product_name' => $product->name
        ]);
    }
    
    /**
     * Get cart contents (AJAX endpoint)
     */
    public function getCart()
    {
        $cart = Session::get('store_cart', []);
        
        return response()->json([
            'success' => true,
            'cart' => $cart
        ]);
    }
    
    /**
     * Get cart summary (AJAX endpoint)
     */
    public function getCartSummary()
    {
        $cart = Session::get('store_cart', []);
        
        $count = count($cart);
        $subtotal = 0;
        $taxAmount = 0;
        
        foreach ($cart as $item) {
            $itemSubtotal = $item['price'] * $item['quantity'];
            $subtotal += $itemSubtotal;
            
            // Calculate tax
            $taxRate = $item['tax_rate'] ?? 7.5;
            $taxAmount += ($itemSubtotal * $taxRate / 100);
        }
        
        $total = $subtotal + $taxAmount;
        
        return response()->json([
            'success' => true,
            'count' => $count,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total
        ]);
    }
    
    /**
     * Update cart item quantity (AJAX endpoint)
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required|integer',
        ]);
        
        $cart = Session::get('store_cart', []);
        $index = array_search($request->product_id, array_column($cart, 'product_id'));
        
        if ($index !== false) {
            $newQuantity = $cart[$index]['quantity'] + $request->quantity;
            
            if ($newQuantity <= 0) {
                // Remove item if quantity is 0 or less
                array_splice($cart, $index, 1);
            } else {
                // Check stock if product tracks stock
                $product = StoreProduct::find($request->product_id);
                if ($product && $product->track_stock && $newQuantity > $product->stock_quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient stock available'
                    ], 400);
                }
                
                $cart[$index]['quantity'] = $newQuantity;
                $cart[$index]['total_price'] = $cart[$index]['price'] * $newQuantity;
            }
            
            Session::put('store_cart', $cart);
            
            return response()->json([
                'success' => true,
                'message' => 'Cart updated'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Item not found in cart'
        ], 404);
    }
    
    /**
     * Remove item from cart (AJAX endpoint)
     */
    public function removeFromCart($productId)
    {
        $cart = Session::get('store_cart', []);
        $index = array_search($productId, array_column($cart, 'product_id'));
        
        if ($index !== false) {
            array_splice($cart, $index, 1);
            Session::put('store_cart', $cart);
            
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Item not found in cart'
        ], 404);
    }
    
    /**
     * Clear cart (AJAX endpoint)
     */
    public function clearCart()
    {
        Session::forget('store_cart');
        
        return response()->json([
            'success' => true,
            'message' => 'Cart cleared'
        ]);
    }
    
    /**
     * Process sale/checkout
     */
    public function processSale(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,card,transfer,mobile_money',
            'amount_paid' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:store_customers,id',
        ]);
        
        // Get cart from session
        $cart = Session::get('store_cart', []);
        
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 400);
        }
        
        DB::beginTransaction();
        
        try {
            $user = Auth::guard('user')->user();
            
            // Calculate totals
            $subtotal = 0;
            $taxAmount = 0;
            $discountAmount = $request->discount_amount ?? 0;
            
            foreach ($cart as $item) {
                $itemSubtotal = $item['price'] * $item['quantity'];
                $subtotal += $itemSubtotal;
                
                // Calculate tax for this item
                $taxRate = $item['tax_rate'] ?? 7.5;
                $taxAmount += ($itemSubtotal * $taxRate / 100);
            }
            
            $totalAmount = $subtotal + $taxAmount - $discountAmount;
            $changeAmount = $request->amount_paid - $totalAmount;
            
            // Create Sale
            $sale = StoreSale::create([
                'store_customer_id' => $request->customer_id,
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'amount_paid' => $request->amount_paid,
                'change_amount' => max(0, $changeAmount),
                'payment_method' => $request->payment_method,
                'payment_status' => 'completed',
                'status' => 'completed',
                'notes' => $request->notes,
                'sale_date' => now()
            ]);
            
            // Create Sale Items
            foreach ($cart as $item) {
                $product = StoreProduct::findOrFail($item['product_id']);
                
                $itemSubtotal = $item['price'] * $item['quantity'];
                $taxRate = $item['tax_rate'] ?? 7.5;
                $itemTaxAmount = ($itemSubtotal * $taxRate / 100);
                
                StoreSaleItem::create([
                    'store_sale_id' => $sale->id,
                    'store_product_id' => $product->id,
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total_price' => $itemSubtotal + $itemTaxAmount,
                    'discount_amount' => 0,
                    'tax_amount' => $itemTaxAmount
                ]);
                
                // Update inventory if stock tracking is enabled
                if ($product->track_stock) {
                    $previousStock = $product->stock_quantity;
                    $product->decrement('stock_quantity', $item['quantity']);
                    
                    // Log inventory change
                    StoreInventoryLog::create([
                        'store_product_id' => $product->id,
                        'user_id' => $user->id,
                        'action_type' => 'sale',
                        'quantity_change' => -$item['quantity'],
                        'previous_stock' => $previousStock,
                        'new_stock' => $product->stock_quantity,
                        'reference_number' => $sale->receipt_number,
                        'action_date' => now()
                    ]);
                }
            }
            
            // Update customer stats if customer exists
            if ($sale->store_customer_id) {
                $customer = StoreCustomer::find($sale->store_customer_id);
                if ($customer) {
                    $customer->updateStats($totalAmount);
                }
            }
            
            // Clear cart
            Session::forget('store_cart');
            
            DB::commit();
            
            // Load relationships for response
            $sale->load(['saleItems.product', 'customer', 'staff']);
            
            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully',
                'sale' => $sale,
                'receipt_number' => $sale->receipt_number
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process sale: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get sale details for receipt
     */
    public function getSale($id)
    {
        $sale = StoreSale::with(['saleItems.product', 'customer', 'staff'])
            ->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'sale' => $sale
        ]);
    }
    
    /**
     * Search or create customer
     */
    public function searchCustomer(Request $request)
    {
        $search = $request->get('search', '');
        
        $customers = StoreCustomer::active()
            ->search($search)
            ->limit(10)
            ->get();
        
        return response()->json([
            'success' => true,
            'customers' => $customers
        ]);
    }
    
    /**
     * Create quick customer
     */
    public function createCustomer(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);
        
        $customer = StoreCustomer::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name ?? '',
            'phone' => $request->phone,
            'email' => $request->email,
            'is_active' => true
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully',
            'customer' => $customer
        ]);
    }
    
    /**
     * Get today's sales report
     */
    public function getDailyReport(Request $request)
    {
        $date = $request->get('date', Carbon::today());
        
        $sales = StoreSale::completed()
            ->whereDate('sale_date', $date)
            ->with(['saleItems', 'staff'])
            ->get();
        
        $totalSales = $sales->sum('total_amount');
        $totalTransactions = $sales->count();
        $totalItems = $sales->sum(function($sale) {
            return $sale->saleItems->sum('quantity');
        });
        
        // Payment method breakdown
        $paymentMethods = $sales->groupBy('payment_method')->map(function($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total_amount')
            ];
        });
        
        // Hourly sales breakdown
        $hourlySales = $sales->groupBy(function($sale) {
            return $sale->sale_date->format('H:00');
        })->map(function($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total_amount')
            ];
        })->sortKeys();
        
        return response()->json([
            'success' => true,
            'report' => [
                'date' => $date,
                'total_sales' => $totalSales,
                'total_transactions' => $totalTransactions,
                'total_items' => $totalItems,
                'average_transaction' => $totalTransactions > 0 ? $totalSales / $totalTransactions : 0,
                'payment_methods' => $paymentMethods,
                'hourly_sales' => $hourlySales
            ]
        ]);
    }
}