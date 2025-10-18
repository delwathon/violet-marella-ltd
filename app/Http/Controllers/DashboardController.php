<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Index Page.
     */
    public function index()
    {
        $user = Auth::guard('user')->user();

        // Get date ranges
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth();
        $lastMonthStart = $lastMonth->copy()->startOfMonth();
        $lastMonthEnd = $lastMonth->copy()->endOfMonth();

        // Calculate statistics
        $stats = [
            // Total Revenue (current month)
            'total_revenue' => Sale::completed()
                ->whereBetween('sale_date', [$startOfMonth, now()])
                ->sum('total_amount'),
            
            // Last month revenue for comparison
            'last_month_revenue' => Sale::completed()
                ->whereBetween('sale_date', [$lastMonthStart, $lastMonthEnd])
                ->sum('total_amount'),
            
            // Total Products
            'total_products' => Product::active()->count(),
            
            // Low Stock Products
            'low_stock_count' => Product::active()->lowStock()->count(),
            
            // Studio Sessions (current week)
            'studio_sessions' => Sale::completed()
                ->where('sale_date', '>=', Carbon::now()->startOfWeek())
                ->count(),
            
            // Last week sessions for comparison
            'last_week_sessions' => Sale::completed()
                ->whereBetween('sale_date', [
                    Carbon::now()->subWeek()->startOfWeek(),
                    Carbon::now()->subWeek()->endOfWeek()
                ])
                ->count(),
            
            // Active customers who made purchases this month
            'active_customers' => Customer::whereHas('sales', function($query) use ($startOfMonth) {
                $query->where('sale_date', '>=', $startOfMonth);
            })->count(),
            
            // Sales due today or overdue (you can adjust this based on your business logic)
            'due_today' => Sale::where('payment_status', 'pending')
                ->whereDate('sale_date', '<=', $today)
                ->count(),
        ];

        // Calculate percentage changes
        if ($stats['last_month_revenue'] > 0) {
            $stats['revenue_change'] = (($stats['total_revenue'] - $stats['last_month_revenue']) / $stats['last_month_revenue']) * 100;
        } else {
            $stats['revenue_change'] = $stats['total_revenue'] > 0 ? 100 : 0;
        }

        if ($stats['last_week_sessions'] > 0) {
            $stats['sessions_change'] = (($stats['studio_sessions'] - $stats['last_week_sessions']) / $stats['last_week_sessions']) * 100;
        } else {
            $stats['sessions_change'] = $stats['studio_sessions'] > 0 ? 100 : 0;
        }

        // Recent Activities (last 10)
        $recentActivities = $this->getRecentActivities();

        // Low Stock Products for alerts
        $lowStockProducts = Product::active()
            ->lowStock()
            ->orderBy('stock_quantity', 'asc')
            ->limit(5)
            ->get();

        // Top Selling Products (this month)
        $topProducts = Product::select(
                'products.id',
                'products.name',
                'products.price',
                'products.sku',
                'products.image',
                DB::raw('SUM(sale_items.quantity) as total_sold')
            )
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->where('sales.sale_date', '>=', $startOfMonth)
            ->groupBy('products.id', 'products.name', 'products.price', 'products.sku', 'products.image')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        // Today's Sales Summary
        $todaySales = [
            'count' => Sale::completed()->whereDate('sale_date', $today)->count(),
            'amount' => Sale::completed()->whereDate('sale_date', $today)->sum('total_amount'),
        ];

        return view('pages.dashboard', compact(
            'user',
            'stats',
            'recentActivities',
            'lowStockProducts',
            'topProducts',
            'todaySales'
        ));
    }

    /**
     * Get recent activities from various sources
     */
    private function getRecentActivities()
    {
        $activities = [];

        // Recent Sales (last 5)
        $recentSales = Sale::completed()
            ->with(['customer', 'saleItems.product'])
            ->latest('sale_date')
            ->limit(5)
            ->get();

        foreach ($recentSales as $sale) {
            $customerName = $sale->customer ? $sale->customer->full_name : 'Walk-in Customer';
            $activities[] = [
                'type' => 'sale',
                'icon' => 'fa-shopping-cart',
                'color' => 'success',
                'title' => 'New sale recorded',
                'description' => "{$customerName} - " . count($sale->saleItems) . " items for ₦" . number_format($sale->total_amount, 2),
                'time' => $sale->sale_date->diffForHumans(),
                'timestamp' => $sale->sale_date,
            ];
        }

        // Recent Inventory Logs (last 5)
        $recentInventoryLogs = InventoryLog::with(['product', 'staff'])
            ->latest('action_date')
            ->limit(5)
            ->get();

        foreach ($recentInventoryLogs as $log) {
            $icon = 'fa-warehouse';
            $color = 'info';
            $title = $log->action_description;

            if ($log->action_type == 'damage' || $log->action_type == 'expiry') {
                $color = 'danger';
                $icon = 'fa-exclamation-triangle';
            } elseif ($log->action_type == 'purchase') {
                $color = 'primary';
                $icon = 'fa-box';
            }

            $activities[] = [
                'type' => 'inventory',
                'icon' => $icon,
                'color' => $color,
                'title' => $title,
                'description' => "{$log->product->name} - " . abs($log->quantity_change) . " units",
                'time' => $log->action_date->diffForHumans(),
                'timestamp' => $log->action_date,
            ];
        }

        // Low Stock Alerts (current)
        $lowStockProducts = Product::active()
            ->lowStock()
            ->limit(3)
            ->get();

        foreach ($lowStockProducts as $product) {
            $activities[] = [
                'type' => 'alert',
                'icon' => 'fa-exclamation-triangle',
                'color' => 'warning',
                'title' => 'Low stock alert',
                'description' => "{$product->name} - Only {$product->stock_quantity} items remaining",
                'time' => 'Now',
                'timestamp' => now(),
            ];
        }

        // Sort all activities by timestamp (most recent first)
        usort($activities, function($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        // Return only the 10 most recent
        return array_slice($activities, 0, 10);
    }
}