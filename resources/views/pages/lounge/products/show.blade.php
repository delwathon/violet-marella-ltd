@extends('layouts.app')
@section('title', 'Product Details')

@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">{{ $product->name }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('lounge.products.index') }}">Products</a></li>
                        <li class="breadcrumb-item active">{{ $product->name }}</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <a href="{{ route('lounge.products.edit', $product->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('lounge.products.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Product Information -->
        <div class="col-lg-8">
            <!-- Basic Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Product Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="img-fluid rounded shadow-sm">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                     style="height: 250px;">
                                    <i class="fas fa-image fa-5x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Product Name:</th>
                                    <td><strong>{{ $product->name }}</strong></td>
                                </tr>
                                <tr>
                                    <th>SKU:</th>
                                    <td>
                                        <span class="badge bg-secondary">{{ $product->sku }}</span>
                                    </td>
                                </tr>
                                @if($product->barcode)
                                <tr>
                                    <th>Barcode:</th>
                                    <td>
                                        <span class="badge bg-info">{{ $product->barcode }}</span>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Category:</th>
                                    <td>
                                        <span class="badge" style="background-color: {{ $product->category->color ?? '#6c757d' }}">
                                            {{ $product->category->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Unit:</th>
                                    <td>{{ $product->unit }}</td>
                                </tr>
                                @if($product->brand)
                                <tr>
                                    <th>Brand:</th>
                                    <td>{{ $product->brand }}</td>
                                </tr>
                                @endif
                                @if($product->supplier)
                                <tr>
                                    <th>Supplier:</th>
                                    <td>{{ $product->supplier }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        @if($product->is_featured)
                                            <span class="badge bg-warning">Featured</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($product->description)
                    <div class="mt-3">
                        <h6>Description</h6>
                        <p class="text-muted">{{ $product->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Pricing Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Pricing</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="pricing-card">
                                <small class="text-muted">Selling Price</small>
                                <h4 class="text-success mb-0">₦{{ number_format($product->price, 2) }}</h4>
                                @if($product->tax_rate)
                                    <small class="text-muted">+{{ $product->tax_rate }}% tax</small>
                                @endif
                            </div>
                        </div>
                        @if($product->cost_price)
                        <div class="col-md-3">
                            <div class="pricing-card">
                                <small class="text-muted">Cost Price</small>
                                <h4 class="text-primary mb-0">₦{{ number_format($product->cost_price, 2) }}</h4>
                                <small class="text-success">{{ number_format($product->profit_margin, 1) }}% margin</small>
                            </div>
                        </div>
                        @endif
                        @if($product->wholesale_price)
                        <div class="col-md-3">
                            <div class="pricing-card">
                                <small class="text-muted">Wholesale Price</small>
                                <h4 class="text-info mb-0">₦{{ number_format($product->wholesale_price, 2) }}</h4>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-3">
                            <div class="pricing-card">
                                <small class="text-muted">Price with Tax</small>
                                <h4 class="text-warning mb-0">₦{{ number_format($product->price_with_tax, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory History -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Inventory History</h5>
                    <button class="btn btn-sm btn-primary" onclick="showAdjustStockModal()">
                        <i class="fas fa-plus"></i> Adjust Stock
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Action</th>
                                    <th>Previous</th>
                                    <th>New</th>
                                    <th>Staff</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inventoryHistory as $log)
                                    <tr>
                                        <td>{{ $log->action_date->format('M d, Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $log->action_type === 'sale' ? 'success' : ($log->action_type === 'purchase' ? 'primary' : 'warning') }}">
                                                {{ $log->action_description }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="{{ $log->quantity_change > 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $log->quantity_change > 0 ? '+' : '' }}{{ $log->quantity_change }}
                                            </span>
                                        </td>
                                        <td>{{ $log->previous_stock }}</td>
                                        <td><strong>{{ $log->new_stock }}</strong></td>
                                        <td>{{ $log->staff->full_name ?? 'System' }}</td>
                                        <td>{{ $log->reason ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No inventory history available
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Sales -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Recent Sales</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Receipt #</th>
                                    <th>Customer</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSales as $saleItem)
                                    <tr>
                                        <td>{{ $saleItem->sale->sale_date->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('lounge.sales.show', $saleItem->sale_id) }}">
                                                {{ $saleItem->sale->receipt_number }}
                                            </a>
                                        </td>
                                        <td>{{ $saleItem->sale->customer->full_name ?? 'Walk-in' }}</td>
                                        <td>{{ $saleItem->quantity }}</td>
                                        <td>₦{{ number_format($saleItem->unit_price, 2) }}</td>
                                        <td><strong>₦{{ number_format($saleItem->total_price, 2) }}</strong></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No sales recorded yet
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Stock Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Stock Information</h5>
                </div>
                <div class="card-body">
                    @if($product->track_stock)
                        <div class="text-center mb-3">
                            <h2 class="mb-0">
                                <span class="badge bg-{{ $product->isOutOfStock() ? 'danger' : ($product->isLowStock() ? 'warning' : 'success') }} p-3">
                                    {{ $product->stock_quantity }}
                                </span>
                            </h2>
                            <p class="text-muted mb-0">Current Stock</p>
                            @if($product->isLowStock())
                                <small class="text-danger">⚠️ Low stock alert!</small>
                            @endif
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Minimum Level:</span>
                            <strong>{{ $product->minimum_stock_level ?? 'Not set' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Maximum Level:</span>
                            <strong>{{ $product->maximum_stock_level ?? 'Not set' }}</strong>
                        </div>
                        @if($product->expiry_date)
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Expiry Date:</span>
                            <strong class="{{ $product->expiry_date->isPast() ? 'text-danger' : '' }}">
                                {{ $product->expiry_date->format('M d, Y') }}
                            </strong>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-infinity fa-3x text-success mb-2"></i>
                            <p class="mb-0">Stock tracking disabled</p>
                            <small class="text-muted">Unlimited stock</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sales Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Sales Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total Units Sold</span>
                            <h4 class="mb-0 text-primary">{{ $product->total_sales }}</h4>
                        </div>
                    </div>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total Revenue</span>
                            <h4 class="mb-0 text-success">₦{{ number_format($product->total_revenue, 2) }}</h4>
                        </div>
                    </div>
                    @if($product->cost_price && $product->total_sales > 0)
                    <div class="stat-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Est. Profit</span>
                            <h4 class="mb-0 text-info">
                                ₦{{ number_format(($product->price - $product->cost_price) * $product->total_sales, 2) }}
                            </h4>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="showAdjustStockModal()">
                            <i class="fas fa-boxes"></i> Adjust Stock
                        </button>
                        <a href="{{ route('lounge.products.edit', $product->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit"></i> Edit Product
                        </a>
                        <button class="btn btn-outline-info" onclick="printBarcode()">
                            <i class="fas fa-barcode"></i> Print Barcode
                        </button>
                        <a href="{{ route('lounge.index') }}" class="btn btn-outline-success">
                            <i class="fas fa-cash-register"></i> Sell in POS
                        </a>
                        <hr>
                        <button class="btn btn-outline-danger" onclick="deleteProduct()">
                            <i class="fas fa-trash"></i> Delete Product
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Adjust Stock Modal -->
<div class="modal fade" id="adjustStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adjust Stock - {{ $product->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="adjustStockForm">
                    <div class="mb-3">
                        <label class="form-label">Current Stock</label>
                        <input type="text" class="form-control" value="{{ $product->stock_quantity }}" readonly>
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
                        <div class="input-group">
                            <span class="input-group-text">₦</span>
                            <input type="number" step="0.01" class="form-control" id="adjustUnitCost">
                        </div>
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

<!-- Print Barcode Modal -->
<div class="modal fade" id="printBarcodeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Print Barcode - {{ $product->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Settings Panel -->
                    <div class="col-md-5">
                        <h6 class="mb-3">Label Settings</h6>
                        
                        <div class="mb-3">
                            <label class="form-label">Number of Labels</label>
                            <input type="number" class="form-control" id="labelQuantity" value="1" min="1" max="100" onchange="updateBarcodePreview()">
                            <small class="text-muted">How many labels to print</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Label Size</label>
                            <select class="form-select" id="labelSize" onchange="updateBarcodePreview()">
                                <option value="small">Small (40x25mm)</option>
                                <option value="medium" selected>Medium (50x30mm)</option>
                                <option value="large">Large (60x40mm)</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="showProductName" checked onchange="updateBarcodePreview()">
                                <label class="form-check-label" for="showProductName">
                                    Show Product Name
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="showPrice" checked onchange="updateBarcodePreview()">
                                <label class="form-check-label" for="showPrice">
                                    Show Price
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="showSKU" onchange="updateBarcodePreview()">
                                <label class="form-check-label" for="showSKU">
                                    Show SKU
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3" id="priceTypeSection">
                            <label class="form-label">Price to Display</label>
                            <select class="form-select" id="priceType" onchange="updateBarcodePreview()">
                                <option value="retail">Retail Price (₦{{ number_format($product->price, 2) }})</option>
                                @if($product->wholesale_price)
                                <option value="wholesale">Wholesale Price (₦{{ number_format($product->wholesale_price, 2) }})</option>
                                @endif
                                <option value="with_tax">Price with Tax (₦{{ number_format($product->price_with_tax, 2) }})</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Preview Panel -->
                    <div class="col-md-7">
                        <h6 class="mb-3">Preview</h6>
                        <div id="barcodePreviewContainer" class="border rounded p-3 bg-light" style="min-height: 300px; overflow-y: auto; max-height: 400px;">
                            <!-- Barcode labels will be generated here -->
                        </div>
                        
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="fas fa-info-circle"></i> 
                            <small>Preview shows actual print size. Adjust settings on the left.</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="printBarcodeLabels()">
                    <i class="fas fa-print"></i> Print Labels
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.pricing-card {
    padding: 1rem;
    border: 1px solid #e0e0e0;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
}

.stat-item {
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 0.5rem;
}

/* Barcode Label Styles */
.barcode-label {
    display: inline-block;
    border: 1px dashed #ccc;
    padding: 10px 8px;
    margin: 8px;
    text-align: center;
    background: white;
    page-break-inside: avoid;
    font-family: Arial, sans-serif;
    box-sizing: border-box;
}

.barcode-label.small {
    width: 150px;
    min-height: 95px;
}

.barcode-label.medium {
    width: 190px;
    min-height: 115px;
}

.barcode-label.large {
    width: 225px;
    min-height: 150px;
}

.barcode-label .product-name {
    font-size: 11px;
    font-weight: bold;
    margin-bottom: 4px;
    line-height: 1.2;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.barcode-label.medium .product-name {
    font-size: 12px;
}

.barcode-label.large .product-name {
    font-size: 14px;
}

.barcode-label .barcode-img {
    margin: 5px 0;
    max-width: 100%;
    height: auto;
}

.barcode-label .barcode-number {
    font-size: 10px;
    margin-top: 2px;
    font-family: monospace;
}

.barcode-label.medium .barcode-number {
    font-size: 11px;
}

.barcode-label.large .barcode-number {
    font-size: 12px;
}

.barcode-label .price {
    font-size: 13px;
    font-weight: bold;
    margin-top: 4px;
    color: #2c5f2d;
}

.barcode-label.medium .price {
    font-size: 14px;
}

.barcode-label.large .price {
    font-size: 16px;
}

.barcode-label .sku {
    font-size: 9px;
    color: #666;
    margin-top: 2px;
}

/* Print styles */
@media print {
    body * {
        visibility: hidden;
    }
    
    #printableBarcodes,
    #printableBarcodes * {
        visibility: visible;
    }
    
    #printableBarcodes {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    
    .barcode-label {
        border: 1px solid #000;
        margin: 5mm;
    }
    
    @page {
        margin: 10mm;
    }
}
</style>
@endpush

@push('scripts')
<!-- JsBarcode Library for barcode generation -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<script>
// Product data for barcode generation
const productData = {
    name: "{{ $product->name }}",
    sku: "{{ $product->sku }}",
    barcode: "{{ $product->barcode ?? $product->sku }}",
    price: {{ $product->price }},
    @if($product->wholesale_price)
    wholesalePrice: {{ $product->wholesale_price }},
    @endif
    priceWithTax: {{ $product->price_with_tax }}
};

function showAdjustStockModal() {
    document.getElementById('adjustQuantity').value = '';
    document.getElementById('adjustUnitCost').value = '';
    document.getElementById('adjustReason').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('adjustStockModal'));
    modal.show();
}

async function submitStockAdjustment() {
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
        const response = await fetch('{{ route('lounge.products.adjust-stock', $product->id) }}', {
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

function printBarcode() {
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('printBarcodeModal'));
    modal.show();
    
    // Generate initial preview
    setTimeout(() => {
        updateBarcodePreview();
    }, 300);
}

function updateBarcodePreview() {
    const quantity = parseInt(document.getElementById('labelQuantity').value) || 1;
    const size = document.getElementById('labelSize').value;
    const showName = document.getElementById('showProductName').checked;
    const showPrice = document.getElementById('showPrice').checked;
    const showSKU = document.getElementById('showSKU').checked;
    const priceType = document.getElementById('priceType').value;
    
    // Toggle price type section visibility
    document.getElementById('priceTypeSection').style.display = showPrice ? 'block' : 'none';
    
    // Get price based on selection
    let price;
    switch(priceType) {
        case 'wholesale':
            price = productData.wholesalePrice;
            break;
        case 'with_tax':
            price = productData.priceWithTax;
            break;
        default:
            price = productData.price;
    }
    
    const container = document.getElementById('barcodePreviewContainer');
    container.innerHTML = '';
    
    // Generate multiple labels
    for (let i = 0; i < Math.min(quantity, 50); i++) {
        const label = createBarcodeLabel(size, showName, showPrice, showSKU, price);
        container.appendChild(label);
    }
    
    if (quantity > 50) {
        const notice = document.createElement('p');
        notice.className = 'text-muted text-center mt-3';
        notice.innerHTML = '<small>Showing first 50 labels. All ' + quantity + ' will be printed.</small>';
        container.appendChild(notice);
    }
}

function createBarcodeLabel(size, showName, showPrice, showSKU, price) {
    const label = document.createElement('div');
    label.className = `barcode-label ${size}`;
    
    // Product name
    if (showName) {
        const nameDiv = document.createElement('div');
        nameDiv.className = 'product-name';
        nameDiv.textContent = productData.name;
        nameDiv.title = productData.name;
        label.appendChild(nameDiv);
    }
    
    // Barcode
    const barcodeSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    barcodeSvg.classList.add('barcode-img');
    label.appendChild(barcodeSvg);
    
    // Generate barcode
    try {
        JsBarcode(barcodeSvg, productData.barcode, {
            format: productData.barcode.length === 13 ? 'EAN13' : 
                    productData.barcode.length === 12 ? 'UPC' : 
                    productData.barcode.length === 8 ? 'EAN8' : 'CODE128',
            width: size === 'small' ? 1 : size === 'medium' ? 1.2 : 1.5,
            height: size === 'small' ? 40 : size === 'medium' ? 50 : 60,
            displayValue: false,
            margin: 2
        });
    } catch (e) {
        // Fallback if barcode generation fails
        JsBarcode(barcodeSvg, productData.barcode, {
            format: 'CODE128',
            width: size === 'small' ? 1 : size === 'medium' ? 1.2 : 1.5,
            height: size === 'small' ? 40 : size === 'medium' ? 50 : 60,
            displayValue: false,
            margin: 2
        });
    }
    
    // Barcode number
    const barcodeNumber = document.createElement('div');
    barcodeNumber.className = 'barcode-number';
    barcodeNumber.textContent = productData.barcode;
    label.appendChild(barcodeNumber);
    
    // Price
    if (showPrice) {
        const priceDiv = document.createElement('div');
        priceDiv.className = 'price';
        priceDiv.textContent = '₦' + price.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        label.appendChild(priceDiv);
    }
    
    // SKU
    if (showSKU) {
        const skuDiv = document.createElement('div');
        skuDiv.className = 'sku';
        skuDiv.textContent = 'SKU: ' + productData.sku;
        label.appendChild(skuDiv);
    }
    
    return label;
}

function printBarcodeLabels() {
    const quantity = parseInt(document.getElementById('labelQuantity').value) || 1;
    const size = document.getElementById('labelSize').value;
    const showName = document.getElementById('showProductName').checked;
    const showPrice = document.getElementById('showPrice').checked;
    const showSKU = document.getElementById('showSKU').checked;
    const priceType = document.getElementById('priceType').value;
    
    // Get price based on selection
    let price;
    switch(priceType) {
        case 'wholesale':
            price = productData.wholesalePrice;
            break;
        case 'with_tax':
            price = productData.priceWithTax;
            break;
        default:
            price = productData.price;
    }
    
    // Create a hidden container for printing
    let printContainer = document.getElementById('printableBarcodes');
    if (!printContainer) {
        printContainer = document.createElement('div');
        printContainer.id = 'printableBarcodes';
        printContainer.style.display = 'none';
        document.body.appendChild(printContainer);
    }
    
    printContainer.innerHTML = '';
    
    // Generate all labels for printing
    for (let i = 0; i < quantity; i++) {
        const label = createBarcodeLabel(size, showName, showPrice, showSKU, price);
        printContainer.appendChild(label);
    }
    
    // Show print container
    printContainer.style.display = 'block';
    
    // Print
    window.print();
    
    // Hide print container after printing
    setTimeout(() => {
        printContainer.style.display = 'none';
    }, 1000);
}

function deleteProduct() {
    if (!confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('lounge.products.destroy', $product->id) }}';
    
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
