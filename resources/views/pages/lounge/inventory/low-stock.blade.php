@extends('layouts.app')
@section('title', 'Low Stock Alert - Violet Marella Limited')
@push('styles')
<link href="{{ asset('assets/css/lounge.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Low Stock Alert</h1>
                <p class="page-subtitle">Products that need restocking</p>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <a href="{{ route('lounge.inventory.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Inventory
                    </a>
                    <button class="btn btn-outline-primary" onclick="exportLowStock()">
                        <i class="fas fa-download me-2"></i>Export List
                    </button>
                    <button class="btn btn-warning" onclick="printLowStock()">
                        <i class="fas fa-print me-2"></i>Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card border-warning">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $products->total() }}</div>
                    <div class="stat-label">Low Stock Items</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card border-danger">
                <div class="stat-icon bg-danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $products->where('stock_quantity', '<=', 0)->count() }}</div>
                    <div class="stat-label">Out of Stock</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card border-info">
                <div class="stat-icon bg-info">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $products->where('stock_quantity', '>', 0)->count() }}</div>
                    <div class="stat-label">Critical Stock</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card border-primary">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">₦{{ number_format($products->sum(function($p) { return ($p->minimum_stock_level - $p->stock_quantity) * ($p->cost_price ?? 0); }), 2) }}</div>
                    <div class="stat-label">Est. Restock Cost</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Bar -->
    <div class="card mb-4 bg-light">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h6 class="mb-2"><i class="fas fa-lightbulb text-warning"></i> Quick Actions</h6>
                    <p class="mb-0 small text-muted">Select products and perform bulk actions to manage stock efficiently</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="btn-group">
                        <button class="btn btn-sm btn-primary" onclick="bulkAdjustStock()">
                            <i class="fas fa-boxes"></i> Bulk Restock
                        </button>
                        <button class="btn btn-sm btn-info" onclick="generatePurchaseOrder()">
                            <i class="fas fa-file-alt"></i> Generate PO
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Table -->
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">Low Stock Products</h5>
                </div>
                <div class="col-auto">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll(this)">
                        <label class="form-check-label" for="selectAll">
                            Select All
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 50px;">
                                <input type="checkbox" class="form-check-input" id="selectAllHeader" onchange="toggleSelectAll(this)">
                            </th>
                            <th style="width: 80px;">Image</th>
                            <th>Product Details</th>
                            <th>Category</th>
                            <th class="text-center">Current</th>
                            <th class="text-center">Min Level</th>
                            <th class="text-center">Shortage</th>
                            <th>Status</th>
                            <th class="text-end">Est. Cost</th>
                            <th style="width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr class="product-row" data-product-id="{{ $product->id }}">
                                <td>
                                    <input type="checkbox" class="form-check-input product-checkbox" value="{{ $product->id }}">
                                </td>
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
                                    @if($product->supplier)
                                        <br><small class="text-muted"><i class="fas fa-truck"></i> {{ $product->supplier }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge" style="background-color: {{ $product->category->color ?? '#6c757d' }}">
                                        {{ $product->category->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $product->stock_quantity <= 0 ? 'danger' : 'warning' }} fs-6">
                                        {{ $product->stock_quantity }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <strong>{{ $product->minimum_stock_level ?? 'Not Set' }}</strong>
                                </td>
                                <td class="text-center">
                                    @php
                                        $shortage = max(0, ($product->minimum_stock_level ?? 0) - $product->stock_quantity);
                                    @endphp
                                    <span class="badge bg-danger">
                                        {{ $shortage }} {{ $product->unit ?? 'units' }}
                                    </span>
                                </td>
                                <td>
                                    @if($product->stock_quantity <= 0)
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle"></i> Out of Stock
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-exclamation-triangle"></i> Low Stock
                                        </span>
                                    @endif
                                    @if($product->expiry_date && $product->expiry_date->isPast())
                                        <br><span class="badge bg-dark mt-1">
                                            <i class="fas fa-calendar-times"></i> Expired
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($product->cost_price)
                                        @php
                                            $restockCost = $shortage * $product->cost_price;
                                        @endphp
                                        <strong class="text-primary">₦{{ number_format($restockCost, 2) }}</strong>
                                        <br><small class="text-muted">@ ₦{{ number_format($product->cost_price, 2) }}/unit</small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-success" 
                                                onclick="quickRestock({{ $product->id }}, '{{ $product->name }}', {{ $shortage }})" 
                                                title="Quick Restock">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <a href="{{ route('lounge.inventory.adjust', $product->id) }}" 
                                           class="btn btn-primary" 
                                           title="Adjust Stock">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('lounge.products.show', $product->id) }}" 
                                           class="btn btn-info" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('lounge.products.edit', $product->id) }}" 
                                           class="btn btn-warning" 
                                           title="Edit Product">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-5">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <br><h5>All products are well stocked!</h5>
                                    <p>No low stock items at the moment.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($products->hasPages())
            <div class="card-footer">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <!-- Important Notice -->
    <div class="alert alert-info mt-4">
        <h6><i class="fas fa-info-circle"></i> Stock Management Tips</h6>
        <ul class="mb-0">
            <li>Products shown here have stock levels at or below their minimum threshold</li>
            <li>Regular restocking prevents lost sales and customer dissatisfaction</li>
            <li>Consider setting up automatic purchase orders for frequently sold items</li>
            <li>Review and update minimum stock levels based on sales patterns</li>
        </ul>
    </div>
