@extends('layouts.app')
@section('title', 'Product Management')

@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Product Management</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Products</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <a href="{{ route('lounge.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-cash-register"></i> Back to POS
                    </a>
                    <a href="{{ route('lounge.categories.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-tags"></i> Categories
                    </a>
                    <a href="{{ route('lounge.products.export') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-download"></i> Export
                    </a>
                    <a href="{{ route('lounge.products.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add Product
                    </a>
                    <a href="{{ route('lounge.products.bulk-upload') }}" class="btn btn-primary">
                        <i class="fas fa-file-upload"></i> Bulk Upload
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $products->total() }}</div>
                    <div class="stat-label">Total Products</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $lowStockCount }}</div>
                    <div class="stat-label">Low Stock</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $outOfStockCount }}</div>
                    <div class="stat-label">Out of Stock</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $categories->count() }}</div>
                    <div class="stat-label">Categories</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('lounge.products.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search by name, SKU, or barcode..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="stock_status" class="form-select">
                            <option value="">All Stock</option>
                            <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Low Stock</option>
                            <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('lounge.products.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Product Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Image</th>
                            <th>Product Details</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Cost</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th style="width: 180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" 
                                             alt="{{ $product->name }}" 
                                             class="img-thumbnail" 
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $product->name }}</strong>
                                    <br><small class="text-muted">SKU: {{ $product->sku }}</small>
                                    @if($product->barcode)
                                        <br><small class="text-muted">Barcode: {{ $product->barcode }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge" style="background-color: {{ $product->category->color ?? '#6c757d' }}">
                                        {{ $product->category->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-success">₦{{ number_format($product->price, 2) }}</strong>
                                    @if($product->wholesale_price)
                                        <br><small class="text-muted">W: ₦{{ number_format($product->wholesale_price, 2) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($product->cost_price)
                                        ₦{{ number_format($product->cost_price, 2) }}
                                        <br><small class="text-muted">{{ number_format($product->profit_margin, 1) }}% margin</small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->track_stock)
                                        <span class="badge bg-{{ $product->isOutOfStock() ? 'danger' : ($product->isLowStock() ? 'warning' : 'success') }}">
                                            {{ $product->stock_quantity }}
                                        </span>
                                        @if($product->minimum_stock_level)
                                            <br><small class="text-muted">Min: {{ $product->minimum_stock_level }}</small>
                                        @endif
                                    @else
                                        <i class="fas fa-infinity text-success" title="Unlimited"></i>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    @if($product->is_featured)
                                        <br><span class="badge bg-warning mt-1">Featured</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('lounge.products.show', $product->id) }}" 
                                           class="btn btn-outline-info" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('lounge.products.edit', $product->id) }}" 
                                           class="btn btn-outline-primary" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-outline-warning" 
                                                onclick="showAdjustStockModal({{ $product->id }}, '{{ $product->name }}', {{ $product->stock_quantity }})" 
                                                title="Adjust Stock">
                                            <i class="fas fa-boxes"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" 
                                                onclick="deleteProduct({{ $product->id }})" 
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fas fa-boxes fa-3x mb-3"></i>
                                    <br><h5>No products found</h5>
                                    <p>Try adjusting your filters or <a href="{{ route('lounge.products.create') }}">add a new product</a></p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($products->hasPages())
                <div class="mt-3">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Adjust Stock Modal -->
<div class="modal fade" id="adjustStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adjust Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="adjustStockForm">
                    <input type="hidden" id="adjustProductId">
                    
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <input type="text" class="form-control" id="adjustProductName" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Current Stock</label>
                        <input type="text" class="form-control" id="adjustCurrentStock" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Action Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="adjustActionType" required>
                            <option value="purchase">Stock Purchase (Add)</option>
                            <option value="adjustment">Manual Adjustment</option>
                            <option value="damage">Damaged Goods (Deduct)</option>
                            <option value="expiry">Expired Goods (Deduct)</option>
                            <option value="return">Customer Return (Add)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Quantity Change <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="adjustQuantity" required>
                        <small class="text-muted">Use positive numbers to add, negative to deduct</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Unit Cost (Optional)</label>
                        <input type="number" step="0.01" class="form-control" id="adjustUnitCost">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea class="form-control" id="adjustReason" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitStockAdjustment()">
                    <i class="fas fa-save"></i> Adjust Stock
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showAdjustStockModal(productId, productName, currentStock) {
    document.getElementById('adjustProductId').value = productId;
    document.getElementById('adjustProductName').value = productName;
    document.getElementById('adjustCurrentStock').value = currentStock;
    document.getElementById('adjustQuantity').value = '';
    document.getElementById('adjustUnitCost').value = '';
    document.getElementById('adjustReason').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('adjustStockModal'));
    modal.show();
}

async function submitStockAdjustment() {
    const productId = document.getElementById('adjustProductId').value;
    const data = {
        action_type: document.getElementById('adjustActionType').value,
        quantity_change: parseInt(document.getElementById('adjustQuantity').value),
        unit_cost: document.getElementById('adjustUnitCost').value || null,
        reason: document.getElementById('adjustReason').value
    };
    
    if (!data.quantity_change) {
        alert('Please enter quantity change');
        return;
    }
    
    try {
        const response = await fetch(`/app/products/${productId}/adjust-stock`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('adjustStockModal'));
            modal.hide();
            
            alert('Stock adjusted successfully');
            window.location.reload();
        } else {
            alert(result.message || 'Failed to adjust stock');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error adjusting stock');
    }
}

function deleteProduct(productId) {
    if (!confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/app/products/${productId}`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
    
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';
    
    form.appendChild(csrfToken);
    form.appendChild(methodField);
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
@endsection