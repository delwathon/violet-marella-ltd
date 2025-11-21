<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\Product;
use App\Models\StoreSale;
use App\Models\StoreCustomer;
use App\Models\StoreProduct;
use App\Models\StudioSession;
use App\Models\StudioCustomer;
use App\Models\PropRental;
use App\Models\RentalCustomer;
use App\Models\Prop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * All Business Reports Dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::guard('user')->user();
        
        // Get filter parameters
        $businessUnit = $request->get('business_unit', 'all');
        $dateRange = $request->get('date_range', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Calculate date ranges
        $dates = $this->getDateRange($dateRange, $startDate, $endDate);
        $previousDates = $this->getPreviousDateRange($dateRange, $startDate, $endDate);
        
        // Get comprehensive statistics
        $statistics = $this->calculateStatistics($businessUnit, $dates, $previousDates);
        
        // Get revenue trend data
        $revenueTrend = $this->getRevenueTrend($businessUnit, $dates);
        
        // Get business unit comparison
        $businessComparison = $this->getBusinessComparison($dates);
        
        // Get top performing products/services
        $topPerformers = $this->getTopPerformers($businessUnit, $dates);
        
        // Get customer insights
        $customerInsights = $this->getCustomerInsights($businessUnit, $dates);
        
        // Get payment status breakdown
        $paymentStatus = $this->getPaymentStatusBreakdown($businessUnit, $dates);
        
        // Get hourly/daily trend
        $timeTrend = $this->getTimeTrend($businessUnit, $dates);
        
        return view('pages.reports', compact(
            'user',
            'businessUnit',
            'dateRange',
            'dates',
            'statistics',
            'revenueTrend',
            'businessComparison',
            'topPerformers',
            'customerInsights',
            'paymentStatus',
            'timeTrend'
        ));
    }
    
    // ... [All the helper methods remain the same until getTopPerformers] ...
    
    /**
     * Get date range based on selection
     */
    private function getDateRange($range, $startDate = null, $endDate = null)
    {
        if ($range === 'custom' && $startDate && $endDate) {
            return [
                'start' => Carbon::parse($startDate),
                'end' => Carbon::parse($endDate),
            ];
        }
        
        switch ($range) {
            case 'today':
                return [
                    'start' => Carbon::today(),
                    'end' => Carbon::now(),
                ];
            case 'week':
                return [
                    'start' => Carbon::now()->startOfWeek(),
                    'end' => Carbon::now(),
                ];
            case 'month':
                return [
                    'start' => Carbon::now()->startOfMonth(),
                    'end' => Carbon::now(),
                ];
            case 'quarter':
                return [
                    'start' => Carbon::now()->startOfQuarter(),
                    'end' => Carbon::now(),
                ];
            case 'year':
                return [
                    'start' => Carbon::now()->startOfYear(),
                    'end' => Carbon::now(),
                ];
            default:
                return [
                    'start' => Carbon::now()->startOfMonth(),
                    'end' => Carbon::now(),
                ];
        }
    }
    
    /**
     * Get previous date range for comparison
     */
    private function getPreviousDateRange($range, $startDate = null, $endDate = null)
    {
        $current = $this->getDateRange($range, $startDate, $endDate);
        $diff = $current['start']->diffInDays($current['end']);
        
        return [
            'start' => $current['start']->copy()->subDays($diff + 1),
            'end' => $current['start']->copy()->subDay(),
        ];
    }
    
    /**
     * Calculate comprehensive statistics
     */
    private function calculateStatistics($businessUnit, $dates, $previousDates)
    {
        $stats = [
            'current' => $this->calculatePeriodStats($businessUnit, $dates),
            'previous' => $this->calculatePeriodStats($businessUnit, $previousDates),
        ];
        
        // Calculate changes
        $stats['changes'] = [
            'revenue' => $this->calculatePercentageChange(
                $stats['current']['revenue'],
                $stats['previous']['revenue']
            ),
            'transactions' => $this->calculatePercentageChange(
                $stats['current']['transactions'],
                $stats['previous']['transactions']
            ),
            'customers' => $this->calculatePercentageChange(
                $stats['current']['customers'],
                $stats['previous']['customers']
            ),
            'avg_transaction' => $this->calculatePercentageChange(
                $stats['current']['avg_transaction'],
                $stats['previous']['avg_transaction']
            ),
        ];
        
        return $stats;
    }
    
    /**
     * Calculate statistics for a period
     */
    private function calculatePeriodStats($businessUnit, $dates)
    {
        $revenue = 0;
        $transactions = 0;
        $customers = 0;
        
        if ($businessUnit === 'all' || $businessUnit === 'lounge') {
            $loungeRevenue = Sale::completed()
                ->whereBetween('sale_date', [$dates['start'], $dates['end']])
                ->sum('total_amount');
            $loungeTransactions = Sale::completed()
                ->whereBetween('sale_date', [$dates['start'], $dates['end']])
                ->count();
            $loungeCustomers = Sale::whereBetween('sale_date', [$dates['start'], $dates['end']])
                ->distinct('customer_id')
                ->count('customer_id');
            
            $revenue += $loungeRevenue;
            $transactions += $loungeTransactions;
            $customers += $loungeCustomers;
        }
        
        if ($businessUnit === 'all' || $businessUnit === 'gift_store') {
            $storeRevenue = StoreSale::completed()
                ->whereBetween('sale_date', [$dates['start'], $dates['end']])
                ->sum('total_amount');
            $storeTransactions = StoreSale::completed()
                ->whereBetween('sale_date', [$dates['start'], $dates['end']])
                ->count();
            $storeCustomers = StoreSale::whereBetween('sale_date', [$dates['start'], $dates['end']])
                ->distinct('store_customer_id')
                ->count('store_customer_id');
            
            $revenue += $storeRevenue;
            $transactions += $storeTransactions;
            $customers += $storeCustomers;
        }
        
        if ($businessUnit === 'all' || $businessUnit === 'photo_studio') {
            $studioRevenue = StudioSession::completed()
                ->whereBetween('check_out_time', [$dates['start'], $dates['end']])
                ->where('payment_status', 'paid')
                ->sum('total_amount');
            $studioTransactions = StudioSession::completed()
                ->whereBetween('check_out_time', [$dates['start'], $dates['end']])
                ->count();
            $studioCustomers = StudioSession::whereBetween('check_in_time', [$dates['start'], $dates['end']])
                ->distinct('customer_id')
                ->count('customer_id');
            
            $revenue += $studioRevenue;
            $transactions += $studioTransactions;
            $customers += $studioCustomers;
        }
        
        if ($businessUnit === 'all' || $businessUnit === 'prop_rental') {
            $propRevenue = PropRental::whereBetween('created_at', [$dates['start'], $dates['end']])
                ->sum('total_amount');
            $propTransactions = PropRental::whereBetween('created_at', [$dates['start'], $dates['end']])
                ->count();
            $propCustomers = PropRental::whereBetween('created_at', [$dates['start'], $dates['end']])
                ->distinct('rental_customer_id')
                ->count('rental_customer_id');
            
            $revenue += $propRevenue;
            $transactions += $propTransactions;
            $customers += $propCustomers;
        }
        
        return [
            'revenue' => $revenue,
            'transactions' => $transactions,
            'customers' => $customers,
            'avg_transaction' => $transactions > 0 ? $revenue / $transactions : 0,
        ];
    }
    
    /**
     * Calculate percentage change
     */
    private function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return (($current - $previous) / $previous) * 100;
    }
    
    /**
     * Get revenue trend data
     */
    private function getRevenueTrend($businessUnit, $dates)
    {
        $days = $dates['start']->diffInDays($dates['end']) + 1;
        $trend = [];
        
        for ($i = 0; $i < min($days, 30); $i++) {
            $date = $dates['start']->copy()->addDays($i);
            $dateString = $date->toDateString();
            
            $dayRevenue = 0;
            
            if ($businessUnit === 'all' || $businessUnit === 'lounge') {
                $dayRevenue += Sale::completed()
                    ->whereDate('sale_date', $dateString)
                    ->sum('total_amount');
            }
            
            if ($businessUnit === 'all' || $businessUnit === 'gift_store') {
                $dayRevenue += StoreSale::completed()
                    ->whereDate('sale_date', $dateString)
                    ->sum('total_amount');
            }
            
            if ($businessUnit === 'all' || $businessUnit === 'photo_studio') {
                $dayRevenue += StudioSession::completed()
                    ->whereDate('check_out_time', $dateString)
                    ->where('payment_status', 'paid')
                    ->sum('total_amount');
            }
            
            if ($businessUnit === 'all' || $businessUnit === 'prop_rental') {
                $dayRevenue += PropRental::whereDate('created_at', $dateString)
                    ->sum('total_amount');
            }
            
            $trend[] = [
                'date' => $date->format('M d'),
                'revenue' => $dayRevenue,
            ];
        }
        
        return $trend;
    }
    
    /**
     * Get business unit comparison
     */
    private function getBusinessComparison($dates)
    {
        return [
            [
                'name' => 'Mini Lounge',
                'revenue' => Sale::completed()
                    ->whereBetween('sale_date', [$dates['start'], $dates['end']])
                    ->sum('total_amount'),
                'transactions' => Sale::completed()
                    ->whereBetween('sale_date', [$dates['start'], $dates['end']])
                    ->count(),
                'color' => '#10b981',
            ],
            [
                'name' => 'Gift Store',
                'revenue' => StoreSale::completed()
                    ->whereBetween('sale_date', [$dates['start'], $dates['end']])
                    ->sum('total_amount'),
                'transactions' => StoreSale::completed()
                    ->whereBetween('sale_date', [$dates['start'], $dates['end']])
                    ->count(),
                'color' => '#ef4444',
            ],
            [
                'name' => 'Photo Studio',
                'revenue' => StudioSession::completed()
                    ->whereBetween('check_out_time', [$dates['start'], $dates['end']])
                    ->where('payment_status', 'paid')
                    ->sum('total_amount'),
                'transactions' => StudioSession::completed()
                    ->whereBetween('check_out_time', [$dates['start'], $dates['end']])
                    ->count(),
                'color' => '#6f42c1',
            ],
            [
                'name' => 'Prop Rental',
                'revenue' => PropRental::whereBetween('created_at', [$dates['start'], $dates['end']])
                    ->sum('total_amount'),
                'transactions' => PropRental::whereBetween('created_at', [$dates['start'], $dates['end']])
                    ->count(),
                'color' => '#f59e0b',
            ],
        ];
    }
    
    /**
     * Get top performers - FIXED COLUMN NAMES
     */
    private function getTopPerformers($businessUnit, $dates)
    {
        $performers = [];
        
        if ($businessUnit === 'all' || $businessUnit === 'lounge') {
            $topProducts = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->join('products', 'sale_items.product_id', '=', 'products.id')
                ->whereBetween('sales.sale_date', [$dates['start'], $dates['end']])
                ->where('sales.status', 'completed')
                ->select(
                    'products.name',
                    DB::raw('SUM(sale_items.quantity) as total_quantity'),
                    DB::raw('SUM(sale_items.total_price) as total_revenue')  // FIXED: was subtotal
                )
                ->groupBy('products.id', 'products.name')
                ->orderBy('total_revenue', 'desc')
                ->limit(5)
                ->get();
            
            $performers['lounge'] = $topProducts;
        }
        
        if ($businessUnit === 'all' || $businessUnit === 'gift_store') {
            $topStoreProducts = DB::table('store_sale_items')
                ->join('store_sales', 'store_sale_items.store_sale_id', '=', 'store_sales.id')
                ->join('store_products', 'store_sale_items.store_product_id', '=', 'store_products.id')
                ->whereBetween('store_sales.sale_date', [$dates['start'], $dates['end']])
                ->where('store_sales.status', 'completed')
                ->select(
                    'store_products.name',
                    DB::raw('SUM(store_sale_items.quantity) as total_quantity'),
                    DB::raw('SUM(store_sale_items.total_price) as total_revenue')  // FIXED: was subtotal
                )
                ->groupBy('store_products.id', 'store_products.name')
                ->orderBy('total_revenue', 'desc')
                ->limit(5)
                ->get();
            
            $performers['gift_store'] = $topStoreProducts;
        }
        
        return $performers;
    }
    
    /**
     * Get customer insights
     */
    private function getCustomerInsights($businessUnit, $dates)
    {
        $insights = [
            'new_customers' => 0,
            'returning_customers' => 0,
            'top_customers' => [],
        ];
        
        if ($businessUnit === 'all' || $businessUnit === 'lounge') {
            $newLounge = Customer::whereBetween('created_at', [$dates['start'], $dates['end']])->count();
            $insights['new_customers'] += $newLounge;
        }
        
        if ($businessUnit === 'all' || $businessUnit === 'gift_store') {
            $newStore = StoreCustomer::whereBetween('created_at', [$dates['start'], $dates['end']])->count();
            $insights['new_customers'] += $newStore;
        }
        
        if ($businessUnit === 'all' || $businessUnit === 'photo_studio') {
            $newStudio = StudioCustomer::whereBetween('created_at', [$dates['start'], $dates['end']])->count();
            $insights['new_customers'] += $newStudio;
        }
        
        if ($businessUnit === 'all' || $businessUnit === 'prop_rental') {
            $newRental = RentalCustomer::whereBetween('created_at', [$dates['start'], $dates['end']])->count();
            $insights['new_customers'] += $newRental;
        }
        
        return $insights;
    }
    
    /**
     * Get payment status breakdown
     */
    private function getPaymentStatusBreakdown($businessUnit, $dates)
    {
        $paid = 0;
        $pending = 0;
        
        if ($businessUnit === 'all' || $businessUnit === 'lounge') {
            $paid += Sale::whereBetween('sale_date', [$dates['start'], $dates['end']])
                ->where('payment_status', 'completed')
                ->sum('total_amount');
            $pending += Sale::whereBetween('sale_date', [$dates['start'], $dates['end']])
                ->where('payment_status', 'pending')
                ->sum('total_amount');
        }
        
        if ($businessUnit === 'all' || $businessUnit === 'gift_store') {
            $paid += StoreSale::whereBetween('sale_date', [$dates['start'], $dates['end']])
                ->where('payment_status', 'completed')
                ->sum('total_amount');
            $pending += StoreSale::whereBetween('sale_date', [$dates['start'], $dates['end']])
                ->where('payment_status', 'pending')
                ->sum('total_amount');
        }
        
        if ($businessUnit === 'all' || $businessUnit === 'photo_studio') {
            $paid += StudioSession::whereBetween('check_in_time', [$dates['start'], $dates['end']])
                ->where('payment_status', 'paid')
                ->sum('total_amount');
            $pending += StudioSession::whereBetween('check_in_time', [$dates['start'], $dates['end']])
                ->where('payment_status', 'pending')
                ->sum('total_amount');
        }
        
        return [
            'paid' => $paid,
            'pending' => $pending,
            'total' => $paid + $pending,
        ];
    }
    
    /**
     * Get time-based trend (hourly for today, daily otherwise)
     */
    private function getTimeTrend($businessUnit, $dates)
    {
        // Implementation depends on requirements
        return [];
    }
}