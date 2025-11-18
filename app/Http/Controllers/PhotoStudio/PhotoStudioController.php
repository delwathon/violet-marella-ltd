<?php

namespace App\Http\Controllers\PhotoStudio;

use App\Http\Controllers\Controller;
use App\Models\Studio;
use App\Models\StudioCustomer;
use App\Models\StudioSession;
use App\Models\StudioRate;
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
        
        // Get all studios with active sessions
        $studios = Studio::with(['activeSession.customer'])
            ->active()
            ->orderBy('code')
            ->get();
        
        // Get active sessions count
        $activeSessions = StudioSession::active()->count();
        
        // Get today's statistics
        $todayStats = $this->getTodayStatistics();
        
        // Get this week's statistics
        $weekStats = $this->getWeekStatistics();
        
        // Get this month's statistics
        $monthStats = $this->getMonthStatistics();
        
        // Get recent sessions (last 5)
        $recentSessions = StudioSession::with(['studio', 'customer'])
            ->completed()
            ->orderBy('check_out_time', 'desc')
            ->limit(5)
            ->get();
        
        // Get top customers
        $topCustomers = StudioCustomer::orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();
        
        // Revenue chart data (last 7 days)
        $revenueData = $this->getRevenueChartData();
        
        // Studio occupancy data
        $occupancyData = $this->getOccupancyData();
        
        return view('pages.photo-studio.dashboard', compact(
            'user',
            'studios',
            'activeSessions',
            'todayStats',
            'weekStats',
            'monthStats',
            'recentSessions',
            'topCustomers',
            'revenueData',
            'occupancyData'
        ));
    }

    /**
     * Active sessions page
     */
    public function activeSessions()
    {
        $user = Auth::guard('user')->user();
        
        $sessions = StudioSession::with(['studio', 'customer'])
            ->active()
            ->orderBy('check_in_time', 'desc')
            ->get();
        
        $studios = Studio::active()->get();
        
        return view('pages.photo-studio.active-sessions', compact('user', 'sessions', 'studios'));
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
            'activeSessions' => $sessions->where('status', 'active')->count(),
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
     * Get occupancy data
     */
    private function getOccupancyData()
    {
        $studios = Studio::active()->get();
        $data = [];
        
        foreach ($studios as $studio) {
            $totalTime = 1440; // Minutes in a day
            $occupiedTime = StudioSession::where('studio_id', $studio->id)
                ->whereDate('check_in_time', today())
                ->sum('actual_duration');
            
            $occupancyRate = $totalTime > 0 ? ($occupiedTime / $totalTime) * 100 : 0;
            
            $data[] = [
                'studio' => $studio->name,
                'rate' => round($occupancyRate, 2),
            ];
        }
        
        return $data;
    }

    /**
     * Calculate base amount based on studio's hourly rate and expected duration
     */
    private function calculateBaseAmount($studio, $expectedDuration)
    {
        // If studio has an hourly rate, use it
        if (isset($studio->hourly_rate) && $studio->hourly_rate > 0) {
            $hours = $expectedDuration / 60;
            return round($hours * $studio->hourly_rate, 2);
        }
        
        // Otherwise, use the studio's base amount if available
        if (isset($studio->base_amount) && $studio->base_amount > 0) {
            $baseTime = $studio->base_time ?? 30;
            
            if ($expectedDuration <= $baseTime) {
                return $studio->base_amount;
            } else {
                $extraTime = $expectedDuration - $baseTime;
                $ratePerMinute = $studio->base_amount / $baseTime;
                return round($studio->base_amount + ($extraTime * $ratePerMinute), 2);
            }
        }
        
        // Fallback to default rate
        $rate = StudioRate::getDefault();
        if ($rate) {
            if ($expectedDuration <= $rate->base_time) {
                return $rate->base_amount;
            } else {
                $extraTime = $expectedDuration - $rate->base_time;
                $ratePerMinute = $rate->base_amount / $rate->base_time;
                return round($rate->base_amount + ($extraTime * $ratePerMinute), 2);
            }
        }
        
        // Final fallback: 2000 for 30 minutes
        if ($expectedDuration <= 30) {
            return 2000;
        } else {
            $extraTime = $expectedDuration - 30;
            return round(2000 + ($extraTime * (2000 / 30)), 2);
        }
    }

    /**
     * Calculate total amount based on actual duration and studio rate
     * IMPORTANT: Charges minimum base amount for any duration up to base time
     */
    private function calculateTotalAmount($studio, $actualDuration)
    {
        // Get the studio's rate information
        $rate = $studio->rate;
        
        // Determine base time and calculate minimum charge
        if ($rate) {
            $baseTime = $rate->base_time; // e.g., 30 minutes
            $baseAmount = $rate->base_amount; // e.g., 1500 for 30min at 3000/hr
            
            // If actual duration is less than or equal to base time, charge base amount
            if ($actualDuration <= $baseTime) {
                return $baseAmount;
            }
            
            // If exceeded base time, calculate extra charges
            $extraTime = $actualDuration - $baseTime;
            $ratePerMinute = $rate->per_minute_rate; // Already calculated in StudioRate model
            $extraCharge = $extraTime * $ratePerMinute;
            
            return round($baseAmount + $extraCharge, 2);
        }
        
        // Fallback to default rate if studio has no rate assigned
        $defaultRate = StudioRate::getDefault();
        if ($defaultRate) {
            $baseTime = $defaultRate->base_time;
            $baseAmount = $defaultRate->base_amount;
            
            if ($actualDuration <= $baseTime) {
                return $baseAmount;
            }
            
            $extraTime = $actualDuration - $baseTime;
            $ratePerMinute = $defaultRate->per_minute_rate;
            $extraCharge = $extraTime * $ratePerMinute;
            
            return round($baseAmount + $extraCharge, 2);
        }
        
        // Final fallback: 2000 for base 30 minutes
        $baseTime = 30;
        $baseAmount = 2000;
        
        if ($actualDuration <= $baseTime) {
            return $baseAmount;
        }
        
        $extraTime = $actualDuration - $baseTime;
        $ratePerMinute = $baseAmount / $baseTime; // 66.67 per minute
        $extraCharge = $extraTime * $ratePerMinute;
        
        return round($baseAmount + $extraCharge, 2);
    }

    /**
     * Store check-in
     */
    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'studio_id' => 'required|exists:studios,id',
            'expected_duration' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Find or create customer
            $customer = StudioCustomer::where('phone', $validated['customer_phone'])->first();
            
            if (!$customer) {
                $customer = StudioCustomer::create([
                    'name' => $validated['customer_name'],
                    'phone' => $validated['customer_phone'],
                    'email' => $validated['customer_email'] ?? null,
                ]);
            } else {
                // Update customer info if provided
                $customer->update([
                    'name' => $validated['customer_name'],
                    'email' => $validated['customer_email'] ?? $customer->email,
                ]);
            }

            // Check if studio is available
            $studio = Studio::findOrFail($validated['studio_id']);
            
            if (!$studio->isAvailable()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Studio is not available'
                ], 422);
            }

            // Calculate base amount based on studio's rate and expected duration
            $baseAmount = $this->calculateBaseAmount($studio, $validated['expected_duration']);

            // Create session
            $session = StudioSession::create([
                'studio_id' => $studio->id,
                'customer_id' => $customer->id,
                'check_in_time' => now(),
                'expected_duration' => $validated['expected_duration'],
                'base_amount' => $baseAmount,
                'total_amount' => $baseAmount,
                'status' => 'active',
            ]);

            // Update studio status
            $studio->markOccupied();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$customer->name} checked into {$studio->name}",
                'session' => $session->load(['studio', 'customer']),
                'qr_code' => $session->qr_code,
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
     * FIXED: Now properly calculates total based on actual duration using studio's rate
     */
    public function checkout(Request $request, $id)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,card,transfer,other',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $session = StudioSession::with(['studio', 'customer'])->findOrFail($id);

            if ($session->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Session is not active'
                ], 422);
            }

            // Calculate actual duration
            $checkInTime = Carbon::parse($session->check_in_time);
            $checkOutTime = now();
            $actualDuration = $checkInTime->diffInMinutes($checkOutTime);

            // Get studio for rate calculation
            $studio = $session->studio;

            // Recalculate total amount based on actual duration
            $totalAmount = $this->calculateTotalAmount($studio, $actualDuration);

            // Apply discount if provided
            $discountAmount = $validated['discount_amount'] ?? 0;
            $finalAmount = max(0, $totalAmount - $discountAmount);

            // Update session
            $session->update([
                'check_out_time' => $checkOutTime,
                'actual_duration' => $actualDuration,
                'total_amount' => $totalAmount,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'paid',
                'status' => 'completed',
            ]);

            // Create payment record
            $session->payments()->create([
                'amount' => $finalAmount,
                'payment_method' => $validated['payment_method'],
                'payment_date' => now(),
            ]);

            // Update studio status to available
            $studio->markAvailable();

            // Update customer statistics
            $customer = $session->customer;
            $customer->increment('total_sessions');
            $customer->increment('total_spent', $finalAmount);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checkout successful',
                'session' => $session->fresh()->load(['studio', 'customer', 'payments']),
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
     * Get session details
     */
    public function getSession($id)
    {
        $session = StudioSession::with(['studio', 'customer', 'payments'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'session' => $session,
            'currentDuration' => $session->getCurrentDuration(),
            'formattedDuration' => $session->formatted_duration,
            'isOvertime' => $session->isOvertime(),
        ]);
    }

    /**
     * Extend session
     */
    public function extendSession(Request $request, $id)
    {
        $validated = $request->validate([
            'additional_time' => 'required|integer|min:1',
        ]);

        $session = StudioSession::findOrFail($id);

        if ($session->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Can only extend active sessions'
            ], 422);
        }

        $session->expected_duration += $validated['additional_time'];
        $session->save();

        return response()->json([
            'success' => true,
            'message' => "Session extended by {$validated['additional_time']} minutes",
            'session' => $session->fresh(),
        ]);
    }

    /**
     * Get active sessions (AJAX)
     */
    public function getActiveSessions()
    {
        $sessions = StudioSession::with(['studio', 'customer'])
            ->active()
            ->orderBy('check_in_time', 'desc')
            ->get();

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
        $session = StudioSession::findOrFail($id);
        
        // In production, use a QR code library like SimpleSoftwareIO/simple-qrcode
        // For now, return the QR code data
        
        return response()->json([
            'success' => true,
            'qr_code' => $session->qr_code,
            'qr_data' => [
                'session_id' => $session->id,
                'session_code' => $session->session_code,
                'customer_name' => $session->customer->name,
                'studio_name' => $session->studio->name,
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
            ->with(['studio', 'customer'])
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'session' => $session,
            'can_checkout' => $session->status === 'active',
        ]);
    }
}