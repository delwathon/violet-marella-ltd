<?php

namespace App\Http\Controllers\AnireCraftStore;

use App\Http\Controllers\Controller;
use App\Models\StoreProduct;
use App\Models\StoreCategory;
use App\Models\StoreInventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StoreInventoryController extends Controller
{
    /**
     * Display inventory overview
     */
    public function index(Request $request)
    {
        $user = Auth::guard('user')->user();
        
        $query = StoreProduct::with('category');
        
        // Search
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }
        
        // Category filter
        if ($request->has('category_id') && $request->category_id) {
            $query->where('store_category_id', $request->category_id);
        }
        
        // Stock status filter
        if ($request->has('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->lowStock();
            } elseif ($request->stock_status === 'out') {
                $query->where('stock_quantity', '<=', 0);
            } elseif ($request->stock_status === 'good') {
                $query->whereRaw('stock_quantity > minimum_stock_level');
            }
        }
        
        $products = $query->orderBy('name')->paginate(20);
        $categories = StoreCategory::active()->ordered()->get();
        
        // Statistics
        $totalProducts = StoreProduct::active()->count();
        $totalStockValue = StoreProduct::active()->sum(DB::raw('stock_quantity * cost_price'));
        $lowStockCount = StoreProduct::lowStock()->count();
        $outOfStockCount = StoreProduct::where('stock_quantity', '<=', 0)->count();
        
        // Recent inventory activities
        $recentActivities = StoreInventoryLog::with(['product', 'staff'])
            ->latest('action_date')
            ->limit(10)
            ->get();
        
        return view('pages.anire-craft-store.inventory.index', compact(
            'user',
            'products',
            'categories',
            'totalProducts',
            'totalStockValue',
            'lowStockCount',
            'outOfStockCount',
            'recentActivities'
        ));
    }
    
    /**
     * Show inventory logs
     */
    public function logs(Request $request)
    {
        $user = Auth::guard('user')->user();
        
        $query = StoreInventoryLog::with(['product', 'staff']);
        
        // Date range filter
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('action_date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('action_date', '<=', $request->end_date);
        }
        
        // Action type filter
        if ($request->has('action_type') && $request->action_type) {
            $query->where('action_type', $request->action_type);
        }
        
        // Product filter
        if ($request->has('product_id') && $request->product_id) {
            $query->where('store_product_id', $request->product_id);
        }
        
        $logs = $query->latest('action_date')->paginate(20);
        $products = StoreProduct::active()->orderBy('name')->get();
        
        return view('pages.anire-craft-store.inventory.logs', compact('user', 'logs', 'products'));
    }
    
    /**
     * Show stock adjustment form
     */
    public function adjust($id)
    {
        $user = Auth::guard('user')->user();
        $product = StoreProduct::with('category')->findOrFail($id);
        
        return view('pages.anire-craft-store.inventory.adjust', compact('user', 'product'));
    }
    
    /**
     * Process stock adjustment
     */
    public function processAdjustment(Request $request, $id)
    {
        $request->validate([
            'quantity_change' => 'required|integer|not_in:0',
            'action_type' => 'required|in:purchase,adjustment,damage,expiry,return',
            'reason' => 'nullable|string|max:255',
            'unit_cost' => 'nullable|numeric|min:0',
        ]);
        
        $product = StoreProduct::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            $previousStock = $product->stock_quantity;
            $newStock = max(0, $previousStock + $request->quantity_change);
            
            // Update product stock
            $product->update(['stock_quantity' => $newStock]);
            
            // Log inventory change
            StoreInventoryLog::create([
                'store_product_id' => $product->id,
                'user_id' => Auth::guard('user')->id(),
                'action_type' => $request->action_type,
                'quantity_change' => $request->quantity_change,
                'previous_stock' => $previousStock,
                'new_stock' => $newStock,
                'unit_cost' => $request->unit_cost,
                'reason' => $request->reason,
                'action_date' => now(),
            ]);
            
            DB::commit();
            
            return redirect()->route('anire-craft-store.inventory.index')
                ->with('success', 'Stock adjusted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Failed to adjust stock: ' . $e->getMessage());
        }
    }
    
    /**
     * Show low stock report
     */
    public function lowStock()
    {
        $user = Auth::guard('user')->user();
        
        $products = StoreProduct::with('category')
            ->lowStock()
            ->orderBy('stock_quantity', 'asc')
            ->paginate(20);
        
        return view('pages.anire-craft-store.inventory.low-stock', compact('user', 'products'));
    }
    
    /**
     * Export inventory to CSV
     */
    public function export()
    {
        $products = StoreProduct::with('category')->get();
        
        $filename = 'inventory_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'SKU', 'Name', 'Category', 'Stock Quantity', 'Min Level',
                'Max Level', 'Unit', 'Cost Price', 'Value', 'Status'
            ]);
            
            // Data
            foreach ($products as $product) {
                $value = $product->stock_quantity * ($product->cost_price ?? 0);
                $status = $product->stock_quantity <= 0 ? 'Out of Stock' : 
                         ($product->stock_quantity <= $product->minimum_stock_level ? 'Low Stock' : 'Good');
                
                fputcsv($file, [
                    $product->sku,
                    $product->name,
                    $product->category->name ?? 'N/A',
                    $product->stock_quantity,
                    $product->minimum_stock_level,
                    $product->maximum_stock_level,
                    $product->unit,
                    $product->cost_price,
                    $value,
                    $status
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Get inventory statistics (AJAX)
     */
    public function statistics()
    {
        $totalProducts = StoreProduct::active()->count();
        $totalStockValue = StoreProduct::active()->sum(DB::raw('stock_quantity * cost_price'));
        $lowStockCount = StoreProduct::lowStock()->count();
        $outOfStockCount = StoreProduct::where('stock_quantity', '<=', 0)->count();
        
        // Stock by category
        $stockByCategory = StoreProduct::select(
                'store_categories.name as category_name',
                DB::raw('COUNT(store_products.id) as product_count'),
                DB::raw('SUM(store_products.stock_quantity) as total_quantity'),
                DB::raw('SUM(store_products.stock_quantity * store_products.cost_price) as total_value')
            )
            ->join('store_categories', 'store_products.store_category_id', '=', 'store_categories.id')
            ->where('store_products.is_active', true)
            ->groupBy('store_categories.id', 'store_categories.name')
            ->get();
        
        // Recent stock movements
        $recentMovements = StoreInventoryLog::select(
                'action_type',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(ABS(quantity_change)) as total_quantity')
            )
            ->where('action_date', '>=', Carbon::now()->subDays(30))
            ->groupBy('action_type')
            ->get();
        
        return response()->json([
            'success' => true,
            'statistics' => [
                'total_products' => $totalProducts,
                'total_stock_value' => $totalStockValue,
                'low_stock_count' => $lowStockCount,
                'out_of_stock_count' => $outOfStockCount,
                'stock_by_category' => $stockByCategory,
                'recent_movements' => $recentMovements
            ]
        ]);
    }
}