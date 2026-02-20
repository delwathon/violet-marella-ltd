@extends('layouts.app')
@section('title', 'Sale Details - Violet Marella Limited')
@push('styles')
<link href="{{ asset('assets/css/lounge.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Sale Details</h1>
                <p class="page-subtitle">Receipt #{{ $sale->receipt_number }}</p>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <a href="{{ route('anire-craft-store.sales.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Sales
                    </a>
                    <button class="btn btn-primary" onclick="printReceipt({{ $sale->id }})">
                        <i class="fas fa-print me-2"></i>Print Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sale Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Transaction Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Receipt Number:</strong><br>
                            <span class="h5">{{ $sale->receipt_number }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Date & Time:</strong><br>
                            {{ $sale->sale_date->format('F d, Y - h:i A') }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Customer:</strong><br>
                            @if($sale->customer)
                                <a href="{{ route('anire-craft-store.customers.show', $sale->customer->id) }}">
                                    {{ $sale->customer->full_name }}
                                </a><br>
                                <small class="text-muted">{{ $sale->customer->phone }}</small>
                            @else
                                <span class="text-muted">Walk-in Customer</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong>Served By:</strong><br>
                            @if($sale->staff)
                                {{ $sale->staff->full_name }}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Payment Method:</strong><br>
                            <span class="badge bg-{{ $sale->payment_method === 'cash' ? 'success' : ($sale->payment_method === 'card' ? 'primary' : 'info') }}">
                                {{ ucfirst($sale->payment_method) }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong><br>
                            <span class="badge bg-{{ $sale->status === 'completed' ? 'success' : ($sale->status === 'pending' ? 'warning' : 'danger') }}">
                                {{ ucfirst($sale->status) }}
                            </span>
                        </div>
                    </div>
                    @if($sale->notes)
                        <div class="row mt-3">
                            <div class="col-12">
                                <strong>Notes:</strong><br>
                                <p class="mb-0">{{ $sale->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sale Items -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Items Purchased</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Tax</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->saleItems as $item)
                                    <tr>
                                        <td>
                                            @if($item->product)
                                                <a href="{{ route('anire-craft-store.products.show', $item->product->id) }}">
                                                    {{ $item->product_name }}
                                                </a>
                                            @else
                                                {{ $item->product_name }}
                                            @endif
                                        </td>
                                        <td>{{ $item->product_sku }}</td>
                                        <td class="text-end">₦{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">₦{{ number_format($item->tax_amount, 2) }}</td>
                                        <td class="text-end">
                                            <strong>₦{{ number_format($item->total_price, 2) }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary & Payment -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Payment Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong>₦{{ number_format($sale->subtotal, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax (7.5%):</span>
                        <strong>₦{{ number_format($sale->tax_amount, 2) }}</strong>
                    </div>
                    @if($sale->discount_amount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Discount:</span>
                            <strong>-₦{{ number_format($sale->discount_amount, 2) }}</strong>
                        </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="h5 mb-0">Total:</span>
                        <strong class="h5 mb-0">₦{{ number_format($sale->total_amount, 2) }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Amount Paid:</span>
                        <strong>₦{{ number_format($sale->amount_paid, 2) }}</strong>
                    </div>
                    @if($sale->change_amount > 0)
                        <div class="d-flex justify-content-between">
                            <span>Change:</span>
                            <strong class="text-success">₦{{ number_format($sale->change_amount, 2) }}</strong>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Total Items</small>
                        <div class="h4 mb-0">{{ $sale->saleItems->sum('quantity') }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Unique Products</small>
                        <div class="h4 mb-0">{{ $sale->saleItems->count() }}</div>
                    </div>
                    <div>
                        <small class="text-muted">Payment Status</small>
                        <div>
                            <span class="badge bg-{{ $sale->payment_status === 'completed' ? 'success' : 'warning' }}">
                                {{ ucfirst($sale->payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    .sidebar, .page-header .btn, .btn-group, nav {
        display: none !important;
    }
    
    .content-area {
        margin-left: 0 !important;
        padding: 20px !important;
    }
    
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
        page-break-inside: avoid;
    }
}
</style>

@push('scripts')
<script>
    function printReceipt(saleId) {
        window.open(`{{ route('anire-craft-store.sales.index') }}/${saleId}/receipt`, '_blank');
    }
</script>
@endpush
@endsection
