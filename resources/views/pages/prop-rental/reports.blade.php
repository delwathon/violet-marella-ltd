{{-- resources/views/pages/prop-rental/reports.blade.php --}}
@extends('layouts.app')

@section('title', 'Prop Rental Reports')

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
                    <i class="fas fa-file-alt me-2"></i>Rental Reports
                </h1>
                <p class="page-subtitle">Comprehensive analytics and insights</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('prop-rental.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('prop-rental.reports') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Date Range</label>
                        <select class="form-select" name="range" onchange="this.form.submit()">
                            <option value="today" {{ $range === 'today' ? 'selected' : '' }}>Today</option>
                            <option value="week" {{ $range === 'week' ? 'selected' : '' }}>This Week</option>
                            <option value="month" {{ $range === 'month' ? 'selected' : '' }}>This Month</option>
                            <option value="year" {{ $range === 'year' ? 'selected' : '' }}>This Year</option>
                            <option value="custom" {{ $range === 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>
                    @if($range === 'custom')
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Start Date</label>
                            <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">End Date</label>
                            <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">Apply</button>
                        </div>
                    @endif
                    <div class="col-md-3 ms-auto">
                        <label class="form-label small fw-semibold">&nbsp;</label>
                        <a href="{{ route('prop-rental.rentals.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success w-100">
                            <i class="fas fa-download me-2"></i>Export CSV
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">Total Rentals</div>
                    <h2 class="mb-0 fw-bold text-primary">{{ $summary['total_rentals'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">Total Revenue</div>
                    <h2 class="mb-0 fw-bold text-success">₦{{ number_format($summary['total_revenue'], 0) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">Avg Rental Value</div>
                    <h2 class="mb-0 fw-bold text-info">₦{{ number_format($summary['avg_rental_value'], 0) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">Avg Duration</div>
                    <h2 class="mb-0 fw-bold text-warning">{{ $summary['avg_duration'] }} days</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold">Revenue Over Time</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueOverTimeChart" height="80"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold">Category Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Tables -->
    <div class="row g-3 mb-4">
        <!-- Revenue by Prop -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">Revenue by Prop</h5>
                    <span class="badge bg-primary">Top {{ count($revenueByProp) }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Prop</th>
                                    <th class="text-end">Rentals</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($revenueByProp as $item)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $item->prop_name }}</div>
                                            <small class="text-muted">{{ $item->category }}</small>
                                        </td>
                                        <td class="text-end">{{ $item->rental_count }}</td>
                                        <td class="text-end fw-bold text-success">₦{{ number_format($item->total_revenue, 0) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">No data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue by Customer -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">Revenue by Customer</h5>
                    <span class="badge bg-success">Top {{ count($revenueByCustomer) }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th class="text-end">Rentals</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($revenueByCustomer as $item)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $item->customer_name }}</div>
                                            <small class="text-muted">{{ $item->customer_phone }}</small>
                                        </td>
                                        <td class="text-end">{{ $item->rental_count }}</td>
                                        <td class="text-end fw-bold text-success">₦{{ number_format($item->total_revenue, 0) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">No data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold">Performance Metrics</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="border-start border-primary border-4 ps-3">
                                <div class="text-muted small mb-1">Completion Rate</div>
                                <div class="h4 mb-0 fw-bold">{{ $metrics['completion_rate'] }}%</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $metrics['completion_rate'] }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-start border-success border-4 ps-3">
                                <div class="text-muted small mb-1">On-Time Returns</div>
                                <div class="h4 mb-0 fw-bold">{{ $metrics['ontime_return_rate'] }}%</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: {{ $metrics['ontime_return_rate'] }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-start border-warning border-4 ps-3">
                                <div class="text-muted small mb-1">Repeat Customer Rate</div>
                                <div class="h4 mb-0 fw-bold">{{ $metrics['repeat_customer_rate'] }}%</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $metrics['repeat_customer_rate'] }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-start border-info border-4 ps-3">
                                <div class="text-muted small mb-1">Cancellation Rate</div>
                                <div class="h4 mb-0 fw-bold">{{ $metrics['cancellation_rate'] }}%</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-info" style="width: {{ $metrics['cancellation_rate'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rental History Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-semibold">Rental History</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover rental-table">
                    <thead>
                        <tr>
                            <th>Rental ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Prop</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rentals as $rental)
                            <tr>
                                <td>
                                    <a href="{{ route('prop-rental.rentals.show', $rental->id) }}" class="text-decoration-none fw-semibold">
                                        {{ strtoupper($rental->rental_id) }}
                                    </a>
                                </td>
                                <td>{{ $rental->created_at->format('d M Y') }}</td>
                                <td>
                                    <div>{{ $rental->customer->name }}</div>
                                    <small class="text-muted">{{ $rental->customer->phone }}</small>
                                </td>
                                <td>{{ $rental->prop->name }}</td>
                                <td>{{ $rental->duration }} days</td>
                                <td><span class="badge {{ $rental->status_badge_class }}">{{ $rental->status_display }}</span></td>
                                <td class="text-end fw-bold">{{ $rental->formatted_total_amount }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                                    No rentals found for this period
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($rentals->hasPages())
                <div class="mt-3">
                    {{ $rentals->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Revenue Over Time Chart
const revenueTimeCtx = document.getElementById('revenueOverTimeChart');
if (revenueTimeCtx) {
    new Chart(revenueTimeCtx, {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Revenue (₦)',
                data: @json($chartData),
                backgroundColor: '#6366F1',
                borderRadius: 8
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

// Category Distribution Chart
const categoryCtx = document.getElementById('categoryChart');
if (categoryCtx) {
    new Chart(categoryCtx, {
        type: 'pie',
        data: {
            labels: @json($categoryLabels),
            datasets: [{
                data: @json($categoryData),
                backgroundColor: [
                    '#6366F1',
                    '#10B981',
                    '#F59E0B',
                    '#EF4444',
                    '#8B5CF6'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}
</script>
@endpush
@endsection