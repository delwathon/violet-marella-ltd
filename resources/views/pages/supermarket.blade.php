@extends('layouts.app')
@section('title', 'Mini Supermarket POS')
@push('styles')
<link href="{{ asset('assets/css/supermarket.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Mini Supermarket POS</h1>
                <p class="page-subtitle">Point of Sale system with inventory management</p>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#newSaleModal">
                        <i class="fas fa-plus me-2"></i>New Sale
                    </button>
                    <button class="btn btn-outline-primary">
                        <i class="fas fa-cash-register me-2"></i>Cash Drawer
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-print me-2"></i>X-Report
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-cash-register"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="todaySales">₦{{ number_format($todaySales ?? 0, 2) }}</div>
                    <div class="stat-label">Today's Sales</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="todayTransactions">{{ $todayTransactions ?? 0 }}</div>
                    <div class="stat-label">Transactions</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="totalStock">{{ $totalStock ?? 0 }}</div>
                    <div class="stat-label">Items in Stock</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="customersServed">{{ $customersServed ?? 0 }}</div>
                    <div class="stat-label">Customers Served</div>
                </div>
            </div>
        </div>
    </div>
    <!-- POS Interface -->
    <div class="row">
        <!-- Product Search & Selection -->
        <div class="col-lg-8">
            <!-- Search Bar -->
            <div class="search-section mb-4">
                <div class="row">
                    <div class="col-md-8">
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control" id="productSearch" placeholder="Search products by name or barcode...">
                            <button class="btn btn-outline-primary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-outline-secondary" type="button" title="Barcode Scanner">
                                <i class="fas fa-barcode"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select form-select-lg" id="categoryFilter">
                            <option value="">All Categories</option>
                            @if(isset($categories))
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>
            <!-- Product Grid -->
            <div class="product-grid" id="productGrid">
                @if(isset($products) && $products->count() > 0)
                    <div class="row">
                        @foreach($products as $product)
                            <div class="col-md-4 col-lg-3 mb-3">
                                <div class="product-card card h-100" data-product-id="{{ $product->id }}">
                                    <div class="card-body d-flex flex-column">
                                        <div class="product-image mb-2">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded" alt="{{ $product->name }}">
                                            @else
                                                <div class="placeholder-image bg-light rounded d-flex align-items-center justify-content-center" style="height: 120px;">
                                                    <i class="fas fa-image text-muted fa-2x"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <h6 class="card-title">{{ $product->name }}</h6>
                                        <p class="card-text text-muted small">{{ $product->category->name }}</p>
                                        <div class="product-info">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="price fw-bold">₦{{ number_format($product->price, 2) }}</span>
                                                <span class="stock text-muted small">
                                                    @if($product->track_stock)
                                                        Stock: {{ $product->stock_quantity }}
                                                    @else
                                                        <i class="fas fa-infinity text-success"></i>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <input type="number" class="form-control form-control-sm quantity-input" value="1" min="1" max="{{ $product->track_stock ? $product->stock_quantity : 999 }}">
                                                <button class="btn btn-primary btn-sm add-to-cart" data-product-id="{{ $product->id }}">
                                                    <i class="fas fa-cart-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No products available</h5>
                        <p class="text-muted">Products will appear here once they are added to the system.</p>
                    </div>
                @endif
            </div>
            <!-- Pagination -->
            <nav aria-label="Product pagination" class="mt-4">
                <ul class="pagination justify-content-center" id="productPagination">
                    <!-- Pagination will be loaded here -->
                </ul>
            </nav>
        </div>
        <!-- Shopping Cart & Checkout -->
        <div class="col-lg-4">
            <div class="cart-panel">
                <div class="cart-header">
                    <h5 class="mb-0">Shopping Cart</h5>
                    <div class="cart-actions">
                        <button class="btn btn-sm btn-outline-warning" title="Hold Transaction">
                            <i class="fas fa-pause"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash me-1"></i>Clear All
                        </button>
                    </div>
                </div>
                <div class="cart-items" id="cartItems">
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Cart is empty</p>
                        <small>Start scanning or selecting products</small>
                    </div>
                </div>
                <!-- Cart Controls -->
                <div class="cart-controls">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <button class="btn btn-outline-secondary btn-sm w-100">
                                <i class="fas fa-percent me-1"></i>Discount
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-info btn-sm w-100">
                                <i class="fas fa-sticky-note me-1"></i>Note
                            </button>
                        </div>
                    </div>
                </div>
                <div class="cart-summary">
                    <div class="summary-row">
                        <span>Items:</span>
                        <span id="cartItemCount">0</span>
                    </div>
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span id="cartSubtotal">₦0</span>
                    </div>
                    <div class="summary-row">
                        <span>Tax (7.5%):</span>
                        <span id="cartTax">₦0</span>
                    </div>
                    <div class="summary-row discount-row" id="discountRow" style="display: none;">
                        <span>Discount:</span>
                        <span id="cartDiscount" class="text-success">₦0</span>
                    </div>
                    <hr>
                    <div class="summary-row total-row">
                        <strong>
                            <span>Total:</span>
                            <span id="cartTotal">₦0</span>
                        </strong>
                    </div>
                </div>
                <div class="checkout-actions">
                    <button class="btn btn-success btn-lg w-100 mb-2" id="checkoutBtn" disabled>
                        <i class="fas fa-credit-card me-2"></i>Checkout
                    </button>
                    <div class="row g-2">
                        <div class="col-6">
                            <button class="btn btn-outline-primary w-100">
                                <i class="fas fa-save me-1"></i>Quote
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-secondary w-100">
                                <i class="fas fa-history me-1"></i>Held
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Recent Transactions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Transactions</h5>
                    <div class="d-flex gap-2">
                        <input type="date" class="form-control form-control-sm" style="width: 150px;">
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i>View All
                        </button>
                        <button class="btn btn-sm btn-outline-success">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Receipt #</th>
                                    <th>Time</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($recentTransactions) && $recentTransactions->count() > 0)
                                    @foreach($recentTransactions as $sale)
                                        <tr>
                                            <td>
                                                <span class="fw-bold">{{ $sale->receipt_number }}</span>
                                            </td>
                                            <td>{{ $sale->sale_date->format('H:i') }}</td>
                                            <td>
                                                @if($sale->customer)
                                                    {{ $sale->customer->full_name }}
                                                @else
                                                    <span class="text-muted">Walk-in</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $sale->saleItems->count() }} items</span>
                                            </td>
                                            <td>
                                                <strong>₦{{ number_format($sale->total_amount, 2) }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $sale->payment_method === 'cash' ? 'success' : ($sale->payment_method === 'card' ? 'primary' : 'info') }}">
                                                    {{ ucfirst($sale->payment_method) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $sale->status === 'completed' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($sale->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-receipt fa-2x mb-2"></i>
                                            <br>No recent transactions
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- New Sale Modal -->
<div class="modal fade" id="newSaleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Customer Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="customerForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Customer Name (Optional)</label>
                                <input type="text" class="form-control" id="customerName" placeholder="Enter customer name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone Number (Optional)</label>
                                <input type="tel" class="form-control" id="customerPhone" placeholder="+234 xxx xxx xxxx">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email (Optional)</label>
                                <input type="email" class="form-control" id="customerEmail" placeholder="customer@email.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Customer Type</label>
                                <select class="form-select" id="customerType">
                                    <option value="walk-in">Walk-in Customer</option>
                                    <option value="regular">Regular Customer</option>
                                    <option value="wholesale">Wholesale Customer</option>
                                    <option value="staff">Staff Purchase</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="isRegularCustomer">
                        <label class="form-check-label" for="isRegularCustomer">
                            Apply loyalty discount (5%)
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Skip</button>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-shopping-cart me-2"></i>Start Sale
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Processing</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="payment-summary mb-4">
                    <h4 class="text-center mb-3">Total Amount</h4>
                    <div class="total-display text-center">
                        <span class="total-amount" id="paymentTotal">₦0</span>
                    </div>
                    <div class="transaction-details mt-3">
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted">Items</small>
                                <div id="paymentItems">0</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Subtotal</small>
                                <div id="paymentSubtotal">₦0</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Tax</small>
                                <div id="paymentTax">₦0</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="payment-methods">
                    <div class="row">
                        <div class="col-6">
                            <button class="btn btn-outline-success payment-method w-100" data-method="cash">
                                <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                <div>Cash</div>
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-primary payment-method w-100" data-method="card">
                                <i class="fas fa-credit-card fa-2x mb-2"></i>
                                <div>Card</div>
                            </button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <button class="btn btn-outline-info payment-method w-100" data-method="transfer">
                                <i class="fas fa-mobile-alt fa-2x mb-2"></i>
                                <div>Bank Transfer</div>
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-warning payment-method w-100" data-method="split">
                                <i class="fas fa-layer-group fa-2x mb-2"></i>
                                <div>Split Payment</div>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Cash Payment Section -->
                <div class="cash-input mt-4" id="cashInput" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label">Amount Received</label>
                        <input type="number" class="form-control form-control-lg text-center" id="amountReceived" placeholder="0" step="0.01">
                    </div>
                    <div class="change-display text-center">
                        <strong>Change: <span id="changeAmount" class="text-success">₦0</span></strong>
                    </div>
                </div>
                <!-- Split Payment Section -->
                <div class="split-payment mt-4" id="splitPayment" style="display: none;">
                    <h6>Split Payment</h6>
                    <div class="split-methods" id="splitMethods">
                        <!-- Split payment methods will be added here -->
                    </div>
                    <button class="btn btn-sm btn-outline-secondary mt-2">
                        <i class="fas fa-plus me-1"></i>Add Payment Method
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="completePaymentBtn" disabled>
                    <i class="fas fa-check me-2"></i>Complete Payment
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Product Details Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="productModalBody">
                <!-- Product details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-cart-plus me-2"></i>Add to Cart
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Receipt Preview Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Receipt Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="receipt" id="receiptContent">
                    <!-- Receipt content will be generated here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-print me-2"></i>Print Receipt
                </button>
                <button type="button" class="btn btn-outline-primary">
                    <i class="fas fa-envelope me-2"></i>Email Receipt
                </button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="{{ asset('assets/js/supermarket-laravel.js') }}"></script>
@endpush
@endsection
