@extends('layouts.app')

@section('title', 'Prop Rental Dashboard')

@push('styles')
<link href="{{ asset('assets/css/prop-rental.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">
                    <i class="fas fa-chart-line me-2"></i>Prop Rental Dashboard
                </h1>
                <p class="page-subtitle">Real-time insights and analytics</p>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <select class="form-select" id="periodFilter" onchange="window.location.href='?period=' + this.value">
                        <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ $period === 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $period === 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="year" {{ $period === 'year' ? 'selected' : '' }}>This Year</option>
                    </select>
                    <a href="{{ route('prop-rental.reports') }}" class="btn btn-outline-primary">
                        <i class="fas fa-file-alt me-2"></i>Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #6366F1 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="text-muted text-uppercase small fw-semibold">Total Revenue</div>
                        <div class="bg-primary bg-opacity-10 rounded p-2">
                            <i class="fas fa-dollar-sign text-primary"></i>
                        </div>
                    </div>
                    <h2 class="mb-2 fw-bold">₦{{ number_format($metrics['total_revenue'], 0) }}</h2>
                    @if($metrics['revenue_change'] > 0)
                        <span class="badge bg-success bg-opacity-10 text-light">
                            <i class="fas fa-arrow-up me-1"></i>{{ $metrics['revenue_change'] }}% vs last period
                        </span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-light">
                            <i class="fas fa-arrow-down me-1"></i>{{ abs($metrics['revenue_change']) }}% vs last period
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #10B981 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="text-muted text-uppercase small fw-semibold">Active Rentals</div>
                        <div class="bg-success bg-opacity-10 rounded p-2">
                            <i class="fas fa-calendar-check text-success"></i>
                        </div>
                    </div>
                    <h2 class="mb-2 fw-bold">{{ $metrics['active_rentals'] }}</h2>
                    <div class="small text-muted">
                        {{ $metrics['utilization_rate'] }}% utilization rate
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #F59E0B !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="text-muted text-uppercase small fw-semibold">Completed</div>
                        <div class="bg-warning bg-opacity-10 rounded p-2">
                            <i class="fas fa-check-circle text-warning"></i>
                        </div>
                    </div>
                    <h2 class="mb-2 fw-bold">{{ $metrics['completed_rentals'] }}</h2>
                    <div class="small text-muted">
                        Avg duration: {{ $metrics['avg_duration'] }} days
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #EF4444 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="text-muted text-uppercase small fw-semibold">Due/Overdue</div>
                        <div class="bg-danger bg-opacity-10 rounded p-2">
                            <i class="fas fa-exclamation-triangle text-danger"></i>
                        </div>
                    </div>
                    <h2 class="mb-2 fw-bold">{{ $metrics['due_today'] + $metrics['overdue'] }}</h2>
                    <div class="small">
                        <span class="text-danger fw-semibold">{{ $metrics['overdue'] }}</span> overdue · 
                        <span class="text-warning fw-semibold">{{ $metrics['due_today'] }}</span> due today
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <!-- Revenue Trend -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold">Revenue Trend</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Rental Status Distribution -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold">Rental Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="200"></canvas>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small">Active</span>
                            <span class="fw-semibold text-success">{{ $statusDistribution['active'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small">Completed</span>
                            <span class="fw-semibold text-primary">{{ $statusDistribution['completed'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small">Overdue</span>
                            <span class="fw-semibold text-danger">{{ $statusDistribution['overdue'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small">Cancelled</span>
                            <span class="fw-semibold text-muted">{{ $statusDistribution['cancelled'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row -->
    <div class="row g-3 mb-4">
        <!-- Popular Props -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold">Most Rented Props</h5>
                </div>
                <div class="card-body">
                    @php
                        $maxPopularRentals = max((int) ($popularProps->max('rentals_count') ?? 0), 1);
                    @endphp
                    @forelse($popularProps as $prop)
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 rounded p-3 me-3">
                                <i class="{{ $prop->image }} fa-2x text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $prop->name }}</div>
                                <small class="text-muted">{{ $prop->brand }} {{ $prop->model }}</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-primary">{{ $prop->rentals_count }}</div>
                                <small class="text-muted">rentals</small>
                            </div>
                        </div>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: {{ round(($prop->rentals_count / $maxPopularRentals) * 100, 1) }}%">
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-guitar fa-3x mb-3 opacity-50"></i>
                            <p>No rental data available</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Customers -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold">Top Customers</h5>
                </div>
                <div class="card-body">
                    @forelse($topCustomers as $index => $customer)
                        <div class="d-flex align-items-center p-3 mb-2 bg-light rounded">
                            <div class="me-3">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" 
                                     style="width: 45px; height: 45px;">
                                    {{ $customer->initials }}
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $customer->name }}</div>
                                <small class="text-muted">{{ $customer->total_rentals }} rentals</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success">{{ $customer->formatted_total_spent }}</div>
                                @if($index === 0)
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-crown"></i> Top
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-users fa-3x mb-3 opacity-50"></i>
                            <p>No customer data available</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Alerts -->
    <div class="row g-3">
        <!-- Recent Activity -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold">Recent Activity</h5>
                </div>
                <div class="card-body">
                    @forelse($recentActivity as $activity)
                        <div class="d-flex align-items-start p-3 mb-2 border-start border-3 border-{{ $activity->activity_color }} bg-light rounded-end">
                            <div class="me-3">
                                <i class="fas {{ $activity->activity_icon }} text-{{ $activity->activity_color }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $activity->activity_title }}</div>
                                <div class="small text-muted">{{ $activity->activity_description }}</div>
                            </div>
                            <div class="text-muted small">
                                {{ $activity->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-history fa-3x mb-3 opacity-50"></i>
                            <p>No recent activity</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Alerts & Notifications -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold">Alerts</h5>
                </div>
                <div class="card-body">
                    <!-- Overdue Rentals -->
                    @if($metrics['overdue'] > 0)
                        <div class="alert alert-danger mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                                <div>
                                    <div class="fw-bold">{{ $metrics['overdue'] }} Overdue Rentals</div>
                                    <small>Immediate action required</small>
                                </div>
                            </div>
                            <a href="{{ route('prop-rental.index', ['tab' => 'active-rentals']) }}" class="btn btn-sm btn-danger w-100 mt-2">
                                View Overdue
                            </a>
                        </div>
                    @endif

                    <!-- Due Today -->
                    @if($metrics['due_today'] > 0)
                        <div class="alert alert-warning mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock fa-2x me-3"></i>
                                <div>
                                    <div class="fw-bold">{{ $metrics['due_today'] }} Due Today</div>
                                    <small>Contact customers for returns</small>
                                </div>
                            </div>
                            <a href="{{ route('prop-rental.index', ['tab' => 'active-rentals']) }}" class="btn btn-sm btn-warning w-100 mt-2">
                                View Due Today
                            </a>
                        </div>
                    @endif

                    <!-- Props in Maintenance -->
                    @if($metrics['in_maintenance'] > 0)
                        <div class="alert alert-info mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-tools fa-2x me-3"></i>
                                <div>
                                    <div class="fw-bold">{{ $metrics['in_maintenance'] }} Props in Maintenance</div>
                                    <small>Currently unavailable for rent</small>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Low Availability -->
                    @if($metrics['utilization_rate'] > 80)
                        <div class="alert alert-success mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle fa-2x me-3"></i>
                                <div>
                                    <div class="fw-bold">High Utilization</div>
                                    <small>{{ $metrics['utilization_rate'] }}% of props are rented</small>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($metrics['overdue'] === 0 && $metrics['due_today'] === 0 && $metrics['in_maintenance'] === 0)
                        <div class="text-center py-4 text-success">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <p class="mb-0 fw-semibold">All Good!</p>
                            <small class="text-muted">No alerts at the moment</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Revenue Trend Chart
const revenueCtx = document.getElementById('revenueChart');
if (revenueCtx) {
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: @json($revenueLabels),
            datasets: [{
                label: 'Revenue (₦)',
                data: @json($revenueData),
                borderColor: '#6366F1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₦' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

// Status Distribution Chart
const statusCtx = document.getElementById('statusChart');
if (statusCtx) {
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Completed', 'Overdue', 'Cancelled'],
            datasets: [{
                data: [
                    {{ $statusDistribution['active'] }},
                    {{ $statusDistribution['completed'] }},
                    {{ $statusDistribution['overdue'] }},
                    {{ $statusDistribution['cancelled'] }}
                ],
                backgroundColor: [
                    '#10B981',
                    '#6366F1',
                    '#EF4444',
                    '#9CA3AF'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}
</script>
@endpush
@endsection
