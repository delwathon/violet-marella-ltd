<?php

namespace App\Http\Controllers\PhotoStudio;

use App\Http\Controllers\Controller;
use App\Models\Studio;
use App\Models\StudioCustomer;
use App\Models\StudioSession;
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
        
        $sessions = StudioSession::with(['studio', 'customer'])
            ->whereDate('check_in_time', $date)
            ->orderBy('check_in_time', 'desc')
            ->get();
        
        $stats = [
            'total_sessions' => $sessions->count(),
            'completed' => $sessions->where('status', 'completed')->count(),
            'active' => $sessions->where('status', 'active')->count(),
            'cancelled' => $sessions->where('status', 'cancelled')->count(),
            'total_revenue' => $sessions->where('payment_status', 'paid')->sum('total_amount'),
            'pending_payment' => $sessions->where('payment_status', 'pending')->sum('total_amount'),
            'total_minutes' => $sessions->where('status', 'completed')->sum('actual_duration'),
        ];
        
        return view('pages.photo-studio.reports.daily', compact('user', 'date', 'sessions', 'stats'));
    }

    /**
     * Revenue report
     */
    public function revenue(Request $request)
    {
        $user = Auth::guard('user')->user();
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        
        $sessions = StudioSession::with(['studio', 'customer'])
            ->whereBetween('check_in_time', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->get();
        
        $stats = [
            'total_revenue' => $sessions->sum('total_amount'),
            'total_sessions' => $sessions->count(),
            'average_session_value' => $sessions->avg('total_amount'),
            'by_studio' => $sessions->groupBy('studio_id')->map(function($group) {
                return [
                    'studio' => $group->first()->studio->name,
                    'revenue' => $group->sum('total_amount'),
                    'sessions' => $group->count(),
                ];
            }),
            'by_payment_method' => $sessions->groupBy('payment_method')->map(function($group) {
                return [
                    'method' => $group->first()->payment_method,
                    'amount' => $group->sum('total_amount'),
                    'count' => $group->count(),
                ];
            }),
        ];
        
        return view('pages.photo-studio.reports.revenue', compact('user', 'startDate', 'endDate', 'sessions', 'stats'));
    }

    /**
     * Occupancy report
     */
    public function occupancy(Request $request)
    {
        $user = Auth::guard('user')->user();
        $date = $request->get('date', today());
        
        $studios = Studio::with(['sessions' => function($query) use ($date) {
            $query->whereDate('check_in_time', $date);
        }])->get();
        
        $occupancyData = $studios->map(function($studio) use ($date) {
            $totalMinutes = 1440; // Minutes in a day
            $occupiedMinutes = $studio->sessions->sum('actual_duration');
            $occupancyRate = $totalMinutes > 0 ? ($occupiedMinutes / $totalMinutes) * 100 : 0;
            
            return [
                'studio' => $studio->name,
                'sessions' => $studio->sessions->count(),
                'total_minutes' => $occupiedMinutes,
                'occupancy_rate' => round($occupancyRate, 2),
                'revenue' => $studio->sessions->where('payment_status', 'paid')->sum('total_amount'),
            ];
        });
        
        return view('pages.photo-studio.reports.occupancy', compact('user', 'date', 'occupancyData'));
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
            'total_revenue' => $customers->sum('total_spent'),
            'average_spent' => $customers->avg('total_spent'),
            'total_sessions' => $customers->sum('total_sessions'),
        ];
        
        return view('pages.photo-studio.reports.customers', compact('user', 'customers', 'stats'));
    }

    /**
     * Export report
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'daily');
        $date = $request->get('date', today());
        
        $sessions = StudioSession::with(['studio', 'customer'])
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
                'Studio',
                'Check-in',
                'Check-out',
                'Duration (min)',
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
                    $session->studio->name,
                    $session->check_in_time->format('Y-m-d H:i:s'),
                    $session->check_out_time ? $session->check_out_time->format('Y-m-d H:i:s') : 'N/A',
                    $session->actual_duration ?? 'N/A',
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