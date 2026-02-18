@extends('layouts.app')
@section('title', 'All Business Overview - Violet Marella Limited')

@push('styles')
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet">
<style>
    /* Enhanced Dashboard Styles */
    .business-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .business-unit-card {
        background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
        border-radius: 16px;
        padding: 1.5rem;
        color: white;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .business-unit-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-violet), var(--secondary-violet));
    }
    
    .business-unit-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }
    
    .business-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }
    
    .business-stats {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .business-stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        font-size: 0.9rem;
    }
    
    .business-stat-label {
        color: rgba(255, 255, 255, 0.7);
    }
    
    .business-stat-value {
        font-weight: 600;
        color: white;
    }
    
    .revenue-chart-container {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }
    
    .activity-feed {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .activity-item {
        display: flex;
        align-items: flex-start;
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .activity-item:hover {
        background: #f9fafb;
        transform: translateX(4px);
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    
    .activity-details {
        flex: 1;
    }
    
    .activity-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
    }
    
    .activity-description {
        color: #6b7280;
        font-size: 0.85rem;
        margin-bottom: 0.25rem;
    }
    
    .activity-time {
        color: #9ca3af;
        font-size: 0.75rem;
    }
    
    .activity-amount {
        font-weight: 600;
        color: #059669;
        font-size: 0.9rem;
        white-space: nowrap;
        margin-left: 1rem;
    }
    
    .today-summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .today-summary-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        border-left: 4px solid;
    }
    
    .summary-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }
    
    .summary-label {
        color: #6b7280;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
    }
</style>
@endpush

