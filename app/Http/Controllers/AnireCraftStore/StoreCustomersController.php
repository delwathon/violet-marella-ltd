<?php

namespace App\Http\Controllers\AnireCraftStore;

use App\Http\Controllers\Controller;
use App\Models\StoreCustomer;
use App\Models\StoreSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreCustomersController extends Controller
{
    /**
     * Display a listing of customers
     */
    public function index(Request $request)
    {
        $user = Auth::guard('user')->user();
        
        $query = StoreCustomer::query();
        
        // Search
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Customer type filter
        if ($request->has('customer_type') && $request->customer_type) {
            $query->byType($request->customer_type);
        }
        
        // Status filter
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        $customers = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Statistics
        $totalCustomers = StoreCustomer::count();
        $activeCustomers = StoreCustomer::active()->count();
        $totalSpent = StoreCustomer::sum('total_spent');
        $avgOrderValue = StoreCustomer::where('total_orders', '>', 0)->avg('total_spent');
        
        return view('pages.anire-craft-store.customers.index', compact(
            'user',
            'customers',
            'totalCustomers',
            'activeCustomers',
            'totalSpent',
            'avgOrderValue'
        ));
    }
    
    /**
     * Show the form for creating a new customer
     */
    public function create()
    {
        $user = Auth::guard('user')->user();
        
        return view('pages.anire-craft-store.customers.create', compact('user'));
    }
    
    /**
     * Store a newly created customer
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:store_customers,email',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'customer_type' => 'required|in:walk-in,regular,wholesale,staff',
            'is_active' => 'in:on,NULL',
            'notes' => 'nullable|string',
        ]);
        
        try {
            $data = $request->all();
            $data['is_active'] = $request->has('is_active');
            $data['loyalty_points'] = 0;
            $data['total_spent'] = 0;
            $data['total_orders'] = 0;
            
            $customer = StoreCustomer::create($data);
            
            return redirect()->route('anire-craft-store.customers.index')
                ->with('success', 'Customer created successfully.');
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create customer: ' . $e->getMessage());
        }
    }
    
    /**
     * Display the specified customer
     */
    public function show($id)
    {
        $user = Auth::guard('user')->user();
        $customer = StoreCustomer::with('sales')->findOrFail($id);
        
        // Get customer sales
        $sales = $customer->sales()
            ->with('saleItems.product')
            ->latest()
            ->paginate(10);
        
        // Purchase statistics
        $totalPurchases = $customer->total_orders;
        $totalSpent = $customer->total_spent;
        $avgOrderValue = $customer->average_order_value;
        $lastPurchase = $customer->last_purchase_date;
        
        // Top products purchased
        $topProducts = DB::table('store_sale_items')
            ->join('store_sales', 'store_sale_items.store_sale_id', '=', 'store_sales.id')
            ->join('store_products', 'store_sale_items.store_product_id', '=', 'store_products.id')
            ->where('store_sales.store_customer_id', $id)
            ->select(
                'store_products.name',
                DB::raw('SUM(store_sale_items.quantity) as total_quantity'),
                DB::raw('SUM(store_sale_items.total_price) as total_spent')
            )
            ->groupBy('store_products.id', 'store_products.name')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();
        
        return view('pages.anire-craft-store.customers.show', compact(
            'user',
            'customer',
            'sales',
            'totalPurchases',
            'totalSpent',
            'avgOrderValue',
            'lastPurchase',
            'topProducts'
        ));
    }
    
    /**
     * Show the form for editing the specified customer
     */
    public function edit($id)
    {
        $user = Auth::guard('user')->user();
        $customer = StoreCustomer::findOrFail($id);
        
        return view('pages.anire-craft-store.customers.edit', compact('user', 'customer'));
    }
    
    /**
     * Update the specified customer
     */
    public function update(Request $request, $id)
    {
        $customer = StoreCustomer::findOrFail($id);
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:store_customers,email,' . $id,
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'customer_type' => 'required|in:walk-in,regular,wholesale,staff',
            'is_active' => 'in:on,NULL',
            'notes' => 'nullable|string'
        ]);
        
        try {
            $data = $request->all();
            $data['is_active'] = $request->has('is_active');
            
            $customer->update($data);
            
            return redirect()->route('anire-craft-store.customers.index')
                ->with('success', 'Customer updated successfully.');
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update customer: ' . $e->getMessage());
        }
    }
    
    /**
     * Remove the specified customer
     */
    public function destroy($id)
    {
        $customer = StoreCustomer::findOrFail($id);
        
        try {
            // Check if customer has sales
            if ($customer->sales()->count() > 0) {
                return back()->with('error', 'Cannot delete customer with existing sales. Consider deactivating instead.');
            }
            
            $customer->delete();
            
            return redirect()->route('anire-craft-store.customers.index')
                ->with('success', 'Customer deleted successfully.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete customer: ' . $e->getMessage());
        }
    }
    
    /**
     * Search customers (AJAX)
     */
    public function search(Request $request)
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
     * Quick create customer (AJAX)
     */
    public function quickStore(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|unique:store_customers,email',
            'customer_type' => 'nullable|in:walk-in,regular,wholesale,staff',
        ]);
        
        try {
            $customer = StoreCustomer::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name ?? '',
                'phone' => $request->phone,
                'email' => $request->email,
                'customer_type' => $request->customer_type ?? 'walk-in',
                'is_active' => true,
                'loyalty_points' => 0,
                'total_spent' => 0,
                'total_orders' => 0,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'customer' => $customer
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create customer: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get customer details (AJAX)
     */
    public function getCustomer($id)
    {
        $customer = StoreCustomer::with('sales')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'customer' => $customer
        ]);
    }
    
    /**
     * Adjust loyalty points (AJAX)
     */
    public function adjustLoyaltyPoints(Request $request, $id)
    {
        $request->validate([
            'points' => 'required|integer',
            'action' => 'required|in:add,deduct',
        ]);
        
        $customer = StoreCustomer::findOrFail($id);
        
        try {
            if ($request->action === 'add') {
                $customer->increment('loyalty_points', $request->points);
            } else {
                $customer->decrement('loyalty_points', $request->points);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Loyalty points adjusted successfully',
                'customer' => $customer->fresh()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to adjust loyalty points: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Export customers to CSV
     */
    public function export()
    {
        $customers = StoreCustomer::all();
        
        $filename = 'customers_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Name', 'Email', 'Phone', 'Type', 'Total Orders',
                'Total Spent', 'Loyalty Points', 'Last Purchase', 'Status'
            ]);
            
            // Data
            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->full_name,
                    $customer->email,
                    $customer->phone,
                    $customer->customer_type,
                    $customer->total_orders,
                    $customer->total_spent,
                    $customer->loyalty_points,
                    $customer->last_purchase_date?->format('Y-m-d'),
                    $customer->is_active ? 'Active' : 'Inactive'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Get customer statistics (AJAX)
     */
    public function getStatistics($id)
    {
        $customer = StoreCustomer::findOrFail($id);
        
        // Monthly purchases
        $monthlyPurchases = DB::table('store_sales')
            ->where('store_customer_id', $id)
            ->where('status', 'completed')
            ->whereYear('sale_date', date('Y'))
            ->select(
                DB::raw('MONTH(sale_date) as month'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_spent')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        return response()->json([
            'success' => true,
            'statistics' => [
                'total_orders' => $customer->total_orders,
                'total_spent' => $customer->total_spent,
                'average_order_value' => $customer->average_order_value,
                'loyalty_points' => $customer->loyalty_points,
                'monthly_purchases' => $monthlyPurchases
            ]
        ]);
    }
}