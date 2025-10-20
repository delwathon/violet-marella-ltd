<?php

namespace App\Http\Controllers\PropRental;

use App\Http\Controllers\Controller;
use App\Models\Prop;
use App\Models\RentalCustomer;
use App\Models\PropRental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PropRentalController extends Controller
{
    /**
     * Display prop rental dashboard with analytics
     */
    public function dashboard(Request $request)
    {
        $user = Auth::guard('user')->user();
        $period = $request->get('period', 'month');
        
        // Date ranges based on period
        $dateRange = $this->getDateRange($period);
        $previousDateRange = $this->getPreviousDateRange($period);
        
        // Calculate metrics
        $currentRevenue = PropRental::whereBetween('created_at', $dateRange)->sum('total_amount');
        $previousRevenue = PropRental::whereBetween('created_at', $previousDateRange)->sum('total_amount');
        $revenueChange = $previousRevenue > 0 ? round((($currentRevenue - $previousRevenue) / $previousRevenue) * 100, 1) : 0;
        
        $metrics = [
            'total_revenue' => $currentRevenue,
            'revenue_change' => $revenueChange,
            'active_rentals' => PropRental::active()->count(),
            'completed_rentals' => PropRental::whereBetween('created_at', $dateRange)->where('status', 'completed')->count(),
            'due_today' => PropRental::dueToday()->count(),
            'overdue' => PropRental::overdue()->count(),
            'in_maintenance' => Prop::where('status', 'maintenance')->count(),
            'utilization_rate' => $this->calculateUtilizationRate(),
            'avg_duration' => round(PropRental::whereBetween('created_at', $dateRange)->avg(DB::raw('DATEDIFF(end_date, start_date)')), 1),
        ];
        
        // Revenue trend data
        $revenueTrend = $this->getRevenueTrend($period);
        $revenueLabels = $revenueTrend->pluck('label')->toArray();
        $revenueData = $revenueTrend->pluck('revenue')->toArray();
        
        // Status distribution
        $statusDistribution = [
            'active' => PropRental::where('status', 'active')->count(),
            'completed' => PropRental::where('status', 'completed')->count(),
            'overdue' => PropRental::overdue()->count(),
            'cancelled' => PropRental::where('status', 'cancelled')->count(),
        ];
        
        // Popular props
        $popularProps = Prop::withCount(['rentals' => function($query) use ($dateRange) {
                $query->whereBetween('created_at', $dateRange);
            }])
            ->having('rentals_count', '>', 0)
            ->orderBy('rentals_count', 'desc')
            ->limit(5)
            ->get();
        
        // Top customers
        $topCustomers = RentalCustomer::orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();
        
        // Recent activity
        $recentActivity = PropRental::with(['prop', 'customer'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function($rental) {
                return (object)[
                    'activity_title' => $this->getActivityTitle($rental),
                    'activity_description' => $this->getActivityDescription($rental),
                    'activity_icon' => $this->getActivityIcon($rental),
                    'activity_color' => $this->getActivityColor($rental),
                    'created_at' => $rental->created_at,
                ];
            });
        
        return view('pages.prop-rental.dashboard', compact(
            'user',
            'period',
            'metrics',
            'revenueLabels',
            'revenueData',
            'statusDistribution',
            'popularProps',
            'topCustomers',
            'recentActivity'
        ));
    }
    
    /**
     * Get date range based on period
     */
    private function getDateRange($period)
    {
        switch ($period) {
            case 'today':
                return [Carbon::today(), Carbon::tomorrow()];
            case 'week':
                return [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
            case 'month':
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
            case 'year':
                return [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()];
            default:
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
        }
    }
    
    /**
     * Get previous date range for comparison
     */
    private function getPreviousDateRange($period)
    {
        switch ($period) {
            case 'today':
                return [Carbon::yesterday(), Carbon::today()];
            case 'week':
                return [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()];
            case 'month':
                return [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()];
            case 'year':
                return [Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()];
            default:
                return [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()];
        }
    }
    
    /**
     * Calculate utilization rate
     */
    private function calculateUtilizationRate()
    {
        $totalProps = Prop::count();
        $rentedProps = Prop::where('status', 'rented')->count();
        
        return $totalProps > 0 ? round(($rentedProps / $totalProps) * 100, 1) : 0;
    }
    
    /**
     * Get revenue trend data
     */
    private function getRevenueTrend($period)
    {
        if ($period === 'today') {
            // Hourly data for today
            return collect(range(0, 23))->map(function($hour) {
                $start = Carbon::today()->setHour($hour);
                $end = Carbon::today()->setHour($hour + 1);
                
                return [
                    'label' => $start->format('ha'),
                    'revenue' => PropRental::whereBetween('created_at', [$start, $end])->sum('total_amount')
                ];
            });
        } elseif ($period === 'week') {
            // Daily data for this week
            return collect(range(0, 6))->map(function($day) {
                $date = Carbon::now()->startOfWeek()->addDays($day);
                
                return [
                    'label' => $date->format('D'),
                    'revenue' => PropRental::whereDate('created_at', $date)->sum('total_amount')
                ];
            });
        } elseif ($period === 'month') {
            // Daily data for this month
            $daysInMonth = Carbon::now()->daysInMonth;
            return collect(range(1, $daysInMonth))->map(function($day) {
                $date = Carbon::now()->startOfMonth()->addDays($day - 1);
                
                return [
                    'label' => $date->format('j'),
                    'revenue' => PropRental::whereDate('created_at', $date)->sum('total_amount')
                ];
            });
        } else {
            // Monthly data for this year
            return collect(range(1, 12))->map(function($month) {
                return [
                    'label' => Carbon::create(null, $month)->format('M'),
                    'revenue' => PropRental::whereYear('created_at', Carbon::now()->year)
                        ->whereMonth('created_at', $month)
                        ->sum('total_amount')
                ];
            });
        }
    }
    
    /**
     * Get activity title
     */
    private function getActivityTitle($rental)
    {
        switch ($rental->status) {
            case 'active':
                return 'New Rental Created';
            case 'completed':
                return 'Rental Completed';
            case 'cancelled':
                return 'Rental Cancelled';
            default:
                return 'Rental Updated';
        }
    }
    
    /**
     * Get activity description
     */
    private function getActivityDescription($rental)
    {
        return "{$rental->customer->name} rented {$rental->prop->name}";
    }
    
    /**
     * Get activity icon
     */
    private function getActivityIcon($rental)
    {
        switch ($rental->status) {
            case 'active':
                return 'fa-plus-circle';
            case 'completed':
                return 'fa-check-circle';
            case 'cancelled':
                return 'fa-times-circle';
            default:
                return 'fa-circle';
        }
    }
    
    /**
     * Get activity color
     */
    private function getActivityColor($rental)
    {
        switch ($rental->status) {
            case 'active':
                return 'success';
            case 'completed':
                return 'primary';
            case 'cancelled':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    /**
     * Display prop rental dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::guard('user')->user();
        
        // Get filter parameters
        $category = $request->get('category', 'all');
        $activeTab = $request->get('tab', 'props');
        
        // Get statistics
        $stats = [
            'total_props' => Prop::count(),
            'currently_rented' => Prop::where('status', 'rented')->count(),
            'due_today' => PropRental::dueToday()->count(),
            'monthly_revenue' => PropRental::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('total_amount'),
        ];
        
        // Get props with filtering
        $propsQuery = Prop::query();
        if ($category !== 'all') {
            $propsQuery->where('category', $category);
        }
        $props = $propsQuery->get();
        
        // Get active rentals
        $activeRentals = PropRental::with(['prop', 'customer'])
            ->active()
            ->latest()
            ->get();
        
        // Get due today
        $dueToday = PropRental::with(['prop', 'customer'])
            ->dueToday()
            ->get();
        
        // Get customers
        $customers = RentalCustomer::latest()->get();
        
        // Get available props and customers for dropdowns
        $availableProps = Prop::available()->get();
        $activeCustomers = RentalCustomer::where('status', 'active')->get();
        
        return view('pages.prop-rental.index', compact(
            'user',
            'stats',
            'props',
            'activeRentals',
            'dueToday',
            'customers',
            'availableProps',
            'activeCustomers',
            'category',
            'activeTab'
        ));
    }

    /**
     * Display reports page
     */
    public function reports(Request $request)
    {
        $user = Auth::guard('user')->user();
        $range = $request->get('range', 'month');
        
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        if ($range !== 'custom') {
            [$start, $end] = $this->getDateRange($range);
            $startDate = $start->format('Y-m-d');
            $endDate = $end->format('Y-m-d');
        }
        
        // Summary data
        $rentalsQuery = PropRental::with(['prop', 'customer'])
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        $summary = [
            'total_rentals' => $rentalsQuery->count(),
            'total_revenue' => $rentalsQuery->sum('total_amount'),
            'avg_rental_value' => $rentalsQuery->avg('total_amount'),
            'avg_duration' => round($rentalsQuery->avg(DB::raw('DATEDIFF(end_date, start_date)')), 1),
        ];
        
        // Revenue by prop
        $revenueByProp = PropRental::with('prop')
            ->select('prop_id', DB::raw('COUNT(*) as rental_count'), DB::raw('SUM(total_amount) as total_revenue'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('prop_id')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return (object)[
                    'prop_name' => $item->prop->name,
                    'category' => ucfirst($item->prop->category),
                    'rental_count' => $item->rental_count,
                    'total_revenue' => $item->total_revenue,
                ];
            });
        
        // Revenue by customer
        $revenueByCustomer = PropRental::with('customer')
            ->select('rental_customer_id', DB::raw('COUNT(*) as rental_count'), DB::raw('SUM(total_amount) as total_revenue'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('rental_customer_id')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return (object)[
                    'customer_name' => $item->customer->name,
                    'customer_phone' => $item->customer->phone,
                    'rental_count' => $item->rental_count,
                    'total_revenue' => $item->total_revenue,
                ];
            });
        
        // Performance metrics
        $totalRentals = PropRental::whereBetween('created_at', [$startDate, $endDate])->count();
        $completedRentals = PropRental::whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed')->count();
        $ontimeReturns = PropRental::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->whereRaw('returned_at <= end_date')
            ->count();
        $repeatCustomers = RentalCustomer::where('total_rentals', '>', 1)->count();
        $totalCustomers = RentalCustomer::count();
        $cancelledRentals = PropRental::whereBetween('created_at', [$startDate, $endDate])->where('status', 'cancelled')->count();
        
        $metrics = [
            'completion_rate' => $totalRentals > 0 ? round(($completedRentals / $totalRentals) * 100, 1) : 0,
            'ontime_return_rate' => $completedRentals > 0 ? round(($ontimeReturns / $completedRentals) * 100, 1) : 0,
            'repeat_customer_rate' => $totalCustomers > 0 ? round(($repeatCustomers / $totalCustomers) * 100, 1) : 0,
            'cancellation_rate' => $totalRentals > 0 ? round(($cancelledRentals / $totalRentals) * 100, 1) : 0,
        ];
        
        // Chart data
        $chartTrend = $this->getRevenueTrend($range === 'custom' ? 'month' : $range);
        $chartLabels = $chartTrend->pluck('label')->toArray();
        $chartData = $chartTrend->pluck('revenue')->toArray();
        
        // Category distribution
        $categoryDistribution = PropRental::with('prop')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy('prop.category')
            ->map(function($group) {
                return $group->count();
            });
        
        $categoryLabels = $categoryDistribution->keys()->map(function($cat) {
            return ucfirst($cat);
        })->toArray();
        $categoryData = $categoryDistribution->values()->toArray();
        
        // Rental history (paginated)
        $rentals = PropRental::with(['prop', 'customer'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->paginate(20);
        
        return view('pages.prop-rental.reports', compact(
            'user',
            'range',
            'startDate',
            'endDate',
            'summary',
            'revenueByProp',
            'revenueByCustomer',
            'metrics',
            'chartLabels',
            'chartData',
            'categoryLabels',
            'categoryData',
            'rentals'
        ));
    }

    /**
     * Show create rental form
     */
    public function create()
    {
        $user = Auth::guard('user')->user();
        $availableProps = Prop::available()->get();
        $customers = RentalCustomer::where('status', 'active')->get();
        
        return view('pages.prop-rental.create', compact('user', 'availableProps', 'customers'));
    }

    /**
     * Store a new rental
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:rental_customers,id',
            'prop_id' => 'required|exists:props,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'security_deposit' => 'required|numeric|min:0',
            'agreement_signed' => 'required|accepted',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $prop = Prop::findOrFail($request->prop_id);
            $customer = RentalCustomer::findOrFail($request->customer_id);

            if (!$prop->isAvailable()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'This prop is not available for rent.');
            }

            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $days = $startDate->diffInDays($endDate);
            $totalAmount = $days * $prop->daily_rate;

            $rental = PropRental::create([
                'prop_id' => $prop->id,
                'rental_customer_id' => $customer->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'daily_rate' => $prop->daily_rate,
                'total_amount' => $totalAmount,
                'security_deposit' => $request->security_deposit,
                'amount_paid' => $request->amount_paid ?? 0,
                'notes' => $request->notes,
                'agreement_signed' => true,
                'status' => 'active',
                'created_by' => Auth::guard('user')->id(),
            ]);

            $prop->update(['status' => 'rented']);
            $customer->incrementRentalStats($totalAmount);

            DB::commit();

            return redirect()->route('prop-rental.index', ['tab' => 'active-rentals'])
                ->with('success', 'Rental created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create rental: ' . $e->getMessage());
        }
    }

    /**
     * Show rental details
     */
    public function show($id)
    {
        $user = Auth::guard('user')->user();
        $rental = PropRental::with(['prop', 'customer', 'creator'])
            ->findOrFail($id);
        
        return view('pages.prop-rental.show', compact('user', 'rental'));
    }

    /**
     * Show extend rental form
     */
    public function editExtend($id)
    {
        $user = Auth::guard('user')->user();
        $rental = PropRental::with(['prop', 'customer'])->findOrFail($id);
        
        if ($rental->status !== 'active') {
            return redirect()->route('prop-rental.index')
                ->with('error', 'Only active rentals can be extended.');
        }
        
        return view('pages.prop-rental.extend', compact('user', 'rental'));
    }

    /**
     * Process rental extension
     */
    public function extend(Request $request, $id)
    {
        $request->validate([
            'additional_days' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $rental = PropRental::findOrFail($id);

            if ($rental->status !== 'active') {
                return redirect()->back()
                    ->with('error', 'Only active rentals can be extended.');
            }

            $additionalDays = $request->additional_days;
            $additionalAmount = $additionalDays * $rental->daily_rate;

            $rental->end_date = $rental->end_date->addDays($additionalDays);
            $rental->total_amount += $additionalAmount;
            $rental->save();

            $rental->customer->increment('total_spent', $additionalAmount);

            DB::commit();

            return redirect()->route('prop-rental.index', ['tab' => 'active-rentals'])
                ->with('success', "Rental extended by {$additionalDays} day(s). Additional charge: ₦" . number_format($additionalAmount, 2));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to extend rental: ' . $e->getMessage());
        }
    }

    /**
     * Show return confirmation
     */
    public function showReturn($id)
    {
        $user = Auth::guard('user')->user();
        $rental = PropRental::with(['prop', 'customer'])->findOrFail($id);
        
        if ($rental->status !== 'active') {
            return redirect()->route('prop-rental.index')
                ->with('error', 'This rental is not active.');
        }
        
        return view('pages.prop-rental.return', compact('user', 'rental'));
    }

    /**
     * Process prop return
     */
    public function returnProp(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $rental = PropRental::findOrFail($id);

            if ($rental->status !== 'active') {
                return redirect()->back()
                    ->with('error', 'This rental is not active.');
            }

            $rental->status = 'completed';
            $rental->returned_at = Carbon::now();
            $rental->save();

            $rental->prop->update(['status' => 'available']);
            $rental->customer->decrementCurrentRentals();

            DB::commit();

            return redirect()->route('prop-rental.index', ['tab' => 'active-rentals'])
                ->with('success', 'Prop returned successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to return prop: ' . $e->getMessage());
        }
    }

    /**
     * Show cancel confirmation
     */
    public function showCancel($id)
    {
        $user = Auth::guard('user')->user();
        $rental = PropRental::with(['prop', 'customer'])->findOrFail($id);
        
        if ($rental->status !== 'active') {
            return redirect()->route('prop-rental.index')
                ->with('error', 'Only active rentals can be cancelled.');
        }
        
        return view('pages.prop-rental.cancel', compact('user', 'rental'));
    }

    /**
     * Cancel rental
     */
    public function cancel($id)
    {
        try {
            DB::beginTransaction();

            $rental = PropRental::findOrFail($id);

            if ($rental->status !== 'active') {
                return redirect()->back()
                    ->with('error', 'Only active rentals can be cancelled.');
            }

            $rental->status = 'cancelled';
            $rental->save();

            $rental->prop->update(['status' => 'available']);
            $rental->customer->decrementCurrentRentals();

            DB::commit();

            return redirect()->route('prop-rental.index', ['tab' => 'active-rentals'])
                ->with('success', 'Rental cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to cancel rental: ' . $e->getMessage());
        }
    }

    /**
     * Export rentals to CSV
     */
    public function export(Request $request)
    {
        $rentals = PropRental::with(['prop', 'customer'])
            ->when($request->status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->get();
        
        $filename = 'prop-rentals-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($rentals) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Rental ID', 'Customer', 'Phone', 'Prop', 'Start Date', 'End Date', 
                'Daily Rate', 'Total Amount', 'Security Deposit', 'Status', 'Notes'
            ]);
            
            foreach ($rentals as $rental) {
                fputcsv($file, [
                    $rental->rental_id,
                    $rental->customer->name,
                    $rental->customer->phone,
                    $rental->prop->name,
                    $rental->start_date->format('Y-m-d'),
                    $rental->end_date->format('Y-m-d'),
                    $rental->daily_rate,
                    $rental->total_amount,
                    $rental->security_deposit,
                    $rental->status_display,
                    $rental->notes ?? '',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get calendar data (AJAX helper)
     */
    public function calendarData(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);
        
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        
        $rentals = PropRental::with(['prop', 'customer'])
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->where('status', 'active')
            ->get();
        
        return response()->json([
            'success' => true,
            'rentals' => $rentals,
        ]);
    }
}