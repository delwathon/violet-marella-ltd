<?php

namespace App\Http\Controllers\PhotoStudio;

use App\Http\Controllers\Controller;
use App\Models\StudioCategory;
use App\Models\StudioCustomer;
use App\Models\StudioSession;
use App\Models\StudioSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PhotoStudioController extends Controller
{
    /**
     * Display enhanced dashboard
     */
    public function index()
    {
        $user = Auth::guard('user')->user();

        // Auto-start sessions once preparation time elapses.
        StudioSession::autoStartDueSessions();
        
        // Get all categories with session counts
        $categories = StudioCategory::withCount(['rooms', 'activeSessions'])
                                    ->active()
                                    ->ordered()
                                    ->get();
        
        // Get active sessions count
        $activeSessions = StudioSession::active()->count();
        
        // Get today's statistics
        $todayStats = $this->getTodayStatistics();
        
        // Get this week's statistics
        $weekStats = $this->getWeekStatistics();
        
        // Get this month's statistics
        $monthStats = $this->getMonthStatistics();
        
        // Get recent sessions (last 10)
        $recentSessions = StudioSession::with(['category', 'customer'])
                                      ->completed()
                                      ->orderBy('check_out_time', 'desc')
                                      ->limit(10)
                                      ->get();
        
        // Get top customers
        $topCustomers = StudioCustomer::orderBy('total_spent', 'desc')
                                      ->limit(5)
                                      ->get();
        
        // Revenue chart data (last 7 days)
        $revenueData = $this->getRevenueChartData();
        
        // Category occupancy data
        $occupancyData = $this->getCategoryOccupancyData();
        
        // Get global settings
        $offsetTime = StudioSetting::offsetTime();
        
        return view('pages.photo-studio.dashboard', compact(
            'user',
            'categories',
            'activeSessions',
            'todayStats',
            'weekStats',
            'monthStats',
            'recentSessions',
            'topCustomers',
            'revenueData',
            'occupancyData',
            'offsetTime'
        ));
    }

    /**
     * Active sessions page
     */
    public function activeSessions()
    {
        $user = Auth::guard('user')->user();

        StudioSession::autoStartDueSessions();
        
        $sessions = StudioSession::with(['category', 'customer'])
                                ->active()
                                ->orderBy('check_in_time', 'desc')
                                ->get();
        
        $categories = StudioCategory::active()->get();
        
        return view('pages.photo-studio.active-sessions', compact('user', 'sessions', 'categories'));
    }

    /**
     * Check-in a customer
     * New version with category-based booking and offset time
     */
    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'category_id' => 'required|exists:studio_categories,id',
            'number_of_people' => 'required|integer|min:1',
            'booked_duration' => 'required|integer|min:10',
            'party_names' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Get category
            $category = StudioCategory::findOrFail($validated['category_id']);
            
            // Check if category is available
            if (!$category->isAvailable()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category is not available',
                ], 422);
            }
            
            // Check if category can accept more sessions
            if (!$category->canAcceptMoreSessions()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No available slots for this category',
                ], 422);
            }
            
            // Check if party size is acceptable
            if (!$category->canAccommodate($validated['number_of_people'])) {
                return response()->json([
                    'success' => false,
                    'message' => "This category allows a maximum of {$category->max_occupants} people",
                ], 422);
            }

            // Find or create customer
            $customer = StudioCustomer::where('phone', $validated['customer_phone'])->first();
            
            if (!$customer) {
                $customer = StudioCustomer::create([
                    'name' => $validated['customer_name'],
                    'phone' => $validated['customer_phone'],
                    'email' => $validated['customer_email'] ?? null,
                ]);
            } else {
                // Check if customer can book
                if (!$customer->canBook()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Customer is not allowed to book',
                    ], 422);
                }
                
                // Check if customer already has an active session
                if ($customer->hasActiveSession()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Customer already has an active session',
                    ], 422);
                }
                
                // Update customer info if provided
                $customer->update([
                    'name' => $validated['customer_name'],
                    'email' => $validated['customer_email'] ?? $customer->email,
                ]);
            }

            // Get offset time
            $offsetTime = StudioSetting::offsetTime();
            
            // Calculate base amount based on booked duration
            $baseAmount = $category->calculatePrice($validated['booked_duration']);
            
            // Create session
            $checkInTime = now();
            $scheduledStartTime = Carbon::parse($checkInTime)->addMinutes($offsetTime);
            
            $session = StudioSession::create([
                'category_id' => $category->id,
                'customer_id' => $customer->id,
                'number_of_people' => $validated['number_of_people'],
                'party_names' => $validated['party_names'] ?? null,
                'check_in_time' => $checkInTime,
                'scheduled_start_time' => $scheduledStartTime,
                'booked_duration' => $validated['booked_duration'],
                'offset_time_applied' => $offsetTime,
                'rate_base_time' => $category->base_time,
                'rate_base_price' => $category->base_price,
                'rate_per_minute' => $category->per_minute_rate,
                'base_amount' => $baseAmount,
                'total_amount' => $baseAmount,
                'status' => 'pending',
                'payment_status' => 'pending',
                'created_by' => Auth::guard('user')->id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$customer->name} checked into {$category->name}",
                'session' => $session->load(['category', 'customer']),
                'qr_code' => $session->qr_code,
                'scheduled_start_time' => $scheduledStartTime->format('h:i A'),
                'offset_minutes' => $offsetTime,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to check in: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Checkout session
     */
    public function checkout(Request $request, $id)
    {
        StudioSession::autoStartDueSessions((int) $id);

        $validated = $request->validate([
            'payment_method' => 'required|in:cash,card,transfer,other',
            'discount_amount' => 'nullable|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $session = StudioSession::with(['category', 'customer'])->findOrFail($id);

            if (!in_array($session->status, ['pending', 'active', 'overtime'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session is not active',
                ], 422);
            }

            // Checkout the session
            $discountAmount = $validated['discount_amount'] ?? 0;
            $session->checkout($validated['payment_method'], $discountAmount, Auth::guard('user')->id());

            // Process payment if amount provided
            if (isset($validated['amount_paid']) && $validated['amount_paid'] > 0) {
                $session->processPayment(
                    $validated['amount_paid'],
                    $validated['payment_method'],
                    Auth::guard('user')->id()
                );
            }

            // Update customer statistics
            $session->customer->updateStatistics();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checkout successful',
                'session' => $session->fresh()->load(['category', 'customer', 'payments']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Checkout failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Start session timer (when offset time expires)
     */
    public function startTimer($id)
    {
        $session = StudioSession::findOrFail($id);
        
        if (!$session->shouldStartTimer()) {
            return response()->json([
                'success' => false,
                'message' => 'Session timer cannot be started yet',
            ], 422);
        }
        
        $session->startTimer();
        
        return response()->json([
            'success' => true,
            'message' => 'Session timer started',
            'session' => $session->fresh(),
        ]);
    }

    /**
     * Get session details
     */
    public function getSession($id)
    {
        StudioSession::autoStartDueSessions((int) $id);

        $session = StudioSession::with(['category', 'customer', 'payments'])
                                ->findOrFail($id);

        return response()->json([
            'success' => true,
            'session' => $session,
            'current_duration' => $session->getCurrentDuration(),
            'formatted_duration' => $session->formatted_duration,
            'time_remaining' => $session->getTimeRemaining(),
            'formatted_time_remaining' => $session->formatted_time_remaining,
            'is_overtime' => $session->isOvertime(),
            'has_timer_started' => $session->hasTimerStarted(),
            'should_start_timer' => $session->shouldStartTimer(),
        ]);
    }

    /**
     * Extend session duration
     */
    public function extendSession(Request $request, $id)
    {
        $validated = $request->validate([
            'additional_time' => 'required|integer|min:1',
        ]);

        $session = StudioSession::findOrFail($id);

        if (!in_array($session->status, ['pending', 'active', 'overtime'])) {
            return response()->json([
                'success' => false,
                'message' => 'Can only extend active sessions',
            ], 422);
        }

        $session->booked_duration += $validated['additional_time'];
        
        // Recalculate base amount for new booked duration
        $session->base_amount = $session->category->calculatePrice($session->booked_duration);
        $session->save();
        
        // Update amounts
        $session->updateAmounts();

        return response()->json([
            'success' => true,
            'message' => "Session extended by {$validated['additional_time']} minutes",
            'session' => $session->fresh(),
        ]);
    }

    /**
     * Cancel session
     */
    public function cancelSession(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $session = StudioSession::findOrFail($id);
        
        if ($session->cancel($validated['reason'])) {
            return response()->json([
                'success' => true,
                'message' => 'Session cancelled successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Cannot cancel this session',
        ], 422);
    }

    /**
     * Get active sessions (AJAX)
     */
    public function getActiveSessions()
    {
        StudioSession::autoStartDueSessions();

        $sessions = StudioSession::with(['category', 'customer'])
                                ->active()
                                ->orderBy('check_in_time', 'desc')
                                ->get()
                                ->map(function($session) {
                                    return [
                                        'id' => $session->id,
                                        'session_code' => $session->session_code,
                                        'customer_name' => $session->customer->name,
                                        'category_name' => $session->category->name,
                                        'number_of_people' => $session->number_of_people,
                                        'check_in_time' => $session->check_in_time->format('h:i A'),
                                        'scheduled_start_time' => $session->scheduled_start_time->format('h:i A'),
                                        'current_duration' => $session->getCurrentDuration(),
                                        'formatted_duration' => $session->formatted_duration,
                                        'time_remaining' => $session->getTimeRemaining(),
                                        'formatted_time_remaining' => $session->formatted_time_remaining,
                                        'is_overtime' => $session->isOvertime(),
                                        'status' => $session->status,
                                        'status_label' => $session->status_label,
                                        'has_timer_started' => $session->hasTimerStarted(),
                                        'booked_duration' => $session->booked_duration,
                                    ];
                                });

        return response()->json([
            'success' => true,
            'sessions' => $sessions,
        ]);
    }

    /**
     * Search customers
     */
    public function searchCustomers(Request $request)
    {
        $term = $request->get('term', '');
        
        $customers = StudioCustomer::search($term)
                                   ->active()
                                   ->limit(10)
                                   ->get();

        return response()->json([
            'success' => true,
            'customers' => $customers,
        ]);
    }

    /**
     * Generate QR Code for session
     */
    public function generateQRCode($id)
    {
        $session = StudioSession::with(['category', 'customer'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'qr_code' => $session->qr_code,
            'qr_data' => [
                'session_id' => $session->id,
                'session_code' => $session->session_code,
                'customer_name' => $session->customer->name,
                'category_name' => $session->category->name,
            ]
        ]);
    }

    /**
     * Scan QR Code
     */
    public function scanQRCode(Request $request)
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
        ]);

        $session = StudioSession::where('qr_code', $validated['qr_code'])
                                ->with(['category', 'customer'])
                                ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'session' => $session,
            'can_checkout' => in_array($session->status, ['pending', 'active', 'overtime']),
        ]);
    }

    /**
     * Get today's statistics
     */
    private function getTodayStatistics()
    {
        $today = today();
        
        $sessions = StudioSession::whereDate('check_in_time', $today)->get();
        $completedSessions = $sessions->where('status', 'completed');
        
        $totalMinutes = $completedSessions->sum('actual_duration');
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        
        return [
            'totalSessions' => $sessions->count(),
            'activeSessions' => $sessions->whereIn('status', ['pending', 'active', 'overtime'])->count(),
            'completedSessions' => $completedSessions->count(),
            'totalHours' => "{$hours}h {$minutes}m",
            'totalMinutes' => $totalMinutes,
            'revenue' => $completedSessions->where('payment_status', 'paid')->sum('total_amount'),
            'pendingPayment' => $completedSessions->where('payment_status', 'pending')->sum('total_amount'),
        ];
    }

    /**
     * Get week statistics
     */
    private function getWeekStatistics()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $sessions = StudioSession::whereBetween('check_in_time', [$startOfWeek, $endOfWeek])
                                ->completed()
                                ->get();
        
        return [
            'totalSessions' => $sessions->count(),
            'revenue' => $sessions->where('payment_status', 'paid')->sum('total_amount'),
            'averageSessionDuration' => $sessions->avg('actual_duration'),
        ];
    }

    /**
     * Get month statistics
     */
    private function getMonthStatistics()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $sessions = StudioSession::whereBetween('check_in_time', [$startOfMonth, $endOfMonth])
                                ->completed()
                                ->get();
        
        return [
            'totalSessions' => $sessions->count(),
            'revenue' => $sessions->where('payment_status', 'paid')->sum('total_amount'),
            'uniqueCustomers' => $sessions->unique('customer_id')->count(),
        ];
    }

    /**
     * Get revenue chart data (last 7 days)
     */
    private function getRevenueChartData()
    {
        $data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenue = StudioSession::whereDate('check_in_time', $date)
                                   ->where('payment_status', 'paid')
                                   ->sum('total_amount');
            
            $data[] = [
                'date' => $date->format('M d'),
                'revenue' => $revenue,
            ];
        }
        
        return $data;
    }

    /**
     * Get category occupancy data
     */
    private function getCategoryOccupancyData()
    {
        $categories = StudioCategory::active()->get();
        $data = [];
        
        foreach ($categories as $category) {
            $data[] = [
                'category' => $category->name,
                'rate' => $category->todayOccupancyRate(),
            ];
        }
        
        return $data;
    }
}
