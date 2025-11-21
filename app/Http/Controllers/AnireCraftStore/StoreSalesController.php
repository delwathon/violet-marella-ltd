<?php

namespace App\Http\Controllers\AnireCraftStore;

use App\Http\Controllers\Controller;
use App\Models\StoreSale;
use App\Models\StoreCustomer;
use App\Models\StoreProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StoreSalesController extends Controller
{
    /**
     * Display a listing of sales
     */
    public function index(Request $request)
    {
        $user = Auth::guard('user')->user();
        
        $query = StoreSale::with(['customer', 'staff', 'saleItems']);
        
        // Date range filter
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('sale_date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('sale_date', '<=', $request->end_date);
        }
        
        // Search by receipt number or customer
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('receipt_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('customer', function($customerQuery) use ($request) {
                      $customerQuery->where('first_name', 'like', '%' . $request->search . '%')
                                   ->orWhere('last_name', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Payment method filter
        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }
        
        $sales = $query->latest('sale_date')->paginate(20);
        
        // Statistics
        $totalSales = StoreSale::completed()->sum('total_amount');
        $todaySales = StoreSale::completed()->whereDate('sale_date', Carbon::today())->sum('total_amount');
        $thisMonthSales = StoreSale::completed()
            ->whereBetween('sale_date', [Carbon::now()->startOfMonth(), Carbon::now()])
            ->sum('total_amount');
        $totalTransactions = StoreSale::completed()->count();
        
        return view('pages.anire-craft-store.sales.index', compact(
            'user',
            'sales',
            'totalSales',
            'todaySales',
            'thisMonthSales',
            'totalTransactions'
        ));
    }
    
    /**
     * Display today's sales
     */
    public function today(Request $request)
    {
        $user = Auth::guard('user')->user();
        
        $query = StoreSale::with(['customer', 'staff', 'saleItems'])
            ->whereDate('sale_date', Carbon::today());
        
        // Search by receipt number or customer
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('receipt_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('customer', function($customerQuery) use ($request) {
                      $customerQuery->where('first_name', 'like', '%' . $request->search . '%')
                                   ->orWhere('last_name', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        $sales = $query->latest('sale_date')->paginate(20);
        
        // Today's statistics
        $todayTotal = StoreSale::completed()->whereDate('sale_date', Carbon::today())->sum('total_amount');
        $todayCount = StoreSale::completed()->whereDate('sale_date', Carbon::today())->count();
        $todayItems = DB::table('store_sale_items')
            ->join('store_sales', 'store_sale_items.store_sale_id', '=', 'store_sales.id')
            ->whereDate('store_sales.sale_date', Carbon::today())
            ->where('store_sales.status', 'completed')
            ->sum('store_sale_items.quantity');
        $todayAverage = $todayCount > 0 ? $todayTotal / $todayCount : 0;
        
        // Hourly breakdown
        $hourlySales = StoreSale::completed()
            ->whereDate('sale_date', Carbon::today())
            ->select(
                DB::raw('HOUR(sale_date) as hour'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
        
        return view('pages.anire-craft-store.sales.today', compact(
            'user',
            'sales',
            'todayTotal',
            'todayCount',
            'todayItems',
            'todayAverage',
            'hourlySales'
        ));
    }
    
    /**
     * Display the specified sale
     */
    public function show($id)
    {
        $user = Auth::guard('user')->user();
        $sale = StoreSale::with(['customer', 'staff', 'saleItems.product'])->findOrFail($id);
        
        return view('pages.anire-craft-store.sales.show', compact('user', 'sale'));
    }
    
    /**
     * Show the form for creating a new sale
     */
    public function create()
    {
        // Redirect to POS system
        return redirect()->route('anire-craft-store.index');
    }
    
    /**
     * Export sales to CSV
     */
    public function export(Request $request)
    {
        $query = StoreSale::with(['customer', 'staff']);
        
        // Apply filters
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('sale_date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('sale_date', '<=', $request->end_date);
        }
        
        $sales = $query->get();
        
        $filename = 'sales_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($sales) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Receipt #', 'Date', 'Customer', 'Staff', 'Items',
                'Subtotal', 'Tax', 'Discount', 'Total', 'Payment Method', 'Status'
            ]);
            
            // Data
            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->receipt_number,
                    $sale->sale_date->format('Y-m-d H:i:s'),
                    $sale->customer ? $sale->customer->full_name : 'Walk-in',
                    $sale->staff ? $sale->staff->full_name : 'N/A',
                    $sale->saleItems->count(),
                    $sale->subtotal,
                    $sale->tax_amount,
                    $sale->discount_amount,
                    $sale->total_amount,
                    $sale->payment_method,
                    $sale->status
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Get sale statistics (AJAX)
     */
    public function statistics(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());
        
        $sales = StoreSale::completed()
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->get();
        
        $totalSales = $sales->sum('total_amount');
        $totalTransactions = $sales->count();
        $totalItems = $sales->sum('total_items');
        $averageTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;
        
        // Payment method breakdown
        $paymentMethods = $sales->groupBy('payment_method')->map(function($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total_amount')
            ];
        });
        
        // Daily sales
        $dailySales = $sales->groupBy(function($sale) {
            return $sale->sale_date->format('Y-m-d');
        })->map(function($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total_amount')
            ];
        })->sortKeys();
        
        return response()->json([
            'success' => true,
            'statistics' => [
                'total_sales' => $totalSales,
                'total_transactions' => $totalTransactions,
                'total_items' => $totalItems,
                'average_transaction' => $averageTransaction,
                'payment_methods' => $paymentMethods,
                'daily_sales' => $dailySales
            ]
        ]);
    }

    /**
     * Display receipt for printing
     */
    public function receipt($id)
    {
        $user = Auth::guard('user')->user();
        $sale = StoreSale::with(['customer', 'staff', 'saleItems.product'])->findOrFail($id);
        
        return view('pages.anire-craft-store.sales.receipt', compact('user', 'sale'));
    }
}