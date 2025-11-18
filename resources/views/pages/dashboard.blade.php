@extends('layouts.app')
@section('title', 'Dashboard - Violet Marella Limited ')
@push('styles')
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Dashboard</h1>
                <p class="page-subtitle">Welcome, {{ $user->full_name }}</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">
                    <i class="fas fa-chart-line me-2"></i>Generate Report
                </button>
            </div>
        </div>
    </div>

    <!-- Dashboard Section -->
    <div class="dashboard-section">
        <!-- Statistics Cards -->
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
                    <div class="stat-value">{{ number_format($stats['total_products']) }}</div>
                    <div class="stat-label">Total Products</div>
                    <div class="stat-change {{ $stats['low_stock_count'] > 0 ? 'text-warning' : 'text-success' }}">
                        @if($stats['low_stock_count'] > 0)
                            <i class="fas fa-exclamation-triangle"></i> {{ $stats['low_stock_count'] }} low stock
                        @else
                            <i class="fas fa-check-circle"></i> All stocked
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($stats['studio_sessions']) }}</div>
                    <div class="stat-label">Sales This Week</div>
                    <div class="stat-change {{ $stats['sessions_change'] >= 0 ? 'text-success' : 'text-danger' }}">
                        <i class="fas fa-arrow-{{ $stats['sessions_change'] >= 0 ? 'up' : 'down' }}"></i> 
                        {{ number_format(abs($stats['sessions_change']), 1) }}% from last week
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($stats['active_customers']) }}</div>
                    <div class="stat-label">Active Customers</div>
                    <div class="stat-change {{ $stats['due_today'] > 0 ? 'text-info' : 'text-muted' }}">
                        @if($stats['due_today'] > 0)
                            <i class="fas fa-clock"></i> {{ $stats['due_today'] }} pending payments
                        @else
                            <i class="fas fa-check"></i> All settled
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Business Modules -->
        <div class="row mb-4">
            <div class="col-lg-4 mb-4">
                <div class="module-card anire-craft-store">
                    <div class="module-header">
                        <div class="module-icon">
                            <i class="fas fa-gift"></i>
                        </div>
                        <h3 class="module-title">Gift Store</h3>
                        <p class="module-description">Manage inventory, track sales, and monitor stock levels for your gift store business.</p>
                    </div>
                    <div class="module-body">
                        <a href="{{ route('anire-craft-store.index') }}" class="quick-action">
                            <i class="fas fa-boxes"></i>
                            View Inventory
                        </a>
                        <button class="quick-action" onclick="showModal('addProductModal')">
                            <i class="fas fa-plus"></i>
                            Add New Product
                        </button>
                        <a href="{{ route('reports.index', ['type' => 'anire-craft-store']) }}" class="quick-action">
                            <i class="fas fa-chart-line"></i>
                            Sales Report
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="module-card lounge">
                    <div class="module-header">
                        <div class="module-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h3 class="module-title">Mini Lounge</h3>
                        <p class="module-description">Complete POS system with inventory management for your lounge operations.</p>
                    </div>
                    <div class="module-body">
                        <a href="{{ route('lounge.index') }}" class="quick-action">
                            <i class="fas fa-cash-register"></i>
                            Open POS
                        </a>
                        <a href="{{ route('lounge.index', ['tab' => 'inventory']) }}" class="quick-action">
                            <i class="fas fa-warehouse"></i>
                            Manage Stock
                        </a>
                        <a href="{{ route('reports.index', ['type' => 'lounge']) }}" class="quick-action">
                            <i class="fas fa-receipt"></i>
                            Daily Sales
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="module-card photo-studio">
                    <div class="module-header">
                        <div class="module-icon">
                            <i class="fas fa-music"></i>
                        </div>
                        <h3 class="module-title">Photo Studio</h3>
                        <p class="module-description">Time-based billing system with QR code generation for studio session management.</p>
                    </div>
                    <div class="module-body">
                        <a href="{{ route('photo-studio.index') }}" class="quick-action">
                            <i class="fas fa-user-plus"></i>
                            Check-in Customer
                        </a>
                        <a href="{{ route('photo-studio.index', ['tab' => 'qr']) }}" class="quick-action">
                            <i class="fas fa-qrcode"></i>
                            Generate QR Code
                        </a>
                        <a href="{{ route('photo-studio.index', ['tab' => 'billing']) }}" class="quick-action">
                            <i class="fas fa-calculator"></i>
                            Calculate Billing
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities and Quick Stats -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Activities</h5>
                        <button class="btn btn-sm btn-outline-primary">View All</button>
                    </div>
                    <div class="card-body">
                        @forelse($recentActivities as $activity)
                            <div class="activity-item">
                                <div class="activity-icon bg-{{ $activity['color'] }}">
                                    <i class="fas {{ $activity['icon'] }}"></i>
                                </div>
                                <div class="activity-content">
                                    <h6>{{ $activity['title'] }}</h6>
                                    <p class="text-muted mb-0">{{ $activity['description'] }}</p>
                                    <small class="text-muted">{{ $activity['time'] }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>No recent activities</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                @if($lowStockProducts->count() > 0)
                <!-- Low Stock Alert Section -->
                <div class="card mt-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Low Stock Alerts
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Current Stock</th>
                                        <th>Min. Level</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockProducts as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>
                                                <span class="badge bg-danger">{{ $product->stock_quantity }}</span>
                                            </td>
                                            <td>{{ $product->minimum_stock_level }}</td>
                                            <td>
                                                <a href="{{ route('lounge.products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">
                                                    Restock
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('lounge.sales.create') }}" class="btn btn-outline-primary">
                                <i class="fas fa-plus me-2"></i>New Sale
                            </a>
                            <a href="{{ route('lounge.products.create') }}" class="btn btn-outline-success">
                                <i class="fas fa-box me-2"></i>Add Product
                            </a>
                            <a href="{{ route('lounge.customers.create') }}" class="btn btn-outline-info">
                                <i class="fas fa-user-plus me-2"></i>New Customer
                            </a>
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-warning">
                                <i class="fas fa-chart-bar me-2"></i>Generate Report
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Today's Sales Summary -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Today's Sales</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Total Sales</span>
                            <h4 class="mb-0">{{ $todaySales['count'] }}</h4>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total Amount</span>
                            <h4 class="mb-0 text-success">₦{{ number_format($todaySales['amount'], 2) }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Top Products -->
                @if($topProducts->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Top Selling Products</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            @foreach($topProducts as $product)
                                <li class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <div>
                                        <strong>{{ $product->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $product->total_sold }} units sold</small>
                                    </div>
                                    <span class="badge bg-success">₦{{ number_format($product->price, 0) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection