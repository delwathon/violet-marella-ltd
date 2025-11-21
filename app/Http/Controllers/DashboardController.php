<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\StoreProduct;
use App\Models\StoreSale;
use App\Models\StoreCustomer;
use App\Models\StudioSession;
use App\Models\StudioCustomer;
use App\Models\PropRental;
use App\Models\RentalCustomer;
use App\Models\Prop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * All Business Overview Dashboard
     */
    public function index()
    {
        $user = Auth::guard('user')->user();

        // Get date ranges
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $lastMonth = Carbon::now()->subMonth();
        $lastMonthStart = $lastMonth->copy()->startOfMonth();
        $lastMonthEnd = $lastMonth->copy()->endOfMonth();
        $startOfWeek = Carbon::now()->startOfWeek();
        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();

        // ==================== AGGREGATED STATISTICS ====================
        
        // Total Revenue (All Businesses This Month)
        $loungeRevenue = Sale::completed()
            ->whereBetween('sale_date', [$startOfMonth, now()])
            ->sum('total_amount');
            
        $storeRevenue = StoreSale::completed()
            ->whereBetween('sale_date', [$startOfMonth, now()])
            ->sum('total_amount');
            
        $studioRevenue = StudioSession::completed()
            ->whereBetween('check_out_time', [$startOfMonth, now()])
            ->where('payment_status', 'paid')
            ->sum('total_amount');
            
        $propRevenue = PropRental::completed()
            ->whereBetween('created_at', [$startOfMonth, now()])
            ->sum('total_amount');
        
        $totalRevenue = $loungeRevenue + $storeRevenue + $studioRevenue + $propRevenue;
        
        // Last Month Revenue for comparison
        $lastMonthLoungeRevenue = Sale::completed()
            ->whereBetween('sale_date', [$lastMonthStart, $lastMonthEnd])
            ->sum('total_amount');
            
        $lastMonthStoreRevenue = StoreSale::completed()
            ->whereBetween('sale_date', [$lastMonthStart, $lastMonthEnd])
            ->sum('total_amount');
            
        $lastMonthStudioRevenue = StudioSession::completed()
            ->whereBetween('check_out_time', [$lastMonthStart, $lastMonthEnd])
            ->where('payment_status', 'paid')
            ->sum('total_amount');
            
        $lastMonthPropRevenue = PropRental::completed()
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('total_amount');
            
        $lastMonthTotalRevenue = $lastMonthLoungeRevenue + $lastMonthStoreRevenue + 
                                 $lastMonthStudioRevenue + $lastMonthPropRevenue;
        
        // Revenue Change Percentage
        if ($lastMonthTotalRevenue > 0) {
            $revenueChange = (($totalRevenue - $lastMonthTotalRevenue) / $lastMonthTotalRevenue) * 100;
        } else {
            $revenueChange = $totalRevenue > 0 ? 100 : 0;
        }
        
        // Total Sales/Transactions This Week
        $loungeSales = Sale::completed()
            ->where('sale_date', '>=', $startOfWeek)
            ->count();
            
        $storeSales = StoreSale::completed()
            ->where('sale_date', '>=', $startOfWeek)
            ->count();
            
        $studioSessions = StudioSession::completed()
            ->where('check_out_time', '>=', $startOfWeek)
            ->count();
            
        $propRentals = PropRental::where('created_at', '>=', $startOfWeek)
            ->count();
        
        $totalTransactions = $loungeSales + $storeSales + $studioSessions + $propRentals;
        
        // Last Week Transactions for comparison
        $lastWeekLoungeSales = Sale::completed()
            ->whereBetween('sale_date', [$lastWeekStart, $lastWeekEnd])
            ->count();
            
        $lastWeekStoreSales = StoreSale::completed()
            ->whereBetween('sale_date', [$lastWeekStart, $lastWeekEnd])
            ->count();
            
        $lastWeekStudioSessions = StudioSession::completed()
            ->whereBetween('check_out_time', [$lastWeekStart, $lastWeekEnd])
            ->count();
            
        $lastWeekPropRentals = PropRental::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
            ->count();
            
        $lastWeekTotalTransactions = $lastWeekLoungeSales + $lastWeekStoreSales + 
                                     $lastWeekStudioSessions + $lastWeekPropRentals;
        
        // Transaction Change Percentage
        if ($lastWeekTotalTransactions > 0) {
            $transactionChange = (($totalTransactions - $lastWeekTotalTransactions) / $lastWeekTotalTransactions) * 100;
        } else {
            $transactionChange = $totalTransactions > 0 ? 100 : 0;
        }
        
        // Total Products Across All Businesses
        $loungeProducts = Product::active()->count();
        $storeProducts = StoreProduct::active()->count();
        $totalProps = Prop::count();
        $totalProducts = $loungeProducts + $storeProducts + $totalProps;
        
        // Low Stock Count
        $loungeLowStock = Product::active()->lowStock()->count();
        $storeLowStock = StoreProduct::lowStock()->count();
        $propsMaintenance = Prop::where('status', 'maintenance')->count();
        $totalLowStock = $loungeLowStock + $storeLowStock + $propsMaintenance;
        
        // Total Customers Across All Businesses
        $loungeCustomers = Customer::count();
        $storeCustomers = StoreCustomer::count();
        $studioCustomers = StudioCustomer::count();
        $rentalCustomers = RentalCustomer::count();
        $totalCustomers = $loungeCustomers + $storeCustomers + $studioCustomers + $rentalCustomers;
        
        // Active Customers (who made purchases this month)
        $loungeActiveCustomers = Customer::whereHas('sales', function($query) use ($startOfMonth) {
            $query->where('sale_date', '>=', $startOfMonth);
        })->count();
        
        $storeActiveCustomers = StoreCustomer::whereHas('sales', function($query) use ($startOfMonth) {
            $query->where('sale_date', '>=', $startOfMonth);
        })->count();
        
        $studioActiveCustomers = StudioCustomer::whereHas('sessions', function($query) use ($startOfMonth) {
            $query->where('check_in_time', '>=', $startOfMonth);
        })->count();
        
        $rentalActiveCustomers = RentalCustomer::whereHas('rentals', function($query) use ($startOfMonth) {
            $query->where('created_at', '>=', $startOfMonth);
        })->count();
        
        $activeCustomers = $loungeActiveCustomers + $storeActiveCustomers + 
                          $studioActiveCustomers + $rentalActiveCustomers;
        
        // Pending Payments
        $loungePending = Sale::where('payment_status', 'pending')->count();
        $storePending = StoreSale::where('payment_status', 'pending')->count();
        $studioPending = StudioSession::where('payment_status', 'pending')->count();
        $propOverdue = PropRental::overdue()->count();
        $totalPending = $loungePending + $storePending + $studioPending + $propOverdue;
        
        // Compile Statistics
        $stats = [
            'total_revenue' => $totalRevenue,
            'revenue_change' => $revenueChange,
            'total_transactions' => $totalTransactions,
            'transaction_change' => $transactionChange,
            'total_products' => $totalProducts,
            'total_low_stock' => $totalLowStock,
            'total_customers' => $totalCustomers,
            'active_customers' => $activeCustomers,
            'total_pending' => $totalPending,
        ];
        
        // ==================== BUSINESS MODULE DETAILS ====================
        
        $businessModules = [
            'lounge' => [
                'name' => 'Mini Lounge',
                'icon' => 'shopping-cart',
                'color' => 'success',
                'revenue' => $loungeRevenue,
                'transactions' => $loungeSales,
                'products' => $loungeProducts,
                'low_stock' => $loungeLowStock,
                'customers' => $loungeActiveCustomers,
                'pending' => $loungePending,
            ],
            'gift_store' => [
                'name' => 'Gift Store',
                'icon' => 'gift',
                'color' => 'danger',
                'revenue' => $storeRevenue,
                'transactions' => $storeSales,
                'products' => $storeProducts,
                'low_stock' => $storeLowStock,
                'customers' => $storeActiveCustomers,
                'pending' => $storePending,
            ],
            'photo_studio' => [
                'name' => 'Photo Studio',
                'icon' => 'camera',
                'color' => 'primary',
                'revenue' => $studioRevenue,
                'transactions' => $studioSessions,
                'active_sessions' => StudioSession::active()->count(),
                'customers' => $studioActiveCustomers,
                'pending' => $studioPending,
            ],
            'prop_rental' => [
                'name' => 'Prop Rental',
                'icon' => 'guitar',
                'color' => 'warning',
                'revenue' => $propRevenue,
                'transactions' => $propRentals,
                'active_rentals' => PropRental::active()->count(),
                'props' => $totalProps,
                'maintenance' => $propsMaintenance,
                'overdue' => $propOverdue,
            ],
        ];
        
        // ==================== REVENUE TREND (LAST 7 DAYS) ====================
        
        $revenueTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateString = $date->toDateString();
            
            $dayLoungeRevenue = Sale::completed()
                ->whereDate('sale_date', $dateString)
                ->sum('total_amount');
                
            $dayStoreRevenue = StoreSale::completed()
                ->whereDate('sale_date', $dateString)
                ->sum('total_amount');
                
            $dayStudioRevenue = StudioSession::completed()
                ->whereDate('check_out_time', $dateString)
                ->where('payment_status', 'paid')
                ->sum('total_amount');
                
            $dayPropRevenue = PropRental::completed()
                ->whereDate('created_at', $dateString)
                ->sum('total_amount');
            
            $revenueTrend[] = [
                'date' => $date->format('M d'),
                'lounge' => $dayLoungeRevenue,
                'store' => $dayStoreRevenue,
                'studio' => $dayStudioRevenue,
                'props' => $dayPropRevenue,
                'total' => $dayLoungeRevenue + $dayStoreRevenue + $dayStudioRevenue + $dayPropRevenue,
            ];
        }
        
        // ==================== TODAY'S SUMMARY ====================
        
        $todaySummary = [
            'lounge' => [
                'revenue' => Sale::completed()->whereDate('sale_date', $today)->sum('total_amount'),
                'transactions' => Sale::completed()->whereDate('sale_date', $today)->count(),
            ],
            'store' => [
                'revenue' => StoreSale::completed()->whereDate('sale_date', $today)->sum('total_amount'),
                'transactions' => StoreSale::completed()->whereDate('sale_date', $today)->count(),
            ],
            'studio' => [
                'revenue' => StudioSession::completed()
                    ->whereDate('check_out_time', $today)
                    ->where('payment_status', 'paid')
                    ->sum('total_amount'),
                'sessions' => StudioSession::whereDate('check_in_time', $today)->count(),
            ],
            'props' => [
                'revenue' => PropRental::whereDate('created_at', $today)->sum('total_amount'),
                'rentals' => PropRental::whereDate('created_at', $today)->count(),
            ],
        ];
        
        // ==================== RECENT ACTIVITIES ====================
        
        $recentActivities = $this->getRecentActivities();
        
        return view('pages.dashboard', compact(
            'user',
            'stats',
            'businessModules',
            'revenueTrend',
            'todaySummary',
            'recentActivities'
        ));
    }
    
    /**
     * Get recent activities across all business units
     */
    private function getRecentActivities()
    {
        $activities = [];
        
        // Recent Lounge Sales
        $recentLoungeSales = Sale::with('customer')
            ->latest()
            ->limit(3)
            ->get()
            ->map(function($sale) {
                return [
                    'type' => 'lounge_sale',
                    'icon' => 'shopping-cart',
                    'color' => 'success',
                    'title' => 'Lounge Sale',
                    'description' => 'Sale to ' . ($sale->customer->full_name ?? 'Walk-in Customer'),
                    'amount' => $sale->total_amount,
                    'time' => $sale->sale_date,
                ];
            });
        
        // Recent Store Sales
        $recentStoreSales = StoreSale::with('customer')
            ->latest()
            ->limit(3)
            ->get()
            ->map(function($sale) {
                return [
                    'type' => 'store_sale',
                    'icon' => 'gift',
                    'color' => 'danger',
                    'title' => 'Gift Store Sale',
                    'description' => 'Sale to ' . ($sale->customer->name ?? 'Walk-in Customer'),
                    'amount' => $sale->total_amount,
                    'time' => $sale->sale_date,
                ];
            });
        
        // Recent Studio Sessions  
        $recentSessions = StudioSession::with(['customer', 'studio'])
            ->latest()
            ->limit(3)
            ->get()
            ->map(function($session) {
                return [
                    'type' => 'studio_session',
                    'icon' => 'camera',
                    'color' => 'primary',
                    'title' => 'Studio Session',
                    'description' => $session->customer->name . ' - ' . $session->studio->name,
                    'amount' => $session->total_amount,
                    'time' => $session->check_in_time,
                ];
            });
        
        // Recent Prop Rentals
        $recentRentals = PropRental::with(['customer', 'prop'])
            ->latest()
            ->limit(3)
            ->get()
            ->map(function($rental) {
                return [
                    'type' => 'prop_rental',
                    'icon' => 'guitar',
                    'color' => 'warning',
                    'title' => 'Prop Rental',
                    'description' => $rental->customer->name . ' - ' . $rental->prop->name,
                    'amount' => $rental->total_amount,
                    'time' => $rental->created_at,
                ];
            });
        
        // Merge all activities
        $activities = collect()
            ->merge($recentLoungeSales)
            ->merge($recentStoreSales)
            ->merge($recentSessions)
            ->merge($recentRentals)
            ->sortByDesc('time')
            ->take(10)
            ->values();
        
        return $activities;
    }
}