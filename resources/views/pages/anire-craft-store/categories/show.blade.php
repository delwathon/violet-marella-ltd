@extends('layouts.app')
@section('title', 'Category Details')

@section('content')
<div class="content-area">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">{{ $category->name }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('anire-craft-store.categories.index') }}">Categories</a></li>
                        <li class="breadcrumb-item active">{{ $category->name }}</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <a href="{{ route('anire-craft-store.categories.edit', $category->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('anire-craft-store.categories.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Category Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Category Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}" 
                                     alt="{{ $category->name }}" 
                                     class="img-fluid rounded shadow-sm">
                            @else
                                <div class="d-flex align-items-center justify-content-center rounded" 
                                     style="height: 200px; background-color: {{ $category->color ?? '#6c757d' }}">
                                    <i class="fas fa-tag fa-5x text-white"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Category Name:</th>
                                    <td><strong>{{ $category->name }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Slug:</th>
                                    <td><span class="badge bg-secondary">{{ $category->slug }}</span></td>
                                </tr>
                                <tr>
                                    <th>Color:</th>
                                    <td>
                                        <span class="badge" style="background-color: {{ $category->color ?? '#6c757d' }}">
                                            {{ $category->color ?? 'Default' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Sort Order:</th>
                                    <td>{{ $category->sort_order }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created:</th>
                                    <td>{{ $category->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $category->updated_at->diffForHumans() }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($category->description)
                    <div class="mt-3">
                        <h6>Description</h6>
                        <p class="text-muted">{{ $category->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Products in Category -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Products in this Category</h5>
                    <a href="{{ route('anire-craft-store.products.create') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus"></i> Add Product
                    </a>
                </div>
                <div class="card-body">
                    @if($category->products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Product Name</th>
                                        <th>SKU</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($category->products as $product)
                                        <tr>
                                            <td>
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" 
                                                         alt="{{ $product->name }}" 
                                                         class="img-thumbnail" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $product->name }}</strong>
                                            </td>
                                            <td>{{ $product->sku }}</td>
                                            <td>₦{{ number_format($product->price, 2) }}</td>
                                            <td>
                                                @if($product->track_stock)
                                                    <span class="badge bg-{{ $product->isLowStock() ? 'warning' : 'success' }}">
                                                        {{ $product->stock_quantity }}
                                                    </span>
                                                @else
                                                    <i class="fas fa-infinity text-success"></i>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('anire-craft-store.products.show', $product->id) }}" 
                                                       class="btn btn-outline-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('anire-craft-store.products.edit', $product->id) }}" 
                                                       class="btn btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No products in this category</h5>
                            <p class="text-muted">
                                <a href="{{ route('anire-craft-store.products.create') }}">Add a new product</a> to this category
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Statistics -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Category Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total Products</span>
                            <h4 class="mb-0 text-primary">{{ $category->products_count }}</h4>
                        </div>
                    </div>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Active Products</span>
                            <h4 class="mb-0 text-success">{{ $category->active_products_count }}</h4>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Inactive Products</span>
                            <h4 class="mb-0 text-secondary">{{ $category->products_count - $category->active_products_count }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('anire-craft-store.products.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add Product to Category
                        </a>
                        <a href="{{ route('anire-craft-store.categories.edit', $category->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit"></i> Edit Category
                        </a>
                        <a href="{{ route('anire-craft-store.products.index') }}?category_id={{ $category->id }}" class="btn btn-outline-info">
                            <i class="fas fa-filter"></i> View All Products
                        </a>
                        <hr>
                        <button class="btn btn-outline-danger" onclick="deleteCategory()">
                            <i class="fas fa-trash"></i> Delete Category
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.stat-item {
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 0.5rem;
}
</style>
@endpush

@push('scripts')
<script>
function deleteCategory() {
    if (!confirm('Are you sure you want to delete this category? All products must be moved to another category first.')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('anire-craft-store.categories.destroy', $category->id) }}';
    
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
