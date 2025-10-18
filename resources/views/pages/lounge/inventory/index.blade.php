@extends('layouts.app')
@section('title', 'Inventory Management - Violet Marella Limited')
@push('styles')
<link href="{{ asset('assets/css/lounge.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Inventory Management</h1>
                <p class="page-subtitle">Track and manage stock levels</p>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <a href="{{ route('products.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Add Product
                    </a>
                    <a href="{{ route('inventory.logs') }}" class="btn btn-outline-primary">
                        <i class="fas fa-history me-2"></i>View Logs
                    </a>
                    <button class="btn btn-outline-secondary" onclick="exportInventory()">
                        <i class="fas fa-download me-2"></i>Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ number_format($totalProducts) }}</div>
                    <div class="stat-label">Total Products</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">₦{{ number_format($totalStockValue, 2) }}</div>
                    <div class="stat-label">Stock Value</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ number_format($lowStockCount) }}</div>
                    <div class="stat-label">Low Stock Items</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ number_format($outOfStockCount) }}</div>
                    <div class="stat-label">Out of Stock</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Inventory List -->
        <div class="col-lg-8">
            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('inventory.index') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="category_id" class="form-select">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="stock_status" class="form-select">
                                    <option value="">All Stock Status</option>
                                    <option value="good" {{ request('stock_status') === 'good' ? 'selected' : '' }}>Good Stock</option>
                                    <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Low Stock</option>
                                    <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Out of Stock</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Product List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Inventory List</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th class="text-center">Stock</th>
                                    <th class="text-center">Min Level</th>
                                    <th class="text-end">Value</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <strong>{{ $product->name }}</strong>
                                            @if($product->image)
                                                <br><img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="width: 40px; height: 40px; object-fit: cover;" class="rounded">
                                            @endif
                                        </td>
                                        <td>{{ $product->sku }}</td>
                                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $product->isOutOfStock() ? 'danger' : ($product->isLowStock() ? 'warning' : 'success') }}">
                                                {{ $product->stock_quantity }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ $product->minimum_stock_level ?? 'N/A' }}</td>
                                        <td class="text-end">
                                            ₦{{ number_format($product->stock_quantity * ($product->cost_price ?? 0), 2) }}
                                        </td>
                                        <td>
                                            @if($product->isOutOfStock())
                                                <span class="badge bg-danger">Out of Stock</span>
                                            @elseif($product->isLowStock())
                                                <span class="badge bg-warning">Low Stock</span>
                                            @else
                                                <span class="badge bg-success">Good</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('inventory.adjust', $product->id) }}" class="btn btn-outline-primary" title="Adjust Stock">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-secondary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-box-open fa-3x mb-3"></i>
                                            <br>No products found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $products->links() }}
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Activities</h5>
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    @forelse($recentActivities as $activity)
                        <div class="activity-item mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-start">
                                <div class="activity-icon me-3">
                                    <i class="fas {{ $activity->action_type === 'purchase' ? 'fa-box text-primary' : ($activity->action_type === 'sale' ? 'fa-shopping-cart text-success' : ($activity->action_type === 'damage' || $activity->action_type === 'expiry' ? 'fa-exclamation-triangle text-danger' : 'fa-edit text-warning')) }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>{{ $activity->action_description }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $activity->action_date->diffForHumans() }}</small>
                                    @if($activity->staff)
                                        <br><small class="text-muted">by {{ $activity->staff->first_name }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-history fa-2x mb-2"></i>
                            <br>No recent activities
                        </div>
                    @endforelse
                </div>
                <div class="card-footer">
                    <a href="{{ route('inventory.logs') }}" class="btn btn-sm btn-outline-primary w-100">
                        View All Logs
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function exportInventory() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = '{{ route("inventory.export") }}?' + params.toString();
}
</script>
@endpush
@endsection