<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Prop;
use App\Models\PropRental;
use App\Models\Product;
use App\Models\RentalCustomer;
use App\Models\Sale;
use App\Models\StoreCustomer;
use App\Models\StoreProduct;
use App\Models\StoreSale;
use App\Models\StudioCustomer;
use App\Models\StudioSession;
use App\Support\BusinessProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * @var array<string, array<string, string>>
     */
    private const BUSINESS_META = [
        'lounge' => [
            'name' => 'Mini Lounge',
            'icon' => 'shopping-cart',
            'color' => 'success',
            'route' => 'lounge.index',
            'trend_key' => 'lounge',
            'chart_border' => '#10b981',
            'chart_bg' => 'rgba(16, 185, 129, 0.14)',
            'today_label' => 'transactions',
        ],
        'gift_store' => [
            'name' => 'Anire Craft Store',
            'icon' => 'gift',
            'color' => 'danger',
            'route' => 'anire-craft-store.index',
            'trend_key' => 'store',
            'chart_border' => '#ef4444',
            'chart_bg' => 'rgba(239, 68, 68, 0.14)',
            'today_label' => 'sales',
        ],
        'photo_studio' => [
            'name' => 'Photo Studio',
            'icon' => 'camera',
            'color' => 'primary',
            'route' => 'photo-studio.index',
            'trend_key' => 'studio',
            'chart_border' => '#2563eb',
            'chart_bg' => 'rgba(37, 99, 235, 0.14)',
            'today_label' => 'sessions',
        ],
        'prop_rental' => [
            'name' => 'Prop Rental',
            'icon' => 'guitar',
            'color' => 'warning',
            'route' => 'prop-rental.index',
            'trend_key' => 'props',
            'chart_border' => '#f59e0b',
            'chart_bg' => 'rgba(245, 158, 11, 0.14)',
            'today_label' => 'rentals',
        ],
    ];

    public function index(Request $request)
    {
        $user = Auth::guard('user')->user();
        $availableBusinesses = $this->orderBusinessSlugs($user->accessibleBusinessSlugs());

        if ($availableBusinesses === []) {
            abort(403, 'No business access has been assigned to your account.');
        }

        [$dashboardMode, $scopeBusinesses, $selectedBusiness, $canSwitchScope] = $this->resolveScope(
            $user,
            $availableBusinesses,
            (string) $request->query('business', '')
        );

        $periods = $this->periods();
        $businessModules = [];

        foreach ($scopeBusinesses as $businessSlug) {
            $businessModules[$businessSlug] = $this->buildBusinessModule($businessSlug, $periods);
        }

        $stats = $this->buildAggregateStats($businessModules);
        $todaySummary = collect($businessModules)
            ->map(function (array $module): array {
                return [
                    'slug' => $module['slug'],
                    'name' => $module['name'],
                    'color' => $module['hex_color'],
                    'revenue' => $module['today_revenue'],
                    'count' => $module['today_count'],
                    'label' => $module['today_label'],
                ];
            })
            ->values()
            ->all();

        $revenueTrend = $this->buildRevenueTrend($scopeBusinesses);
        $chartDatasets = $this->buildChartDatasets($scopeBusinesses);
        $recentActivities = $this->getRecentActivities($scopeBusinesses);
        $scopeBusinessesMeta = array_map(fn (string $slug): array => [
            'slug' => $slug,
            'name' => $this->businessName($slug),
        ], $availableBusinesses);

        $roleContext = $this->buildRoleContext(
            $user,
            $dashboardMode,
            $scopeBusinesses,
            $availableBusinesses,
            $selectedBusiness,
            $stats,
            $canSwitchScope
        );

        return view('pages.dashboard', [
            'user' => $user,
            'stats' => $stats,
            'businessModules' => $businessModules,
            'todaySummary' => $todaySummary,
            'revenueTrend' => $revenueTrend,
            'chartDatasets' => $chartDatasets,
            'recentActivities' => $recentActivities,
            'dashboardMode' => $dashboardMode,
            'selectedBusiness' => $selectedBusiness,
            'scopeBusinessesMeta' => $scopeBusinessesMeta,
            'roleContext' => $roleContext,
            'canSwitchScope' => $canSwitchScope,
            'canViewReports' => $user->hasPermission('reports.view'),
        ]);
    }

    /**
     * @return array{
     *     today:Carbon,
     *     now:Carbon,
     *     start_of_month:Carbon,
     *     last_month_start:Carbon,
     *     last_month_end:Carbon,
     *     start_of_week:Carbon,
     *     last_week_start:Carbon,
     *     last_week_end:Carbon
     * }
     */
    private function periods(): array
    {
        $now = now();

        return [
            'today' => Carbon::today(),
            'now' => $now,
            'start_of_month' => $now->copy()->startOfMonth(),
            'last_month_start' => $now->copy()->subMonth()->startOfMonth(),
            'last_month_end' => $now->copy()->subMonth()->endOfMonth(),
            'start_of_week' => $now->copy()->startOfWeek(),
            'last_week_start' => $now->copy()->subWeek()->startOfWeek(),
            'last_week_end' => $now->copy()->subWeek()->endOfWeek(),
        ];
    }

    /**
     * @param array<int, string> $availableBusinesses
     * @return array{0:string,1:array<int, string>,2:string,3:bool}
     */
    private function resolveScope(User $user, array $availableBusinesses, string $requestedBusiness): array
    {
        $requested = trim($requestedBusiness);
        $selectedBusiness = in_array($requested, $availableBusinesses, true)
            ? $requested
            : 'all';

        if ($user->isSuperAdmin() || $user->role === 'admin') {
            if ($selectedBusiness !== 'all') {
                return ['superadmin', [$selectedBusiness], $selectedBusiness, true];
            }

            return ['superadmin', $availableBusinesses, 'all', count($availableBusinesses) > 1];
        }

        if ($user->role === 'manager') {
            if ($selectedBusiness !== 'all') {
                return ['manager', [$selectedBusiness], $selectedBusiness, count($availableBusinesses) > 1];
            }

            return ['manager', $availableBusinesses, 'all', count($availableBusinesses) > 1];
        }

        if (count($availableBusinesses) === 1) {
            $selectedBusiness = $availableBusinesses[0];
            return ['staff', [$selectedBusiness], $selectedBusiness, false];
        }

        if ($selectedBusiness !== 'all') {
            return ['staff', [$selectedBusiness], $selectedBusiness, true];
        }

        return ['staff', $availableBusinesses, 'all', true];
    }

    /**
     * @param array{
     *     today:Carbon,
     *     now:Carbon,
     *     start_of_month:Carbon,
     *     last_month_start:Carbon,
     *     last_month_end:Carbon,
     *     start_of_week:Carbon,
     *     last_week_start:Carbon,
     *     last_week_end:Carbon
     * } $periods
     * @return array<string, mixed>
     */
    private function buildBusinessModule(string $businessSlug, array $periods): array
    {
        $meta = self::BUSINESS_META[$businessSlug];
        $today = $periods['today'];
        $now = $periods['now'];
        $startOfMonth = $periods['start_of_month'];
        $lastMonthStart = $periods['last_month_start'];
        $lastMonthEnd = $periods['last_month_end'];
        $startOfWeek = $periods['start_of_week'];
        $lastWeekStart = $periods['last_week_start'];
        $lastWeekEnd = $periods['last_week_end'];

        $module = [
            'slug' => $businessSlug,
            'name' => $this->businessName($businessSlug),
            'icon' => $meta['icon'],
            'color' => $meta['color'],
            'hex_color' => $meta['chart_border'],
            'route' => $meta['route'],
            'trend_key' => $meta['trend_key'],
            'today_label' => $meta['today_label'],
            'revenue' => 0.0,
            'revenue_previous' => 0.0,
            'transactions' => 0,
            'transactions_previous' => 0,
            'products' => 0,
            'low_stock' => 0,
            'customers_total' => 0,
            'customers' => 0,
            'pending' => 0,
            'active_sessions' => 0,
            'active_rentals' => 0,
            'props' => 0,
            'overdue' => 0,
            'today_revenue' => 0.0,
            'today_count' => 0,
            'alerts' => 0,
            'highlights' => [],
        ];

        switch ($businessSlug) {
            case 'lounge':
                $module['revenue'] = (float) Sale::completed()->whereBetween('sale_date', [$startOfMonth, $now])->sum('total_amount');
                $module['revenue_previous'] = (float) Sale::completed()->whereBetween('sale_date', [$lastMonthStart, $lastMonthEnd])->sum('total_amount');
                $module['transactions'] = (int) Sale::completed()->where('sale_date', '>=', $startOfWeek)->count();
                $module['transactions_previous'] = (int) Sale::completed()->whereBetween('sale_date', [$lastWeekStart, $lastWeekEnd])->count();
                $module['products'] = (int) Product::active()->count();
                $module['low_stock'] = (int) Product::active()->lowStock()->count();
                $module['customers_total'] = (int) Customer::count();
                $module['customers'] = (int) Customer::whereHas('sales', fn ($query) => $query->where('sale_date', '>=', $startOfMonth))->count();
                $module['pending'] = (int) Sale::where('payment_status', 'pending')->count();
                $module['today_revenue'] = (float) Sale::completed()->whereDate('sale_date', $today)->sum('total_amount');
                $module['today_count'] = (int) Sale::completed()->whereDate('sale_date', $today)->count();
                $module['alerts'] = $module['low_stock'] + $module['pending'];
                $module['highlights'] = [
                    ['label' => 'Transactions', 'value' => $module['transactions'], 'tone' => 'default'],
                    ['label' => 'Products', 'value' => $module['products'], 'tone' => 'default'],
                    ['label' => 'Low Stock', 'value' => $module['low_stock'], 'tone' => 'warning'],
                    ['label' => 'Pending Payments', 'value' => $module['pending'], 'tone' => 'warning'],
                ];
                break;

            case 'gift_store':
                $module['revenue'] = (float) StoreSale::completed()->whereBetween('sale_date', [$startOfMonth, $now])->sum('total_amount');
                $module['revenue_previous'] = (float) StoreSale::completed()->whereBetween('sale_date', [$lastMonthStart, $lastMonthEnd])->sum('total_amount');
                $module['transactions'] = (int) StoreSale::completed()->where('sale_date', '>=', $startOfWeek)->count();
                $module['transactions_previous'] = (int) StoreSale::completed()->whereBetween('sale_date', [$lastWeekStart, $lastWeekEnd])->count();
                $module['products'] = (int) StoreProduct::active()->count();
                $module['low_stock'] = (int) StoreProduct::lowStock()->count();
                $module['customers_total'] = (int) StoreCustomer::count();
                $module['customers'] = (int) StoreCustomer::whereHas('sales', fn ($query) => $query->where('sale_date', '>=', $startOfMonth))->count();
                $module['pending'] = (int) StoreSale::where('payment_status', 'pending')->count();
                $module['today_revenue'] = (float) StoreSale::completed()->whereDate('sale_date', $today)->sum('total_amount');
                $module['today_count'] = (int) StoreSale::completed()->whereDate('sale_date', $today)->count();
                $module['alerts'] = $module['low_stock'] + $module['pending'];
                $module['highlights'] = [
                    ['label' => 'Sales', 'value' => $module['transactions'], 'tone' => 'default'],
                    ['label' => 'Products', 'value' => $module['products'], 'tone' => 'default'],
                    ['label' => 'Low Stock', 'value' => $module['low_stock'], 'tone' => 'warning'],
                    ['label' => 'Pending Payments', 'value' => $module['pending'], 'tone' => 'warning'],
                ];
                break;

            case 'photo_studio':
                $module['revenue'] = (float) StudioSession::completed()
                    ->whereBetween('check_out_time', [$startOfMonth, $now])
                    ->where('payment_status', 'paid')
                    ->sum('total_amount');
                $module['revenue_previous'] = (float) StudioSession::completed()
                    ->whereBetween('check_out_time', [$lastMonthStart, $lastMonthEnd])
                    ->where('payment_status', 'paid')
                    ->sum('total_amount');
                $module['transactions'] = (int) StudioSession::completed()->where('check_out_time', '>=', $startOfWeek)->count();
                $module['transactions_previous'] = (int) StudioSession::completed()->whereBetween('check_out_time', [$lastWeekStart, $lastWeekEnd])->count();
                $module['customers_total'] = (int) StudioCustomer::count();
                $module['customers'] = (int) StudioCustomer::whereHas('sessions', fn ($query) => $query->where('check_in_time', '>=', $startOfMonth))->count();
                $module['pending'] = (int) StudioSession::where('payment_status', 'pending')->count();
                $module['active_sessions'] = (int) StudioSession::active()->count();
                $module['today_revenue'] = (float) StudioSession::completed()
                    ->whereDate('check_out_time', $today)
                    ->where('payment_status', 'paid')
                    ->sum('total_amount');
                $module['today_count'] = (int) StudioSession::whereDate('check_in_time', $today)->count();
                $module['alerts'] = $module['pending'];
                $module['highlights'] = [
                    ['label' => 'Sessions', 'value' => $module['transactions'], 'tone' => 'default'],
                    ['label' => 'Active Sessions', 'value' => $module['active_sessions'], 'tone' => 'success'],
                    ['label' => 'Active Customers', 'value' => $module['customers'], 'tone' => 'default'],
                    ['label' => 'Pending Payment', 'value' => $module['pending'], 'tone' => 'warning'],
                ];
                break;

            case 'prop_rental':
                $module['revenue'] = (float) PropRental::completed()->whereBetween('created_at', [$startOfMonth, $now])->sum('total_amount');
                $module['revenue_previous'] = (float) PropRental::completed()->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->sum('total_amount');
                $module['transactions'] = (int) PropRental::where('created_at', '>=', $startOfWeek)->count();
                $module['transactions_previous'] = (int) PropRental::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->count();
                $module['props'] = (int) Prop::count();
                $module['products'] = $module['props'];
                $module['low_stock'] = (int) Prop::where('status', 'maintenance')->count();
                $module['customers_total'] = (int) RentalCustomer::count();
                $module['customers'] = (int) RentalCustomer::whereHas('rentals', fn ($query) => $query->where('created_at', '>=', $startOfMonth))->count();
                $module['overdue'] = (int) PropRental::overdue()->count();
                $module['pending'] = $module['overdue'];
                $module['active_rentals'] = (int) PropRental::active()->count();
                $module['today_revenue'] = (float) PropRental::whereDate('created_at', $today)->sum('total_amount');
                $module['today_count'] = (int) PropRental::whereDate('created_at', $today)->count();
                $module['alerts'] = $module['overdue'] + $module['low_stock'];
                $module['highlights'] = [
                    ['label' => 'Rentals', 'value' => $module['transactions'], 'tone' => 'default'],
                    ['label' => 'Active Rentals', 'value' => $module['active_rentals'], 'tone' => 'success'],
                    ['label' => 'Overdue', 'value' => $module['overdue'], 'tone' => 'danger'],
                    ['label' => 'Maintenance Props', 'value' => $module['low_stock'], 'tone' => 'warning'],
                ];
                break;
        }

        return $module;
    }

    /**
     * @param array<string, array<string, mixed>> $businessModules
     * @return array<string, float|int>
     */
    private function buildAggregateStats(array $businessModules): array
    {
        $totalRevenue = (float) array_sum(array_map(fn (array $module): float => (float) ($module['revenue'] ?? 0), $businessModules));
        $lastMonthRevenue = (float) array_sum(array_map(fn (array $module): float => (float) ($module['revenue_previous'] ?? 0), $businessModules));
        $totalTransactions = (int) array_sum(array_map(fn (array $module): int => (int) ($module['transactions'] ?? 0), $businessModules));
        $lastWeekTransactions = (int) array_sum(array_map(fn (array $module): int => (int) ($module['transactions_previous'] ?? 0), $businessModules));
        $totalLowStock = (int) array_sum(array_map(fn (array $module): int => (int) ($module['low_stock'] ?? 0), $businessModules));
        $totalPending = (int) array_sum(array_map(fn (array $module): int => (int) ($module['pending'] ?? 0), $businessModules));

        return [
            'total_revenue' => $totalRevenue,
            'revenue_change' => $this->calculatePercentageChange($totalRevenue, $lastMonthRevenue),
            'total_transactions' => $totalTransactions,
            'transaction_change' => $this->calculatePercentageChange($totalTransactions, $lastWeekTransactions),
            'total_products' => (int) array_sum(array_map(fn (array $module): int => (int) ($module['products'] ?? 0), $businessModules)),
            'total_low_stock' => $totalLowStock,
            'total_customers' => (int) array_sum(array_map(fn (array $module): int => (int) ($module['customers_total'] ?? 0), $businessModules)),
            'active_customers' => (int) array_sum(array_map(fn (array $module): int => (int) ($module['customers'] ?? 0), $businessModules)),
            'total_pending' => $totalPending,
            'total_alerts' => $totalLowStock + $totalPending,
            'today_revenue' => (float) array_sum(array_map(fn (array $module): float => (float) ($module['today_revenue'] ?? 0), $businessModules)),
            'today_count' => (int) array_sum(array_map(fn (array $module): int => (int) ($module['today_count'] ?? 0), $businessModules)),
        ];
    }

    /**
     * @param array<int, string> $scopeBusinesses
     * @return array<int, array<string, float|string>>
     */
    private function buildRevenueTrend(array $scopeBusinesses): array
    {
        $trend = [];

        for ($index = 6; $index >= 0; $index--) {
            $date = now()->subDays($index);
            $dateString = $date->toDateString();

            $row = [
                'date' => $date->format('M d'),
                'lounge' => 0.0,
                'store' => 0.0,
                'studio' => 0.0,
                'props' => 0.0,
                'total' => 0.0,
            ];

            foreach ($scopeBusinesses as $businessSlug) {
                $meta = self::BUSINESS_META[$businessSlug];
                $value = $this->fetchRevenueForDay($businessSlug, $dateString);
                $row[$meta['trend_key']] = $value;
                $row['total'] += $value;
            }

            $trend[] = $row;
        }

        return $trend;
    }

    private function fetchRevenueForDay(string $businessSlug, string $dateString): float
    {
        switch ($businessSlug) {
            case 'lounge':
                return (float) Sale::completed()->whereDate('sale_date', $dateString)->sum('total_amount');
            case 'gift_store':
                return (float) StoreSale::completed()->whereDate('sale_date', $dateString)->sum('total_amount');
            case 'photo_studio':
                return (float) StudioSession::completed()
                    ->whereDate('check_out_time', $dateString)
                    ->where('payment_status', 'paid')
                    ->sum('total_amount');
            case 'prop_rental':
                return (float) PropRental::completed()->whereDate('created_at', $dateString)->sum('total_amount');
            default:
                return 0.0;
        }
    }

    /**
     * @param array<int, string> $scopeBusinesses
     * @return array<int, array<string, string>>
     */
    private function buildChartDatasets(array $scopeBusinesses): array
    {
        $datasets = [];

        foreach ($scopeBusinesses as $businessSlug) {
            $meta = self::BUSINESS_META[$businessSlug];
            $datasets[] = [
                'label' => $this->businessName($businessSlug),
                'key' => $meta['trend_key'],
                'borderColor' => $meta['chart_border'],
                'backgroundColor' => $meta['chart_bg'],
            ];
        }

        return $datasets;
    }

    /**
     * @param array<int, string> $scopeBusinesses
     * @return array<int, array<string, mixed>>
     */
    private function getRecentActivities(array $scopeBusinesses): array
    {
        $activities = collect();

        if (in_array('lounge', $scopeBusinesses, true)) {
            $activities = $activities->merge(
                Sale::with('customer')
                    ->latest('sale_date')
                    ->limit(3)
                    ->get()
                    ->map(function (Sale $sale): array {
                        return [
                            'business_slug' => 'lounge',
                            'business_name' => $this->businessName('lounge'),
                            'icon' => self::BUSINESS_META['lounge']['icon'],
                            'color' => self::BUSINESS_META['lounge']['color'],
                            'title' => 'Lounge Sale',
                            'description' => 'Sale to ' . ($sale->customer->full_name ?? 'Walk-in Customer'),
                            'amount' => (float) $sale->total_amount,
                            'time' => $sale->sale_date,
                        ];
                    })
            );
        }

        if (in_array('gift_store', $scopeBusinesses, true)) {
            $activities = $activities->merge(
                StoreSale::with('customer')
                    ->latest('sale_date')
                    ->limit(3)
                    ->get()
                    ->map(function (StoreSale $sale): array {
                        return [
                            'business_slug' => 'gift_store',
                            'business_name' => $this->businessName('gift_store'),
                            'icon' => self::BUSINESS_META['gift_store']['icon'],
                            'color' => self::BUSINESS_META['gift_store']['color'],
                            'title' => 'Store Sale',
                            'description' => 'Sale to ' . ($sale->customer->name ?? 'Walk-in Customer'),
                            'amount' => (float) $sale->total_amount,
                            'time' => $sale->sale_date,
                        ];
                    })
            );
        }

        if (in_array('photo_studio', $scopeBusinesses, true)) {
            $activities = $activities->merge(
                StudioSession::with('customer')
                    ->latest('check_in_time')
                    ->limit(3)
                    ->get()
                    ->map(function (StudioSession $session): array {
                        return [
                            'business_slug' => 'photo_studio',
                            'business_name' => $this->businessName('photo_studio'),
                            'icon' => self::BUSINESS_META['photo_studio']['icon'],
                            'color' => self::BUSINESS_META['photo_studio']['color'],
                            'title' => 'Studio Session',
                            'description' => 'Session for ' . ($session->customer->name ?? 'Unknown customer'),
                            'amount' => (float) ($session->total_amount ?? 0),
                            'time' => $session->check_in_time ?? $session->created_at,
                        ];
                    })
            );
        }

        if (in_array('prop_rental', $scopeBusinesses, true)) {
            $activities = $activities->merge(
                PropRental::with(['customer', 'prop'])
                    ->latest()
                    ->limit(3)
                    ->get()
                    ->map(function (PropRental $rental): array {
                        return [
                            'business_slug' => 'prop_rental',
                            'business_name' => $this->businessName('prop_rental'),
                            'icon' => self::BUSINESS_META['prop_rental']['icon'],
                            'color' => self::BUSINESS_META['prop_rental']['color'],
                            'title' => 'Prop Rental',
                            'description' => ($rental->customer->name ?? 'Customer') . ' - ' . ($rental->prop->name ?? 'Prop'),
                            'amount' => (float) $rental->total_amount,
                            'time' => $rental->created_at,
                        ];
                    })
            );
        }

        return $activities
            ->sortByDesc('time')
            ->take(10)
            ->values()
            ->all();
    }

    /**
     * @param array<int, string> $scopeBusinesses
     * @param array<int, string> $availableBusinesses
     * @param array<string, float|int> $stats
     * @return array<string, mixed>
     */
    private function buildRoleContext(
        User $user,
        string $dashboardMode,
        array $scopeBusinesses,
        array $availableBusinesses,
        string $selectedBusiness,
        array $stats,
        bool $canSwitchScope
    ): array {
        $scopeLabel = $selectedBusiness === 'all'
            ? 'All assigned businesses'
            : $this->businessName($selectedBusiness);

        if ($dashboardMode === 'superadmin') {
            return [
                'title' => 'Super Admin Dashboard',
                'subtitle' => $selectedBusiness === 'all'
                    ? 'Executive overview across all business units'
                    : 'Focused view: ' . $scopeLabel,
                'kpis' => [
                    ['label' => 'Businesses In View', 'value' => (string) count($scopeBusinesses)],
                    ['label' => 'Active Users', 'value' => (string) User::where('is_active', true)->count()],
                    ['label' => 'Open Alerts', 'value' => (string) $stats['total_alerts']],
                ],
                'scope_label' => $scopeLabel,
                'can_switch_scope' => $canSwitchScope,
            ];
        }

        if ($dashboardMode === 'manager') {
            return [
                'title' => 'Manager Dashboard',
                'subtitle' => $selectedBusiness === 'all'
                    ? 'Portfolio view for businesses assigned to you'
                    : 'Managed business focus: ' . $scopeLabel,
                'kpis' => [
                    ['label' => 'Managed Businesses', 'value' => (string) count($availableBusinesses)],
                    ['label' => 'Team Members', 'value' => (string) $this->countActiveTeamMembers($availableBusinesses, $user->id)],
                    ['label' => 'Open Alerts', 'value' => (string) $stats['total_alerts']],
                ],
                'scope_label' => $scopeLabel,
                'can_switch_scope' => $canSwitchScope,
            ];
        }

        return [
            'title' => 'Operations Dashboard',
            'subtitle' => $selectedBusiness === 'all'
                ? 'Daily operations across your assigned businesses'
                : 'Daily operations for ' . $scopeLabel,
            'kpis' => [
                ['label' => 'Assigned Businesses', 'value' => (string) count($availableBusinesses)],
                ['label' => 'Role', 'value' => ucfirst(str_replace('_', ' ', $user->role))],
                ['label' => 'Today Activity', 'value' => number_format((int) $stats['today_count'])],
            ],
            'scope_label' => $scopeLabel,
            'can_switch_scope' => $canSwitchScope,
        ];
    }

    /**
     * @param array<int, string> $scopeBusinesses
     */
    private function countActiveTeamMembers(array $scopeBusinesses, int $excludeUserId): int
    {
        return User::query()
            ->where('is_active', true)
            ->where('id', '!=', $excludeUserId)
            ->whereHas('businesses', function ($query) use ($scopeBusinesses) {
                $query->whereIn('slug', $scopeBusinesses);
            })
            ->distinct('users.id')
            ->count('users.id');
    }

    private function businessName(string $businessSlug): string
    {
        $fallback = self::BUSINESS_META[$businessSlug]['name']
            ?? ucfirst(str_replace('_', ' ', $businessSlug));
        $name = BusinessProfile::forSlug($businessSlug)['name'] ?? '';

        return trim((string) $name) !== '' ? (string) $name : $fallback;
    }

    /**
     * @param array<int, string> $slugs
     * @return array<int, string>
     */
    private function orderBusinessSlugs(array $slugs): array
    {
        $ordered = [];

        foreach (array_keys(self::BUSINESS_META) as $knownSlug) {
            if (in_array($knownSlug, $slugs, true)) {
                $ordered[] = $knownSlug;
            }
        }

        foreach ($slugs as $slug) {
            if (!in_array($slug, $ordered, true)) {
                $ordered[] = $slug;
            }
        }

        return $ordered;
    }

    private function calculatePercentageChange(float|int $current, float|int $previous): float
    {
        if ((float) $previous === 0.0) {
            return (float) $current > 0 ? 100.0 : 0.0;
        }

        return (((float) $current - (float) $previous) / (float) $previous) * 100;
    }
}
