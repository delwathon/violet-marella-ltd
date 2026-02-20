<?php

namespace App\Http\Controllers\PhotoStudio;

use App\Http\Controllers\Controller;
use App\Models\StudioSession;
use App\Models\StudioCategory;
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
        $categoryId = $request->get('category_id');
        $paymentStatus = $request->get('payment_status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        $sessions = StudioSession::with(['category', 'customer'])
            ->when($status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($categoryId, function($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($paymentStatus, function($query, $paymentStatus) {
                $query->where('payment_status', $paymentStatus);
            })
            ->when($dateFrom, function($query, $dateFrom) {
                $query->whereDate('check_in_time', '>=', $dateFrom);
            })
            ->when($dateTo, function($query, $dateTo) {
                $query->whereDate('check_in_time', '<=', $dateTo);
            })
            ->orderBy('check_in_time', 'desc')
            ->paginate(20);
        
        // Get categories for filter
        $categories = StudioCategory::active()->ordered()->get();
        
        return view('pages.photo-studio.sessions.index', compact(
            'user', 
            'sessions', 
            'categories',
            'status',
            'categoryId',
            'paymentStatus',
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
        $session = StudioSession::with(['category', 'customer', 'payments.receivedBy', 'creator', 'checkoutStaff'])
                                ->findOrFail($id);
        
        return view('pages.photo-studio.sessions.show', compact('user', 'session'));
    }

    /**
     * Process additional payment for session
     */
    public function processPayment(Request $request, $id)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,transfer,other',
        ]);

        $session = StudioSession::findOrFail($id);
        
        if ($session->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Can only process payments for completed sessions',
            ], 422);
        }

        $payment = $session->processPayment(
            $validated['amount'],
            $validated['payment_method'],
            Auth::guard('user')->id()
        );

        return response()->json([
            'success' => true,
            'message' => 'Payment processed successfully',
            'payment' => $payment,
            'session' => $session->fresh(),
        ]);
    }

    /**
     * Export sessions to CSV
     */
    public function export(Request $request)
    {
        $status = $request->get('status');
        $categoryId = $request->get('category_id');
        $paymentStatus = $request->get('payment_status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        $sessions = StudioSession::with(['category', 'customer'])
            ->when($status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($categoryId, function($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($paymentStatus, function($query, $paymentStatus) {
                $query->where('payment_status', $paymentStatus);
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
                'Category',
                'Number of People',
                'Check-in Time',
                'Scheduled Start',
                'Actual Start',
                'Check-out Time',
                'Booked Duration (min)',
                'Actual Duration (min)',
                'Overtime (min)',
                'Base Amount',
                'Overtime Amount',
                'Discount',
                'Total Amount',
                'Amount Paid',
                'Balance',
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
                    $session->category->name,
                    $session->number_of_people,
                    $session->check_in_time->format('Y-m-d H:i:s'),
                    $session->scheduled_start_time->format('Y-m-d H:i:s'),
                    $session->actual_start_time ? $session->actual_start_time->format('Y-m-d H:i:s') : 'N/A',
                    $session->check_out_time ? $session->check_out_time->format('Y-m-d H:i:s') : 'N/A',
                    $session->booked_duration,
                    $session->actual_duration ?? 'N/A',
                    $session->overtime_duration ?? 'N/A',
                    $session->base_amount,
                    $session->overtime_amount,
                    $session->discount_amount,
                    $session->total_amount,
                    $session->amount_paid,
                    $session->balance,
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
