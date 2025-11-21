@extends('layouts.app')
@section('title', 'Adjust Stock - Violet Marella Limited')
@push('styles')
<link href="{{ asset('assets/css/lounge.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Adjust Stock</h1>
                <p class="page-subtitle">Update inventory levels for {{ $product->name }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('anire-craft-store.inventory.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Inventory
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Adjustment Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Stock Adjustment Form</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('anire-craft-store.inventory.process-adjustment', $product->id) }}">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Product Name</label>
                                <input type="text" class="form-control" value="{{ $product->name }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Current Stock</label>
                                <input type="text" class="form-control text-center" value="{{ $product->stock_quantity }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Min Level</label>
                                <input type="text" class="form-control text-center" value="{{ $product->minimum_stock_level ?? 'N/A' }}" readonly>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Action Type <span class="text-danger">*</span></label>
                                <select name="action_type" class="form-select @error('action_type') is-invalid @enderror" required>
                                    <option value="">Select Action Type</option>
                                    <option value="purchase">Stock Purchase (Add Stock)</option>
                                    <option value="adjustment">Manual Adjustment</option>
                                    <option value="return">Customer Return (Add Stock)</option>
                                    <option value="damage">Damaged Goods (Reduce Stock)</option>
                                    <option value="expiry">Expired Items (Reduce Stock)</option>
                                </select>
                                @error('action_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantity Change <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-danger" onclick="changeQuantity(-1)">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" name="quantity_change" id="quantityChange" class="form-control text-center @error('quantity_change') is-invalid @enderror" value="0" required>
                                    <button type="button" class="btn btn-outline-success" onclick="changeQuantity(1)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Use negative numbers to reduce stock, positive to add</small>
                                @error('quantity_change')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Unit Cost (Optional)</label>
                                <div class="input-group">
                                    <span class="input-group-text">₦</span>
                                    <input type="number" name="unit_cost" class="form-control @error('unit_cost') is-invalid @enderror" step="0.01" min="0" placeholder="0.00">
                                </div>
                                <small class="text-muted">Cost per unit (for purchases)</small>
                                @error('unit_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">New Stock Level (Preview)</label>
                                <input type="text" id="newStockPreview" class="form-control text-center fw-bold" value="{{ $product->stock_quantity }}" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Reason/Notes</label>
                            <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" rows="3" placeholder="Enter reason for stock adjustment..."></textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> This action will be logged in the inventory history with your user details and timestamp.
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Adjustment
                            </button>
                            <a href="{{ route('anire-craft-store.inventory.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Product Info Sidebar -->
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Product Information</h5>
                </div>
                <div class="card-body">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded mb-3">
                    @endif
                    
                    <div class="mb-2">
                        <strong>SKU:</strong> {{ $product->sku }}
                    </div>
                    <div class="mb-2">
                        <strong>Category:</strong> {{ $product->category->name ?? 'N/A' }}
                    </div>
                    <div class="mb-2">
                        <strong>Price:</strong> ₦{{ number_format($product->price, 2) }}
                    </div>
                    @if($product->cost_price)
                        <div class="mb-2">
                            <strong>Cost Price:</strong> ₦{{ number_format($product->cost_price, 2) }}
                        </div>
                    @endif
                    <div class="mb-2">
                        <strong>Unit:</strong> {{ $product->unit ?? 'N/A' }}
                    </div>
                    <div class="mb-2">
                        <strong>Stock Tracking:</strong> 
                        <span class="badge bg-{{ $product->track_stock ? 'success' : 'secondary' }}">
                            {{ $product->track_stock ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="setQuickAdjustment('purchase', 10)">
                            <i class="fas fa-plus me-2"></i>Add 10 Units
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="setQuickAdjustment('purchase', 50)">
                            <i class="fas fa-plus me-2"></i>Add 50 Units
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="setQuickAdjustment('damage', -5)">
                            <i class="fas fa-minus me-2"></i>Remove 5 Units
                        </button>
                        <a href="{{ route('anire-craft-store.products.edit', $product->id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-edit me-2"></i>Edit Product
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const currentStock = {{ $product->stock_quantity }};

function changeQuantity(amount) {
    const input = document.getElementById('quantityChange');
    const currentValue = parseInt(input.value) || 0;
    input.value = currentValue + amount;
    updateNewStockPreview();
}

function updateNewStockPreview() {
    const quantityChange = parseInt(document.getElementById('quantityChange').value) || 0;
    const newStock = Math.max(0, currentStock + quantityChange);
    const preview = document.getElementById('newStockPreview');
    preview.value = newStock;
    
    // Color coding
    if (newStock === 0) {
        preview.classList.remove('text-success', 'text-warning');
        preview.classList.add('text-danger');
    } else if (newStock <= {{ $product->minimum_stock_level ?? 0 }}) {
        preview.classList.remove('text-success', 'text-danger');
        preview.classList.add('text-warning');
    } else {
        preview.classList.remove('text-warning', 'text-danger');
        preview.classList.add('text-success');
    }
}

function setQuickAdjustment(actionType, quantity) {
    document.querySelector('select[name="action_type"]').value = actionType;
    document.getElementById('quantityChange').value = quantity;
    updateNewStockPreview();
}

// Update preview when quantity changes
document.getElementById('quantityChange').addEventListener('input', updateNewStockPreview);
</script>
@endpush
@endsection