@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">All Business Overview</h1>
                <p class="page-subtitle">Consolidated view across all business units</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('reports.index') }}" class="btn btn-primary">
                    <i class="fas fa-chart-line me-2"></i>Detailed Reports
                </a>
            </div>
        </div>
    </div>

    <!-- Main Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-value">₦{{ number_format($stats['total_revenue'], 2) }}</div>
                <div class="stat-label">Total Revenue (This Month)</div>
                <div class="stat-change {{ $stats['revenue_change'] >= 0 ? 'text-success' : 'text-danger' }}">
                    <i class="fas fa-arrow-{{ $stats['revenue_change'] >= 0 ? 'up' : 'down' }}"></i> 
                    {{ number_format(abs($stats['revenue_change']), 1) }}% from last month
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-value">{{ number_format($stats['total_transactions']) }}</div>
                <div class="stat-label">Total Transactions (This Week)</div>
                <div class="stat-change {{ $stats['transaction_change'] >= 0 ? 'text-success' : 'text-danger' }}">
                    <i class="fas fa-arrow-{{ $stats['transaction_change'] >= 0 ? 'up' : 'down' }}"></i> 
                    {{ number_format(abs($stats['transaction_change']), 1) }}% from last week
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-value">{{ number_format($stats['total_customers']) }}</div>
                <div class="stat-label">Total Customers</div>
                <div class="stat-change text-info">
                    <i class="fas fa-users"></i> {{ $stats['active_customers'] }} active this month
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-value">{{ number_format($stats['total_products']) }}</div>
                <div class="stat-label">Total Products & Props</div>
                <div class="stat-change {{ $stats['total_low_stock'] > 0 ? 'text-warning' : 'text-success' }}">
                    @if($stats['total_low_stock'] > 0)
                        <i class="fas fa-exclamation-triangle"></i> {{ $stats['total_low_stock'] }} need attention
                    @else
                        <i class="fas fa-check-circle"></i> All in good stock
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Summary -->
    <div class="section-header">
        <h2 class="section-title">Today's Performance</h2>
        <span class="text-muted">{{ now()->format('l, F d, Y') }}</span>
    </div>
    
    <div class="today-summary-grid">
        <div class="today-summary-card" style="border-left-color: #10b981;">
            <div class="summary-value">₦{{ number_format($todaySummary['lounge']['revenue'], 2) }}</div>
            <div class="summary-label">Mini Lounge</div>
            <small class="text-muted">{{ $todaySummary['lounge']['transactions'] }} transactions</small>
        </div>
        
        <div class="today-summary-card" style="border-left-color: #ef4444;">
            <div class="summary-value">₦{{ number_format($todaySummary['store']['revenue'], 2) }}</div>
            <div class="summary-label">Gift Store</div>
            <small class="text-muted">{{ $todaySummary['store']['transactions'] }} sales</small>
        </div>
        
        <div class="today-summary-card" style="border-left-color: #6f42c1;">
            <div class="summary-value">₦{{ number_format($todaySummary['studio']['revenue'], 2) }}</div>
            <div class="summary-label">Photo Studio</div>
            <small class="text-muted">{{ $todaySummary['studio']['sessions'] }} sessions</small>
        </div>
        
        <div class="today-summary-card" style="border-left-color: #f59e0b;">
            <div class="summary-value">₦{{ number_format($todaySummary['props']['revenue'], 2) }}</div>
            <div class="summary-label">Prop Rental</div>
            <small class="text-muted">{{ $todaySummary['props']['rentals'] }} rentals</small>
        </div>
    </div>

    <!-- Business Units Grid -->
    <div class="section-header">
        <h2 class="section-title">Business Units</h2>
    </div>
    
    <div class="business-grid">
        <!-- Mini Lounge -->
        <div class="business-unit-card">
            <div class="business-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="fas fa-{{ $businessModules['lounge']['icon'] }}"></i>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">
                {{ $businessModules['lounge']['name'] }}
            </h3>
            <div style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem;">
                ₦{{ number_format($businessModules['lounge']['revenue'], 2) }}
            </div>
            <div style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.7); margin-bottom: 1rem;">
                This month's revenue
            </div>
            
            <div class="business-stats">
                <div class="business-stat-item">
                    <span class="business-stat-label">Transactions</span>
                    <span class="business-stat-value">{{ $businessModules['lounge']['transactions'] }}</span>
                </div>
                <div class="business-stat-item">
                    <span class="business-stat-label">Products</span>
                    <span class="business-stat-value">{{ $businessModules['lounge']['products'] }}</span>
                </div>
                <div class="business-stat-item">
                    <span class="business-stat-label">Low Stock</span>
                    <span class="business-stat-value text-warning">{{ $businessModules['lounge']['low_stock'] }}</span>
                </div>
                <div class="business-stat-item">
                    <span class="business-stat-label">Active Customers</span>
                    <span class="business-stat-value">{{ $businessModules['lounge']['customers'] }}</span>
                </div>
            </div>
            
            <a href="{{ route('lounge.index') }}" class="btn btn-light btn-sm w-100 mt-3">
                <i class="fas fa-arrow-right me-2"></i>View Details
            </a>
        </div>

        <!-- Gift Store -->
        <div class="business-unit-card">
            <div class="business-icon" style="background: linear-gradient(135deg, #ec4899, #f97316);">
                <i class="fas fa-{{ $businessModules['gift_store']['icon'] }}"></i>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">
                {{ $businessModules['gift_store']['name'] }}
            </h3>
            <div style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem;">
                ₦{{ number_format($businessModules['gift_store']['revenue'], 2) }}
            </div>
            <div style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.7); margin-bottom: 1rem;">
                This month's revenue
            </div>
            
            <div class="business-stats">
                <div class="business-stat-item">
                    <span class="business-stat-label">Transactions</span>
                    <span class="business-stat-value">{{ $businessModules['gift_store']['transactions'] }}</span>
                </div>
                <div class="business-stat-item">
                    <span class="business-stat-label">Products</span>
                    <span class="business-stat-value">{{ $businessModules['gift_store']['products'] }}</span>
                </div>
                <div class="business-stat-item">
                    <span class="business-stat-label">Low Stock</span>
                    <span class="business-stat-value text-warning">{{ $businessModules['gift_store']['low_stock'] }}</span>
                </div>
                <div class="business-stat-item">
                    <span class="business-stat-label">Active Customers</span>
                    <span class="business-stat-value">{{ $businessModules['gift_store']['customers'] }}</span>
                </div>
            </div>
            
            <a href="{{ route('anire-craft-store.index') }}" class="btn btn-light btn-sm w-100 mt-3">
                <i class="fas fa-arrow-right me-2"></i>View Details
            </a>
        </div>

        <!-- Photo Studio -->
        <div class="business-unit-card">
            <div class="business-icon" style="background: linear-gradient(135deg, #6f42c1, #9333ea);">
                <i class="fas fa-{{ $businessModules['photo_studio']['icon'] }}"></i>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">
                {{ $businessModules['photo_studio']['name'] }}
            </h3>
            <div style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem;">
                ₦{{ number_format($businessModules['photo_studio']['revenue'], 2) }}
            </div>
            <div style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.7); margin-bottom: 1rem;">
                This month's revenue
            </div>
            
            <div class="business-stats">
                <div class="business-stat-item">
                    <span class="business-stat-label">Sessions</span>
                    <span class="business-stat-value">{{ $businessModules['photo_studio']['transactions'] }}</span>
                </div>
                <div class="business-stat-item">
                    <span class="business-stat-label">Active Now</span>
                    <span class="business-stat-value text-success">{{ $businessModules['photo_studio']['active_sessions'] }}</span>
                </div>
                <div class="business-stat-item">
                    <span class="business-stat-label">Active Customers</span>
                    <span class="business-stat-value">{{ $businessModules['photo_studio']['customers'] }}</span>
                </div>
                <div class="business-stat-item">
                    <span class="business-stat-label">Pending Payment</span>
                    <span class="business-stat-value text-warning">{{ $businessModules['photo_studio']['pending'] }}</span>
                </div>
            </div>
            
            <a href="{{ route('photo-studio.index') }}" class="btn btn-light btn-sm w-100 mt-3">
                <i class="fas fa-arrow-right me-2"></i>View Details
            </a>
        </div>

        <!-- Prop Rental -->
        <div class="business-unit-card">
            <div class="business-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <i class="fas fa-{{ $businessModules['prop_rental']['icon'] }}"></i>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">
                {{ $businessModules['prop_rental']['name'] }}
            </h3>
            <div style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem;">
                ₦{{ number_format($businessModules['prop_rental']['revenue'], 2) }}
            </div>
            <div style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.7); margin-bottom: 1rem;">
                This month's revenue
            </div>
            
            <div class="business-stats">
                <div class="business-stat-item">
                    <span class="business-stat-label">Rentals</span>
                    <span class="business-stat-value">{{ $businessModules['prop_rental']['transactions'] }}</span>
                </div>
                <div class="business-stat-item">
                    <span class="business-stat-label">Active Rentals</span>
                    <span class="business-stat-value text-success">{{ $businessModules['prop_rental']['active_rentals'] }}</span>
                </div>
                <div class="business-stat-item">
                    <span class="business-stat-label">Total Props</span>
                    <span class="business-stat-value">{{ $businessModules['prop_rental']['props'] }}</span>
                </div>
                <div class="business-stat-item">
                    <span class="business-stat-label">Overdue</span>
                    <span class="business-stat-value text-danger">{{ $businessModules['prop_rental']['overdue'] }}</span>
                </div>
            </div>
            
            <a href="{{ route('prop-rental.index') }}" class="btn btn-light btn-sm w-100 mt-3">
                <i class="fas fa-arrow-right me-2"></i>View Details
            </a>
        </div>
    </div>

    <!-- Revenue Trend Chart -->
    <div class="revenue-chart-container">
        <div class="section-header mb-3">
            <h2 class="section-title">Revenue Trend (Last 7 Days)</h2>
        </div>
        <canvas id="revenueTrendChart" height="80"></canvas>
    </div>

    <!-- Recent Activities -->
    <div class="activity-feed">
        <div class="section-header mb-3">
            <h2 class="section-title">Recent Activities</h2>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        
        @forelse($recentActivities as $activity)
            <div class="activity-item">
                <div class="activity-icon" style="background: linear-gradient(135deg, 
                    @if($activity['color'] == 'success') #10b981, #059669
                    @elseif($activity['color'] == 'danger') #ef4444, #dc2626
                    @elseif($activity['color'] == 'primary') #6f42c1, #9333ea
                    @elseif($activity['color'] == 'warning') #f59e0b, #d97706
                    @endif);">
                    <i class="fas fa-{{ $activity['icon'] }} text-white"></i>
                </div>
                
                <div class="activity-details">
                    <div class="activity-title">{{ $activity['title'] }}</div>
                    {{-- <div class="activity-description">{{ $activity['description'] }}</div> 
                    <div class="activity-time">{{ $activity['time']->diffForHumans() }}</div> --}}
                </div>
                
                {{-- <div class="activity-amount">₦{{ number_format($activity['amount'], 2) }}</div> --}}
            </div>
        @empty
            <div class="text-center text-muted py-4">
                <i class="fas fa-inbox fa-3x mb-3" style="opacity: 0.3;"></i>
                <p>No recent activities</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Trend Chart
    const ctx = document.getElementById('revenueTrendChart');
    if (ctx) {
        const revenueTrendData = @json($revenueTrend);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: revenueTrendData.map(item => item.date),
                datasets: [
                    {
                        label: 'Mini Lounge',
                        data: revenueTrendData.map(item => item.lounge),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Gift Store',
                        data: revenueTrendData.map(item => item.store),
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Photo Studio',
                        data: revenueTrendData.map(item => item.studio),
                        borderColor: '#6f42c1',
                        backgroundColor: 'rgba(111, 66, 193, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Prop Rental',
                        data: revenueTrendData.map(item => item.props),
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
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
                            padding: 15,
                            font: {
                                size: 12,
                                weight: 600
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ₦' + context.parsed.y.toLocaleString('en-NG', {
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
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
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
    }
});
</script>
@endpush