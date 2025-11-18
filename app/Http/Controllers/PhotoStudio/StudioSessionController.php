<?php

namespace App\Http\Controllers\PhotoStudio;

use App\Http\Controllers\Controller;
use App\Models\StudioSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudioSessionController extends Controller
{
    /**
     * Display a listing of sessions
     */
    public function index(Request $request)
    {
        $user = Auth::guard('user')->user();
        
        $status = $request->get('status');
        $studioId = $request->get('studio_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        $sessions = StudioSession::with(['studio', 'customer'])
            ->when($status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($studioId, function($query, $studioId) {
                $query->where('studio_id', $studioId);
            })
            ->when($dateFrom, function($query, $dateFrom) {
                $query->whereDate('check_in_time', '>=', $dateFrom);
            })
            ->when($dateTo, function($query, $dateTo) {
                $query->whereDate('check_in_time', '<=', $dateTo);
            })
            ->orderBy('check_in_time', 'desc')
            ->paginate(20);
        
        // Get studios for filter
        $studios = \App\Models\Studio::active()->get();
        
        return view('pages.photo-studio.sessions.index', compact(
            'user', 
            'sessions', 
            'studios',
            'status',
            'studioId',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Display the specified session
     */
    public function show($id)
    {
        $user = Auth::guard('user')->user();
        $session = StudioSession::with(['studio', 'customer', 'payments'])
            ->findOrFail($id);
        
        return view('pages.photo-studio.sessions.show', compact('user', 'session'));
    }

    /**
     * Export sessions to CSV
     */
    public function export(Request $request)
    {
        $status = $request->get('status');
        $studioId = $request->get('studio_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        $sessions = StudioSession::with(['studio', 'customer'])
            ->when($status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($studioId, function($query, $studioId) {
                $query->where('studio_id', $studioId);
            })
            ->when($dateFrom, function($query, $dateFrom) {
                $query->whereDate('check_in_time', '>=', $dateFrom);
            })
            ->when($dateTo, function($query, $dateTo) {
                $query->whereDate('check_in_time', '<=', $dateTo);
            })
            ->orderBy('check_in_time', 'desc')
            ->get();
        
        $filename = 'studio-sessions-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($sessions) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Session Code',
                'Customer Name',
                'Customer Phone',
                'Studio',
                'Check-in Time',
                'Check-out Time',
                'Duration (minutes)',
                'Expected Duration',
                'Base Amount',
                'Extra Amount',
                'Discount',
                'Total Amount',
                'Payment Status',
                'Payment Method',
                'Status',
            ]);
            
            // Data rows
            foreach ($sessions as $session) {
                fputcsv($file, [
                    $session->session_code,
                    $session->customer->name,
                    $session->customer->phone,
                    $session->studio->name,
                    $session->check_in_time->format('Y-m-d H:i:s'),
                    $session->check_out_time ? $session->check_out_time->format('Y-m-d H:i:s') : 'N/A',
                    $session->actual_duration ?? 'N/A',
                    $session->expected_duration,
                    $session->base_amount,
                    $session->extra_amount,
                    $session->discount_amount,
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