</div>

<!-- Quick Restock Modal -->
<div class="modal fade" id="quickRestockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Restock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="quickRestockForm">
                    <input type="hidden" id="restockProductId">
                    
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <input type="text" class="form-control" id="restockProductName" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Recommended Quantity</label>
                        <input type="number" class="form-control" id="restockRecommended" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Quantity to Add <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="restockQuantity" min="1" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Unit Cost</label>
                        <div class="input-group">
                            <span class="input-group-text">₦</span>
                            <input type="number" step="0.01" class="form-control" id="restockUnitCost">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Supplier (Optional)</label>
                        <input type="text" class="form-control" id="restockSupplier">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" id="restockNotes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="submitQuickRestock()">
                    <i class="fas fa-check"></i> Restock Now
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Modal -->
<div class="modal fade" id="bulkActionsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Restock Selected Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="bulkItemsList" class="mb-3"></div>
                
                <div class="mb-3">
                    <label class="form-label">Default Action</label>
                    <select class="form-select" id="bulkActionType">
                        <option value="restock_to_min">Restock to Minimum Level</option>
                        <option value="restock_custom">Custom Quantity for All</option>
                    </select>
                </div>
                
                <div class="mb-3" id="customQuantityDiv" style="display: none;">
                    <label class="form-label">Quantity for All Selected</label>
                    <input type="number" class="form-control" id="bulkCustomQuantity" min="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitBulkRestock()">
                    <i class="fas fa-boxes"></i> Restock All Selected
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Select All Functionality
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
    document.getElementById('selectAll').checked = checkbox.checked;
    document.getElementById('selectAllHeader').checked = checkbox.checked;
}

// Quick Restock
function quickRestock(productId, productName, recommendedQty) {
    document.getElementById('restockProductId').value = productId;
    document.getElementById('restockProductName').value = productName;
    document.getElementById('restockRecommended').value = recommendedQty;
    document.getElementById('restockQuantity').value = recommendedQty;
    
    const modal = new bootstrap.Modal(document.getElementById('quickRestockModal'));
    modal.show();
}

async function submitQuickRestock() {
    const productId = document.getElementById('restockProductId').value;
    const quantity = parseInt(document.getElementById('restockQuantity').value);
    const unitCost = document.getElementById('restockUnitCost').value;
    const notes = document.getElementById('restockNotes').value;
    
    if (!quantity || quantity <= 0) {
        alert('Please enter a valid quantity');
        return;
    }
    
    try {
        const response = await fetch(`/app/products/${productId}/adjust-stock`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                action_type: 'purchase',
                quantity_change: quantity,
                unit_cost: unitCost || null,
                reason: notes || 'Quick restock from low stock alert'
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('quickRestockModal'));
            modal.hide();
            
            showToast('Stock updated successfully', 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            alert(result.message || 'Failed to update stock');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error updating stock');
    }
}

// Bulk Actions
function bulkAdjustStock() {
    const selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked'))
        .map(cb => cb.value);
    
    if (selectedProducts.length === 0) {
        alert('Please select at least one product');
        return;
    }
    
    // Build list of selected items
    const listHtml = selectedProducts.map(id => {
        const row = document.querySelector(`tr[data-product-id="${id}"]`);
        const name = row.querySelector('strong').textContent;
        return `<div class="alert alert-info"><i class="fas fa-box"></i> ${name}</div>`;
    }).join('');
    
    document.getElementById('bulkItemsList').innerHTML = listHtml;
    
    const modal = new bootstrap.Modal(document.getElementById('bulkActionsModal'));
    modal.show();
}

// Toggle custom quantity field
document.addEventListener('DOMContentLoaded', function() {
    const actionSelect = document.getElementById('bulkActionType');
    if (actionSelect) {
        actionSelect.addEventListener('change', function() {
            const customDiv = document.getElementById('customQuantityDiv');
            customDiv.style.display = this.value === 'restock_custom' ? 'block' : 'none';
        });
    }
});

function submitBulkRestock() {
    alert('Bulk restock functionality - This would process all selected items');
    // Implementation would loop through selected products and call adjust-stock for each
}

function generatePurchaseOrder() {
    const selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked'));
    
    if (selectedProducts.length === 0) {
        alert('Please select products to generate purchase order');
        return;
    }
    
    alert('Purchase Order generation coming soon!');
    // This would generate a PO document with all selected items
}

function exportLowStock() {
    window.location.href = '{{ route("lounge.inventory.export") }}?stock_status=low';
}

function printLowStock() {
    window.print();
}
</script>

<style>
@media print {
    .btn, .page-header .col-auto, .card-footer, .no-print {
        display: none !important;
    }
    
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
    
    .table {
        font-size: 12px;
    }
}

.product-row:hover {
    background-color: #f8f9fa;
}
</style>
@endpush
@endsection