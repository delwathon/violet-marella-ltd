<?php

namespace App\Http\Controllers\PhotoStudio;

use App\Http\Controllers\Controller;
use App\Models\StudioCategory;
use App\Models\StudioCustomer;
use App\Models\StudioSession;
use App\Models\StudioPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StudioReportController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index()
    {
        $user = Auth::guard('user')->user();
        
        return view('pages.photo-studio.reports.index', compact('user'));
    }

    /**
     * Daily report
     */
    public function daily(Request $request)
    {
        $user = Auth::guard('user')->user();
        $date = $request->get('date', today());
        
        $sessions = StudioSession::with(['category', 'customer'])
                                ->whereDate('check_in_time', $date)
                                ->orderBy('check_in_time', 'desc')
                                ->get();
        
        $stats = [
            'total_sessions' => $sessions->count(),
            'completed' => $sessions->where('status', 'completed')->count(),
            'active' => $sessions->whereIn('status', ['pending', 'active', 'overtime'])->count(),
            'cancelled' => $sessions->where('status', 'cancelled')->count(),
            'no_show' => $sessions->where('status', 'no_show')->count(),
            'total_revenue' => $sessions->where('payment_status', 'paid')->sum('total_amount'),
            'pending_payment' => $sessions->where('payment_status', 'pending')->sum('total_amount'),
            'total_minutes' => $sessions->where('status', 'completed')->sum('actual_duration'),
            'total_customers' => $sessions->unique('customer_id')->count(),
            'average_party_size' => $sessions->avg('number_of_people'),
        ];
        
        // Revenue by category
        $byCategory = $sessions->where('payment_status', 'paid')
                              ->groupBy('category_id')
                              ->map(function($group) {
                                  return [
                                      'category' => $group->first()->category->name,
                                      'revenue' => $group->sum('total_amount'),
                                      'sessions' => $group->count(),
                                  ];
                              });
        
        return view('pages.photo-studio.reports.daily', compact(
            'user',
            'date',
            'sessions',
            'stats',
            'byCategory'
        ));
    }

    /**
     * Revenue report
     */
    public function revenue(Request $request)
    {
        $user = Auth::guard('user')->user();
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        
        $sessions = StudioSession::with(['category', 'customer'])
                                ->whereBetween('check_in_time', [$startDate, $endDate])
                                ->where('payment_status', 'paid')
                                ->get();
        
        $stats = [
            'total_revenue' => $sessions->sum('total_amount'),
            'total_sessions' => $sessions->count(),
            'average_session_value' => $sessions->avg('total_amount'),
            'base_revenue' => $sessions->sum('base_amount'),
            'overtime_revenue' => $sessions->sum('overtime_amount'),
            'discounts_given' => $sessions->sum('discount_amount'),
        ];
        
        // Revenue by category
        $byCategory = $sessions->groupBy('category_id')->map(function($group) {
            return [
                'category' => $group->first()->category->name,
                'revenue' => $group->sum('total_amount'),
                'sessions' => $group->count(),
                'average' => $group->avg('total_amount'),
            ];
        });
        
        // Revenue by payment method
        $byPaymentMethod = $sessions->groupBy('payment_method')->map(function($group) {
            return [
                'method' => ucfirst($group->first()->payment_method),
                'amount' => $group->sum('total_amount'),
                'count' => $group->count(),
            ];
        });
        
        // Daily revenue breakdown
        $dailyRevenue = [];
        $currentDate = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        while ($currentDate <= $end) {
            $dayRevenue = StudioSession::whereDate('check_in_time', $currentDate)
                                      ->where('payment_status', 'paid')
                                      ->sum('total_amount');
            
            $dailyRevenue[] = [
                'date' => $currentDate->format('M d'),
                'revenue' => $dayRevenue,
            ];
            
            $currentDate->addDay();
        }
        
        return view('pages.photo-studio.reports.revenue', compact(
            'user',
            'startDate',
            'endDate',
            'sessions',
            'stats',
            'byCategory',
            'byPaymentMethod',
            'dailyRevenue'
        ));
    }

    /**
     * Occupancy report
     */
    public function occupancy(Request $request)
    {
        $user = Auth::guard('user')->user();
        $date = $request->get('date', today());
        
        $categories = StudioCategory::active()->get();
        
        $occupancyData = $categories->map(function($category) use ($date) {
            $sessions = $category->sessions()
                                ->whereDate('check_in_time', $date)
                                ->where('status', 'completed')
                                ->get();
            
            $totalMinutes = $sessions->sum('actual_duration');
            $totalSessions = $sessions->count();
            
            // Max possible minutes = max_concurrent_sessions × 1440 (minutes in day)
            $maxPossibleMinutes = $category->max_concurrent_sessions * 1440;
            $occupancyRate = $maxPossibleMinutes > 0 
                           ? ($totalMinutes / $maxPossibleMinutes) * 100 
                           : 0;
            
            return [
                'category' => $category->name,
                'sessions' => $totalSessions,
                'total_minutes' => $totalMinutes,
                'max_concurrent' => $category->max_concurrent_sessions,
                'occupancy_rate' => round($occupancyRate, 2),
                'revenue' => $sessions->where('payment_status', 'paid')->sum('total_amount'),
                'average_duration' => $totalSessions > 0 ? round($totalMinutes / $totalSessions, 2) : 0,
            ];
        });
        
        return view('pages.photo-studio.reports.occupancy', compact(
            'user',
            'date',
            'occupancyData'
        ));
    }

    /**
     * Customer report
     */
    public function customers(Request $request)
    {
        $user = Auth::guard('user')->user();
        
        $customers = StudioCustomer::with(['sessions'])
                                   ->orderBy('total_spent', 'desc')
                                   ->get();
        
        $stats = [
            'total_customers' => $customers->count(),
            'active_customers' => $customers->where('is_active', true)->count(),
            'blacklisted_customers' => $customers->where('is_blacklisted', true)->count(),
            'new_customers' => $customers->where('total_sessions', '<', 3)->count(),
            'regular_customers' => $customers->where('total_sessions', '>=', 10)->count(),
            'total_revenue' => $customers->sum('total_spent'),
            'average_spent' => $customers->avg('total_spent'),
            'total_sessions' => $customers->sum('total_sessions'),
        ];
        
        // Customers by tier
        $byTier = [
            'Bronze' => $customers->where('total_spent', '<', 100000)->count(),
            'Silver' => $customers->where('total_spent', '>=', 100000)
                                 ->where('total_spent', '<', 200000)->count(),
            'Gold' => $customers->where('total_spent', '>=', 200000)
                               ->where('total_spent', '<', 500000)->count(),
            'Platinum' => $customers->where('total_spent', '>=', 500000)->count(),
        ];
        
        return view('pages.photo-studio.reports.customers', compact(
            'user',
            'customers',
            'stats',
            'byTier'
        ));
    }

    /**
     * Category performance report
     */
    public function categoryPerformance(Request $request)
    {
        $user = Auth::guard('user')->user();
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        
        $categories = StudioCategory::withCount([
            'sessions' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('check_in_time', [$startDate, $endDate]);
            }
        ])->get();
        
        $performanceData = $categories->map(function($category) use ($startDate, $endDate) {
            $sessions = $category->sessions()
                                ->whereBetween('check_in_time', [$startDate, $endDate])
                                ->get();
            
            $completedSessions = $sessions->where('status', 'completed');
            
            return [
                'category' => $category->name,
                'total_sessions' => $sessions->count(),
                'completed_sessions' => $completedSessions->count(),
                'cancelled_sessions' => $sessions->where('status', 'cancelled')->count(),
                'no_show' => $sessions->where('status', 'no_show')->count(),
                'revenue' => $completedSessions->where('payment_status', 'paid')->sum('total_amount'),
                'average_session_value' => $completedSessions->avg('total_amount'),
                'total_minutes' => $completedSessions->sum('actual_duration'),
                'average_duration' => $completedSessions->avg('actual_duration'),
                'total_people' => $sessions->sum('number_of_people'),
                'utilization' => $category->todayOccupancyRate(),
            ];
        });
        
        return view('pages.photo-studio.reports.category-performance', compact(
            'user',
            'startDate',
            'endDate',
            'performanceData'
        ));
    }

    /**
     * Export report
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'daily');
        $date = $request->get('date', today());
        
        $sessions = StudioSession::with(['category', 'customer'])
                                ->whereDate('check_in_time', $date)
                                ->get();
        
        $filename = "studio-report-{$type}-" . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($sessions) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Session Code',
                'Customer',
                'Phone',
                'Category',
                'Number of People',
                'Check-in',
                'Check-out',
                'Booked (min)',
                'Actual (min)',
                'Overtime (min)',
                'Amount',
                'Payment Status',
                'Payment Method',
                'Status',
            ]);
            
            foreach ($sessions as $session) {
                fputcsv($file, [
                    $session->session_code,
                    $session->customer->name,
                    $session->customer->phone,
                    $session->category->name,
                    $session->number_of_people,
                    $session->check_in_time->format('Y-m-d H:i:s'),
                    $session->check_out_time ? $session->check_out_time->format('Y-m-d H:i:s') : 'N/A',
                    $session->booked_duration,
                    $session->actual_duration ?? 'N/A',
                    $session->overtime_duration ?? 'N/A',
                    $session->total_amount,
                    $session->payment_status,
                    $session->payment_method ?? 'N/A',
                    $session->status,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}