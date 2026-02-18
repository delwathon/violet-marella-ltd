@extends('layouts.app')
@section('title', 'Photo Studio Dashboard')
@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title mb-1">Photo Studio Dashboard</h1>
                <p class="text-muted mb-0">Overview of studio operations and performance</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkInModal">
                    <i class="fas fa-user-plus me-2"></i>New Check-in
                </button>
                <a href="{{ route('photo-studio.sessions.active') }}" class="btn btn-outline-primary">
                    <i class="fas fa-list me-2"></i>View Sessions
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Today's Sessions -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Today's Sessions</div>
                    <div class="stat-value">{{ $todayStats['totalSessions'] }}</div>
                    <div class="stat-detail">
                        <span class="text-success">{{ $todayStats['activeSessions'] }} Active</span>
                        <span class="text-muted ms-2">{{ $todayStats['completedSessions'] }} Completed</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Revenue -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Today's Revenue</div>
                    <div class="stat-value">₦{{ number_format($todayStats['revenue'], 2) }}</div>
                    <div class="stat-detail">
                        @if($todayStats['pendingPayment'] > 0)
                        <span class="text-warning">₦{{ number_format($todayStats['pendingPayment'], 2) }} Pending</span>
                        @else
                        <span class="text-success">All payments received</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Hours -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Hours</div>
                    <div class="stat-value">{{ $todayStats['totalHours'] }}</div>
                    <div class="stat-detail">
                        <span class="text-muted">{{ $todayStats['totalMinutes'] }} minutes total</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Sessions -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Active Now</div>
                    <div class="stat-value">{{ $activeSessions }}</div>
                    <div class="stat-detail">
                        <span class="text-muted">{{ $studios->where('status', 'occupied')->count() }} studios occupied</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Studio Status Cards -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Studio Status</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($studios as $studio)
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="studio-status-card" data-studio-id="{{ $studio->id }}">
                                <div class="studio-header">
                                    <h5 class="mb-0">{{ $studio->name }}</h5>
                                    <span class="status-badge {{ $studio->status }}">
                                        {{ ucfirst($studio->status) }}
                                    </span>
                                </div>
                                <div class="studio-info">
                                    @if($studio->status === 'occupied' && $studio->activeSession)
                                        @php
                                            $session = $studio->activeSession;
                                            $duration = $session->getCurrentDuration();
                                            $hours = floor($duration / 60);
                                            $minutes = $duration % 60;
                                            $durationText = $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
                                        @endphp
                                        <div class="customer-name">{{ $session->customer->name }}</div>
                                        <div class="session-time">Started: {{ $session->check_in_time->format('g:i A') }}</div>
                                        <div class="duration">Duration: {{ $durationText }}</div>
                                    @elseif($studio->status === 'available')
                                        <div class="empty-state">
                                            <i class="fas fa-door-open"></i>
                                            <div>Ready for next customer</div>
                                        </div>
                                    @else
                                        <div class="empty-state">
                                            <i class="fas fa-tools"></i>
                                            <div>Under maintenance</div>
                                        </div>
                                    @endif
                                </div>
                                <div class="studio-actions">
                                    @if($studio->status === 'occupied' && $studio->activeSession)
                                        <button class="btn btn-sm btn-outline-primary" onclick="viewSessionDetails({{ $studio->activeSession->id }})">View</button>
                                        <button class="btn btn-sm btn-success" onclick="showCheckoutModal({{ $studio->activeSession->id }})">Checkout</button>
                                    @elseif($studio->status === 'available')
                                        <button class="btn btn-sm btn-primary w-100" onclick="selectStudio({{ $studio->id }})" data-bs-toggle="modal" data-bs-target="#checkInModal">Check-in</button>
                                    @else
                                        <button class="btn btn-sm btn-outline-secondary w-100" disabled>Unavailable</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Revenue Chart -->
        <div class="col-lg-8 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Revenue Trend (Last 7 Days)</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Occupancy Chart -->
        <div class="col-lg-4 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Studio Occupancy Today</h5>
                </div>
                <div class="card-body">
                    @foreach($occupancyData as $data)
                    <div class="occupancy-item mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">{{ $data['studio'] }}</span>
                            <span class="text-muted">{{ $data['rate'] }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $data['rate'] }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Top Customers -->
    <div class="row">
        <!-- Recent Sessions -->
        <div class="col-lg-7 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Sessions</h5>
                    <a href="{{ route('photo-studio.sessions.history') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentSessions->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>No recent sessions</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Studio</th>
                                        <th>Duration</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentSessions as $session)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="customer-avatar-sm me-2">{{ $session->customer->initials }}</div>
                                                <div>
                                                    <div class="fw-semibold">{{ $session->customer->name }}</div>
                                                    <small class="text-muted">{{ $session->customer->phone }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $session->studio->name }}</td>
                                        <td>{{ $session->actual_duration }} min</td>
                                        <td>₦{{ number_format($session->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $session->payment_status === 'paid' ? 'success' : 'warning' }}">
                                                {{ ucfirst($session->payment_status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Customers -->
        <div class="col-lg-5 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Top Customers</h5>
                    <a href="{{ route('photo-studio.customers.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($topCustomers->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <p>No customers yet</p>
                        </div>
                    @else
                        @foreach($topCustomers as $customer)
                        <div class="top-customer-item">
                            <div class="d-flex align-items-center">
                                <div class="customer-avatar me-3">{{ $customer->initials }}</div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $customer->name }}</div>
                                    <small class="text-muted">{{ $customer->total_sessions }} sessions</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-success">₦{{ number_format($customer->total_spent, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Modals -->
@include('pages.photo-studio.modals.check-in')
@include('pages.photo-studio.modals.checkout')
@include('pages.photo-studio.modals.session-details')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('assets/js/photo-studio.js') }}"></script>
<script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        const revenueData = @json($revenueData);
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: revenueData.map(d => d.date),
                datasets: [{
                    label: 'Revenue (₦)',
                    data: revenueData.map(d => d.revenue),
                    borderColor: '#6f42c1',
                    backgroundColor: 'rgba(111, 66, 193, 0.1)',
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
</script>
@endpush
@endsection