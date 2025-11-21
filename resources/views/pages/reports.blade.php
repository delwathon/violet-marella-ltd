@extends('layouts.app')
@section('title', 'All Business Reports - Violet Marella Limited')

@push('styles')
<style>
    /* Reports Page Styles */
    .filter-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        margin-bottom: 2rem;
    }
    
    .summary-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-box {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        position: relative;
        overflow: hidden;
    }
    
    .stat-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, var(--primary-violet), var(--secondary-violet));
    }
    
    .stat-box.revenue::before {
        background: linear-gradient(180deg, #10b981, #059669);
    }
    
    .stat-box.transactions::before {
        background: linear-gradient(180deg, #ef4444, #dc2626);
    }
    
    .stat-box.customers::before {
        background: linear-gradient(180deg, #f59e0b, #d97706);
    }
    
    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        color: #6b7280;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .stat-change {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        font-weight: 600;
    }
    
    .stat-change.positive {
        color: #10b981;
    }
    
    .stat-change.negative {
        color: #ef4444;
    }
    
    .chart-container {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        margin-bottom: 2rem;
    }
    
    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f3f4f6;
    }
    
    .chart-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }
    
    .business-comparison-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }
    
    .business-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        border: 2px solid #f3f4f6;
        transition: all 0.3s ease;
    }
    
    .business-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }
    
    .business-card-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin: 0 auto 1rem;
    }
    
    .business-card-revenue {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    
    .business-card-label {
        color: #6b7280;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.75rem;
    }
    
    .business-card-transactions {
        color: #9ca3af;
        font-size: 0.85rem;
    }
    
    .top-performers-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .performer-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 0.75rem;
        background: #f9fafb;
        transition: all 0.2s ease;
    }
    
    .performer-item:hover {
        background: #f3f4f6;
        transform: translateX(4px);
    }
    
    .performer-rank {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6f42c1, #9333ea);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    
    .performer-info {
        flex: 1;
    }
    
    .performer-name {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }
    
    .performer-quantity {
        font-size: 0.85rem;
        color: #6b7280;
    }
    
    .performer-revenue {
        font-weight: 700;
        color: #10b981;
        white-space: nowrap;
    }
    
    .payment-status-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }
    
    .payment-card {
        text-align: center;
        padding: 1.5rem;
        border-radius: 12px;
        border: 2px solid #f3f4f6;
    }
    
    .payment-card-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .payment-card-label {
        color: #6b7280;
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .custom-date-inputs {
        display: none;
    }
    
    .custom-date-inputs.active {
        display: block;
    }
    
    .export-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .export-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    
    @media (max-width: 768px) {
        .summary-stats,
        .business-comparison-grid,
        .payment-status-grid {
            grid-template-columns: 1fr;
        }
        
        .stat-value {
            font-size: 2rem;
        }
        
        .chart-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">All Business Reports</h1>
                <p class="page-subtitle">Comprehensive analytics across all business units</p>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary export-button" onclick="exportReport('pdf')">
                        <i class="fas fa-file-pdf"></i>Export PDF
                    </button>
                    <button class="btn btn-primary export-button" onclick="exportReport('excel')">
                        <i class="fas fa-file-excel"></i>Export Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('reports.index') }}" id="filterForm">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Business Unit</label>
                    <select class="form-select" name="business_unit" onchange="this.form.submit()">
                        <option value="all" {{ $businessUnit === 'all' ? 'selected' : '' }}>All Business Units</option>
                        <option value="lounge" {{ $businessUnit === 'lounge' ? 'selected' : '' }}>Mini Lounge</option>
                        <option value="gift_store" {{ $businessUnit === 'gift_store' ? 'selected' : '' }}>Gift Store</option>
                        <option value="photo_studio" {{ $businessUnit === 'photo_studio' ? 'selected' : '' }}>Photo Studio</option>
                        <option value="prop_rental" {{ $businessUnit === 'prop_rental' ? 'selected' : '' }}>Prop Rental</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Date Range</label>
                    <select class="form-select" name="date_range" id="dateRangeSelect" onchange="toggleCustomDates()">
                        <option value="today" {{ $dateRange === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ $dateRange === 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $dateRange === 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="quarter" {{ $dateRange === 'quarter' ? 'selected' : '' }}>This Quarter</option>
                        <option value="year" {{ $dateRange === 'year' ? 'selected' : '' }}>This Year</option>
                        <option value="custom" {{ $dateRange === 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>
                
                <div class="col-md-6 custom-date-inputs {{ $dateRange === 'custom' ? 'active' : '' }}" id="customDateInputs">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="date" class="form-control" name="start_date" value="{{ $dates['start']->format('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">End Date</label>
                            <input type="date" class="form-control" name="end_date" value="{{ $dates['end']->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary Statistics -->
    <div class="summary-stats">
        <div class="stat-box revenue">
            <div class="stat-value">₦{{ number_format($statistics['current']['revenue'], 2) }}</div>
            <div class="stat-label">Total Revenue</div>
            <div class="stat-change {{ $statistics['changes']['revenue'] >= 0 ? 'positive' : 'negative' }}">
                <i class="fas fa-arrow-{{ $statistics['changes']['revenue'] >= 0 ? 'up' : 'down' }}"></i>
                <span>{{ number_format(abs($statistics['changes']['revenue']), 1) }}%</span>
                <span style="color: #9ca3af; font-weight: 400;">vs previous period</span>
            </div>
        </div>
        
        <div class="stat-box transactions">
            <div class="stat-value">{{ number_format($statistics['current']['transactions']) }}</div>
            <div class="stat-label">Total Transactions</div>
            <div class="stat-change {{ $statistics['changes']['transactions'] >= 0 ? 'positive' : 'negative' }}">
                <i class="fas fa-arrow-{{ $statistics['changes']['transactions'] >= 0 ? 'up' : 'down' }}"></i>
                <span>{{ number_format(abs($statistics['changes']['transactions']), 1) }}%</span>
                <span style="color: #9ca3af; font-weight: 400;">vs previous period</span>
            </div>
        </div>
        
        <div class="stat-box customers">
            <div class="stat-value">{{ number_format($statistics['current']['customers']) }}</div>
            <div class="stat-label">Unique Customers</div>
            <div class="stat-change {{ $statistics['changes']['customers'] >= 0 ? 'positive' : 'negative' }}">
                <i class="fas fa-arrow-{{ $statistics['changes']['customers'] >= 0 ? 'up' : 'down' }}"></i>
                <span>{{ number_format(abs($statistics['changes']['customers']), 1) }}%</span>
                <span style="color: #9ca3af; font-weight: 400;">vs previous period</span>
            </div>
        </div>
        
        <div class="stat-box">
            <div class="stat-value">₦{{ number_format($statistics['current']['avg_transaction'], 2) }}</div>
            <div class="stat-label">Avg Transaction Value</div>
            <div class="stat-change {{ $statistics['changes']['avg_transaction'] >= 0 ? 'positive' : 'negative' }}">
                <i class="fas fa-arrow-{{ $statistics['changes']['avg_transaction'] >= 0 ? 'up' : 'down' }}"></i>
                <span>{{ number_format(abs($statistics['changes']['avg_transaction']), 1) }}%</span>
                <span style="color: #9ca3af; font-weight: 400;">vs previous period</span>
            </div>
        </div>
    </div>

    <!-- Revenue Trend Chart -->
    <div class="chart-container">
        <div class="chart-header">
            <h2 class="chart-title">Revenue Trend</h2>
            <span class="text-muted">{{ $dates['start']->format('M d, Y') }} - {{ $dates['end']->format('M d, Y') }}</span>
        </div>
        <canvas id="revenueTrendChart" height="80"></canvas>
    </div>

    <!-- Business Unit Comparison -->
    @if($businessUnit === 'all')
    <div class="chart-container">
        <div class="chart-header">
            <h2 class="chart-title">Business Unit Performance</h2>
        </div>
        
        <div class="row mb-4">
            <div class="col-lg-6">
                <canvas id="businessRevenueChart" height="120"></canvas>
            </div>
            <div class="col-lg-6">
                <canvas id="businessTransactionsChart" height="120"></canvas>
            </div>
        </div>
        
        <div class="business-comparison-grid">
            @foreach($businessComparison as $business)
            <div class="business-card">
                <div class="business-card-icon" style="background: linear-gradient(135deg, {{ $business['color'] }}, {{ $business['color'] }}dd);">
                    <i class="fas fa-{{ 
                        $business['name'] === 'Mini Lounge' ? 'shopping-cart' : 
                        ($business['name'] === 'Gift Store' ? 'gift' : 
                        ($business['name'] === 'Photo Studio' ? 'camera' : 'guitar'))
                    }}"></i>
                </div>
                <div class="business-card-revenue">₦{{ number_format($business['revenue'], 2) }}</div>
                <div class="business-card-label">{{ $business['name'] }}</div>
                <div class="business-card-transactions">{{ $business['transactions'] }} transactions</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Top Performers & Customer Insights -->
    <div class="row">
        <!-- Top Performers -->
        @if(!empty($topPerformers))
        <div class="col-lg-6 mb-4">
            <div class="chart-container">
                <div class="chart-header">
                    <h2 class="chart-title">Top Performing Products</h2>
                </div>
                
                @if(isset($topPerformers['lounge']) && $topPerformers['lounge']->count() > 0)
                <div class="mb-4">
                    <h5 class="fw-semibold mb-3" style="color: #10b981;">
                        <i class="fas fa-shopping-cart me-2"></i>Mini Lounge
                    </h5>
                    <ul class="top-performers-list">
                        @foreach($topPerformers['lounge'] as $index => $product)
                        <li class="performer-item">
                            <span class="performer-rank">{{ $index + 1 }}</span>
                            <div class="performer-info">
                                <div class="performer-name">{{ $product->name }}</div>
                                <div class="performer-quantity">{{ $product->total_quantity }} units sold</div>
                            </div>
                            <span class="performer-revenue">₦{{ number_format($product->total_revenue, 2) }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                @if(isset($topPerformers['gift_store']) && $topPerformers['gift_store']->count() > 0)
                <div class="mb-4">
                    <h5 class="fw-semibold mb-3" style="color: #ef4444;">
                        <i class="fas fa-gift me-2"></i>Gift Store
                    </h5>
                    <ul class="top-performers-list">
                        @foreach($topPerformers['gift_store'] as $index => $product)
                        <li class="performer-item">
                            <span class="performer-rank">{{ $index + 1 }}</span>
                            <div class="performer-info">
                                <div class="performer-name">{{ $product->name }}</div>
                                <div class="performer-quantity">{{ $product->total_quantity }} units sold</div>
                            </div>
                            <span class="performer-revenue">₦{{ number_format($product->total_revenue, 2) }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Customer Insights & Payment Status -->
        <div class="col-lg-6 mb-4">
            <!-- Customer Insights -->
            <div class="chart-container mb-4">
                <div class="chart-header">
                    <h2 class="chart-title">Customer Insights</h2>
                </div>
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="stat-value" style="color: #10b981;">{{ $customerInsights['new_customers'] }}</div>
                        <div class="stat-label">New Customers</div>
                    </div>
                    <div class="col-6">
                        <div class="stat-value" style="color: #6f42c1;">{{ $statistics['current']['customers'] }}</div>
                        <div class="stat-label">Total Customers</div>
                    </div>
                </div>
            </div>
            
            <!-- Payment Status -->
            <div class="chart-container">
                <div class="chart-header">
                    <h2 class="chart-title">Payment Status</h2>
                </div>
                
                <div class="payment-status-grid">
                    <div class="payment-card" style="border-color: #10b981;">
                        <div class="payment-card-value" style="color: #10b981;">
                            ₦{{ number_format($paymentStatus['paid'], 2) }}
                        </div>
                        <div class="payment-card-label">Paid</div>
                        <div class="text-muted small mt-2">
                            {{ $paymentStatus['total'] > 0 ? number_format(($paymentStatus['paid'] / $paymentStatus['total']) * 100, 1) : 0 }}% of total
                        </div>
                    </div>
                    
                    <div class="payment-card" style="border-color: #f59e0b;">
                        <div class="payment-card-value" style="color: #f59e0b;">
                            ₦{{ number_format($paymentStatus['pending'], 2) }}
                        </div>
                        <div class="payment-card-label">Pending</div>
                        <div class="text-muted small mt-2">
                            {{ $paymentStatus['total'] > 0 ? number_format(($paymentStatus['pending'] / $paymentStatus['total']) * 100, 1) : 0 }}% of total
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <canvas id="paymentStatusChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Trend Chart
    const revenueTrendCtx = document.getElementById('revenueTrendChart');
    if (revenueTrendCtx) {
        const revenueTrendData = @json($revenueTrend);
        
        new Chart(revenueTrendCtx, {
            type: 'line',
            data: {
                labels: revenueTrendData.map(item => item.date),
                datasets: [{
                    label: 'Revenue',
                    data: revenueTrendData.map(item => item.revenue),
                    borderColor: '#6f42c1',
                    backgroundColor: 'rgba(111, 66, 193, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: ₦' + context.parsed.y.toLocaleString('en-NG', {
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
                            callback: function(value) {
                                return '₦' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Business Revenue Comparison Chart
    @if($businessUnit === 'all')
    const businessRevenueCtx = document.getElementById('businessRevenueChart');
    if (businessRevenueCtx) {
        const businessData = @json($businessComparison);
        
        new Chart(businessRevenueCtx, {
            type: 'doughnut',
            data: {
                labels: businessData.map(b => b.name),
                datasets: [{
                    data: businessData.map(b => b.revenue),
                    backgroundColor: businessData.map(b => b.color),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    title: {
                        display: true,
                        text: 'Revenue Distribution',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ₦' + context.parsed.toLocaleString('en-NG', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Business Transactions Comparison Chart
    const businessTransactionsCtx = document.getElementById('businessTransactionsChart');
    if (businessTransactionsCtx) {
        const businessData = @json($businessComparison);
        
        new Chart(businessTransactionsCtx, {
            type: 'bar',
            data: {
                labels: businessData.map(b => b.name),
                datasets: [{
                    label: 'Transactions',
                    data: businessData.map(b => b.transactions),
                    backgroundColor: businessData.map(b => b.color),
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Transaction Count',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    @endif
    
    // Payment Status Chart
    const paymentStatusCtx = document.getElementById('paymentStatusChart');
    if (paymentStatusCtx) {
        const paymentData = @json($paymentStatus);
        
        new Chart(paymentStatusCtx, {
            type: 'bar',
            data: {
                labels: ['Paid', 'Pending'],
                datasets: [{
                    data: [paymentData.paid, paymentData.pending],
                    backgroundColor: ['#10b981', '#f59e0b'],
                    borderRadius: 8
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₦' + context.parsed.x.toLocaleString('en-NG', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }
                    }
                },
                scales: {
                    x: {
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
});

function toggleCustomDates() {
    const select = document.getElementById('dateRangeSelect');
    const customInputs = document.getElementById('customDateInputs');
    
    if (select.value === 'custom') {
        customInputs.classList.add('active');
    } else {
        customInputs.classList.remove('active');
        document.getElementById('filterForm').submit();
    }
}

function exportReport(format) {
    alert('Export to ' + format.toUpperCase() + ' functionality will be implemented.');
    // Implement export functionality here
}
</script>
@endpush