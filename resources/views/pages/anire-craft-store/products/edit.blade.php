@extends('layouts.app')
@section('title', 'Edit Product')

@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Edit Product</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('anire-craft-store.products.index') }}">Products</a></li>
                        <li class="breadcrumb-item active">Edit: {{ $product->name }}</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <a href="{{ route('anire-craft-store.products.show', $product->id) }}" class="btn btn-outline-info">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                    <a href="{{ route('anire-craft-store.products.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Products
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('anire-craft-store.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Main Information -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Product Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $product->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">SKU</label>
                                <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" 
                                       value="{{ old('sku', $product->sku) }}">
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Barcode</label>
                                <input type="text" name="barcode" class="form-control @error('barcode') is-invalid @enderror" 
                                       value="{{ old('barcode', $product->barcode) }}">
                                @error('barcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                          rows="4">{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="store_category_id" class="form-select @error('store_category_id') is-invalid @enderror" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ old('store_category_id', $product->store_category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('store_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit</label>
                                <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" 
                                       value="{{ old('unit', $product->unit) }}">
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Brand</label>
                                <input type="text" name="brand" class="form-control @error('brand') is-invalid @enderror" 
                                       value="{{ old('brand', $product->brand) }}">
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Supplier</label>
                                <input type="text" name="supplier" class="form-control @error('supplier') is-invalid @enderror" 
                                       value="{{ old('supplier', $product->supplier) }}">
                                @error('supplier')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Pricing</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Selling Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₦</span>
                                    <input type="number" step="0.01" name="price" 
                                           class="form-control @error('price') is-invalid @enderror" 
                                           value="{{ old('price', $product->price) }}" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Cost Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">₦</span>
                                    <input type="number" step="0.01" name="cost_price" 
                                           class="form-control @error('cost_price') is-invalid @enderror" 
                                           value="{{ old('cost_price', $product->cost_price) }}">
                                    @error('cost_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @if($product->profit_margin)
                                    <small class="text-success">Current margin: {{ number_format($product->profit_margin, 1) }}%</small>
                                @endif
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Wholesale Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">₦</span>
                                    <input type="number" step="0.01" name="wholesale_price" 
                                           class="form-control @error('wholesale_price') is-invalid @enderror" 
                                           value="{{ old('wholesale_price', $product->wholesale_price) }}">
                                    @error('wholesale_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tax Rate (%)</label>
                                <input type="number" step="0.01" name="tax_rate" 
                                       class="form-control @error('tax_rate') is-invalid @enderror" 
                                       value="{{ old('tax_rate', $product->tax_rate) }}">
                                @error('tax_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inventory -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Inventory Management</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="track_stock" 
                                           id="trackStock" {{ old('track_stock', $product->track_stock) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="trackStock">
                                        Track Stock Quantity
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Current Stock Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="stock_quantity" 
                                       class="form-control @error('stock_quantity') is-invalid @enderror" 
                                       value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
                                @error('stock_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($product->isLowStock())
                                    <small class="text-danger">⚠️ Low stock alert!</small>
                                @endif
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Minimum Stock Level</label>
                                <input type="number" name="minimum_stock_level" 
                                       class="form-control @error('minimum_stock_level') is-invalid @enderror" 
                                       value="{{ old('minimum_stock_level', $product->minimum_stock_level) }}">
                                @error('minimum_stock_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Maximum Stock Level</label>
                                <input type="number" name="maximum_stock_level" 
                                       class="form-control @error('maximum_stock_level') is-invalid @enderror" 
                                       value="{{ old('maximum_stock_level', $product->maximum_stock_level) }}">
                                @error('maximum_stock_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Expiry Date</label>
                                <input type="date" name="expiry_date" 
                                       class="form-control @error('expiry_date') is-invalid @enderror" 
                                       value="{{ old('expiry_date', $product->expiry_date?->format('Y-m-d')) }}">
                                @error('expiry_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    To adjust stock with proper logging, use the 
                                    <a href="{{ route('anire-craft-store.products.show', $product->id) }}">product details page</a>.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Product Image -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Product Image</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Upload New Image</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" 
                                   accept="image/*" onchange="previewImage(event)">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Leave empty to keep current image</small>
                        </div>
                        
                        <div class="text-center">
                            <img id="imagePreview" 
                                 src="{{ $product->image ? asset('storage/' . $product->image) : asset('assets/images/no-image.png') }}" 
                                 class="img-fluid rounded" style="max-height: 250px;" alt="Preview">
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" 
                                       id="isActive" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="isActive">
                                    Active
                                </label>
                            </div>
                            <small class="text-muted">Product will be visible in POS</small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_featured" 
                                       id="isFeatured" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                <label class="form-check-label" for="isFeatured">
                                    Featured Product
                                </label>
                            </div>
                            <small class="text-muted">Display on homepage/featured section</small>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Product Stats</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">Total Sales:</small>
                            <strong class="float-end">{{ $product->total_sales }} units</strong>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Revenue:</small>
                            <strong class="float-end text-success">₦{{ number_format($product->total_revenue, 2) }}</strong>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Created:</small>
                            <strong class="float-end">{{ $product->created_at->format('M d, Y') }}</strong>
                        </div>
                        <div>
                            <small class="text-muted">Last Updated:</small>
                            <strong class="float-end">{{ $product->updated_at->diffForHumans() }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Update Product
                            </button>
                            <a href="{{ route('anire-craft-store.products.show', $product->id) }}" class="btn btn-outline-info">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            <a href="{{ route('anire-craft-store.products.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Image Preview
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const preview = document.getElementById('imagePreview');
        preview.src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endpush
@endsection