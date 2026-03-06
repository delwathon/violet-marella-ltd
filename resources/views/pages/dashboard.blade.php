@extends('layouts.app')
@section('title', ($roleContext['title'] ?? 'Dashboard') . ' - ' . ($companyProfile['name'] ?? 'Violet Marella Limited'))

@push('styles')
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet">
<style>
    .dashboard-shell {
        display: grid;
        gap: 1.5rem;
    }

    .dashboard-hero {
        background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
        border-radius: 16px;
        padding: 1.5rem;
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .hero-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .hero-kpi {
        background: rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        padding: 0.75rem;
    }

    .hero-kpi .label {
        color: rgba(255, 255, 255, 0.75);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 0.25rem;
    }

    .hero-kpi .value {
        font-size: 1.1rem;
        font-weight: 700;
    }

    .scope-toolbar {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 0.75rem;
        align-items: center;
    }

    .today-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
    }

    .today-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.1rem;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.08);
        border-top: 4px solid transparent;
    }

    .today-card .value {
        font-size: 1.45rem;
        font-weight: 700;
        color: #111827;
    }

    .today-card .label {
        color: #64748b;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.35px;
    }

    .business-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1rem;
    }

    .business-card {
        border-radius: 14px;
        padding: 1.25rem;
        color: #fff;
        background: linear-gradient(135deg, #1f2937, #374151);
        border: 1px solid rgba(255, 255, 255, 0.1);
        position: relative;
        overflow: hidden;
    }

    .business-card::before {
        content: '';
        position: absolute;
        inset: 0 auto auto 0;
        width: 100%;
        height: 4px;
        background: var(--accent-color);
    }

    .business-highlights {
        margin-top: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1px solid rgba(255, 255, 255, 0.12);
    }

    .business-highlight {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
        padding: 0.22rem 0;
    }

    .business-highlight .label {
        color: rgba(255, 255, 255, 0.75);
    }

    .chart-card,
    .activity-card {
        background: #fff;
        border-radius: 14px;
        padding: 1.3rem;
        box-shadow: 0 2px 14px rgba(15, 23, 42, 0.08);
    }

    .activity-item {
        display: grid;
        grid-template-columns: 40px 1fr auto;
        gap: 0.75rem;
        align-items: center;
        border: 1px solid #eef2f7;
        border-radius: 10px;
        padding: 0.8rem;
        margin-bottom: 0.65rem;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .activity-meta {
        display: flex;
        gap: 0.45rem;
        flex-wrap: wrap;
        margin-bottom: 0.2rem;
    }
</style>
@endpush

@section('content')
<div class="content-area">
    <div class="dashboard-shell">
        <div class="dashboard-hero">
            <div class="scope-toolbar">
                <div>
                    <h1 class="h4 mb-1">{{ $roleContext['title'] }}</h1>
                    <p class="mb-0 text-white-50">{{ $roleContext['subtitle'] }}</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if($canViewReports)
                        <a href="{{ route('reports.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-chart-line me-1"></i>Reports
                        </a>
                    @endif
                    <span class="badge bg-light text-dark px-3 py-2">
                        Scope: {{ $roleContext['scope_label'] }}
                    </span>
                </div>
            </div>

            @if($canSwitchScope)
                <form method="GET" action="{{ route('dashboard') }}" class="row g-2 mt-3">
                    <div class="col-sm-6 col-lg-4">
                        <label for="business" class="form-label text-white-50 mb-1">Dashboard Scope</label>
                        <select id="business" name="business" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="all" {{ $selectedBusiness === 'all' ? 'selected' : '' }}>All assigned businesses</option>
                            @foreach($scopeBusinessesMeta as $businessOption)
                                <option value="{{ $businessOption['slug'] }}" {{ $selectedBusiness === $businessOption['slug'] ? 'selected' : '' }}>
                                    {{ $businessOption['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            @endif

            <div class="hero-kpi-grid">
                @foreach($roleContext['kpis'] as $kpi)
                    <div class="hero-kpi">
                        <div class="label">{{ $kpi['label'] }}</div>
                        <div class="value">{{ $kpi['value'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6 col-xl-3">
                <div class="stat-card h-100">
                    <div class="stat-value">₦{{ number_format($stats['total_revenue'], 2) }}</div>
                    <div class="stat-label">Revenue (This Month)</div>
                    <div class="stat-change {{ $stats['revenue_change'] >= 0 ? 'text-success' : 'text-danger' }}">
                        <i class="fas fa-arrow-{{ $stats['revenue_change'] >= 0 ? 'up' : 'down' }}"></i>
                        {{ number_format(abs($stats['revenue_change']), 1) }}% vs last month
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="stat-card h-100">
                    <div class="stat-value">{{ number_format($stats['total_transactions']) }}</div>
                    <div class="stat-label">Transactions (This Week)</div>
                    <div class="stat-change {{ $stats['transaction_change'] >= 0 ? 'text-success' : 'text-danger' }}">
                        <i class="fas fa-arrow-{{ $stats['transaction_change'] >= 0 ? 'up' : 'down' }}"></i>
                        {{ number_format(abs($stats['transaction_change']), 1) }}% vs last week
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="stat-card h-100">
                    <div class="stat-value">{{ number_format($stats['total_customers']) }}</div>
                    <div class="stat-label">Customers</div>
                    <div class="stat-change text-info">
                        <i class="fas fa-users"></i> {{ number_format($stats['active_customers']) }} active this month
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="stat-card h-100">
                    <div class="stat-value">{{ number_format($stats['total_alerts']) }}</div>
                    <div class="stat-label">Alerts</div>
                    <div class="stat-change {{ $stats['total_alerts'] > 0 ? 'text-warning' : 'text-success' }}">
                        @if($stats['total_alerts'] > 0)
                            <i class="fas fa-exclamation-triangle"></i> Needs attention
                        @else
                            <i class="fas fa-check-circle"></i> No current alerts
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="h6 mb-0">Today</h2>
                <small class="text-muted">{{ now()->format('l, F d, Y') }}</small>
            </div>
            <div class="today-grid">
                @foreach($todaySummary as $summary)
                    <div class="today-card" style="border-top-color: {{ $summary['color'] }};">
                        <div class="value">₦{{ number_format($summary['revenue'], 2) }}</div>
                        <div class="fw-semibold text-dark">{{ $summary['name'] }}</div>
                        <div class="label">{{ number_format($summary['count']) }} {{ $summary['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div>
            <h2 class="h6 mb-2">Businesses In Scope</h2>
            <div class="business-grid">
                @foreach($businessModules as $module)
                    <div class="business-card" style="--accent-color: {{ $module['hex_color'] }};">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="small text-white-50">{{ $module['name'] }}</div>
                                <div class="fs-5 fw-bold">₦{{ number_format($module['revenue'], 2) }}</div>
                                <div class="small text-white-50">Revenue (this month)</div>
                            </div>
                            <div class="text-white-50">
                                <i class="fas fa-{{ $module['icon'] }} fs-5"></i>
                            </div>
                        </div>

                        <div class="business-highlights">
                            @foreach($module['highlights'] as $highlight)
                                @php
                                    $toneClass = match($highlight['tone']) {
                                        'warning' => 'text-warning',
                                        'danger' => 'text-danger',
                                        'success' => 'text-success',
                                        default => 'text-white',
                                    };
                                @endphp
                                <div class="business-highlight">
                                    <span class="label">{{ $highlight['label'] }}</span>
                                    <span class="fw-semibold {{ $toneClass }}">{{ number_format($highlight['value']) }}</span>
                                </div>
                            @endforeach
                        </div>

                        <a href="{{ route($module['route']) }}" class="btn btn-light btn-sm w-100 mt-3">
                            Open {{ $module['name'] }}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="chart-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h6 mb-0">Revenue Trend (Last 7 Days)</h2>
            </div>
            <canvas id="revenueTrendChart" height="90"></canvas>
        </div>

        <div class="activity-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="h6 mb-0">Recent Activity</h2>
                @if($canViewReports)
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-primary btn-sm">View Reports</a>
                @endif
            </div>

            @forelse($recentActivities as $activity)
                @php
                    $activityTime = $activity['time'] instanceof \Carbon\CarbonInterface
                        ? $activity['time']
                        : \Carbon\Carbon::parse($activity['time']);
                    $gradient = match($activity['color']) {
                        'danger' => 'linear-gradient(135deg, #ef4444, #dc2626)',
                        'primary' => 'linear-gradient(135deg, #2563eb, #1d4ed8)',
                        'warning' => 'linear-gradient(135deg, #f59e0b, #d97706)',
                        default => 'linear-gradient(135deg, #10b981, #047857)',
                    };
                @endphp
                <div class="activity-item">
                    <div class="activity-icon" style="background: {{ $gradient }};">
                        <i class="fas fa-{{ $activity['icon'] }} text-white"></i>
                    </div>
                    <div>
                        <div class="activity-meta">
                            <span class="badge bg-light text-dark">{{ $activity['business_name'] }}</span>
                            <span class="fw-semibold text-dark">{{ $activity['title'] }}</span>
                        </div>
                        <div class="small text-muted">{{ $activity['description'] }}</div>
                        <div class="small text-muted">{{ $activityTime->diffForHumans() }}</div>
                    </div>
                    <div class="fw-semibold text-success">₦{{ number_format($activity['amount'], 2) }}</div>
                </div>
            @empty
                <div class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-2x mb-2" style="opacity: 0.35;"></i>
                    <p class="mb-0">No recent activity for this scope.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('revenueTrendChart');
    if (!ctx) {
        return;
    }

    const revenueTrendData = @json($revenueTrend);
    const chartDatasets = @json($chartDatasets);

    const datasets = chartDatasets.map((dataset) => ({
        label: dataset.label,
        data: revenueTrendData.map((item) => Number(item[dataset.key] || 0)),
        borderColor: dataset.borderColor,
        backgroundColor: dataset.backgroundColor,
        tension: 0.35,
        fill: true,
        pointRadius: 3,
        pointHoverRadius: 4
    }));

    if (datasets.length > 1) {
        datasets.push({
            label: 'Combined Revenue',
            data: revenueTrendData.map((item) => Number(item.total || 0)),
            borderColor: '#111827',
            backgroundColor: 'rgba(17, 24, 39, 0)',
            borderDash: [6, 6],
            tension: 0.25,
            fill: false,
            pointRadius: 0
        });
    }

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: revenueTrendData.map((item) => item.date),
            datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 14
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const value = Number(context.parsed.y || 0);
                            return context.dataset.label + ': ₦' + value.toLocaleString('en-NG', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return '₦' + Number(value).toLocaleString();
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.06)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>
@endpush
