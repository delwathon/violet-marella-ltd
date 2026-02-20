@extends('layouts.app')
@section('title', 'Sales - Violet Marella Limited')
@push('styles')
<link href="{{ asset('assets/css/lounge.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Sales Management</h1>
                <p class="page-subtitle">View and manage all sales transactions</p>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <a href="{{ route('anire-craft-store.index') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>New Sale
                    </a>
                    <button class="btn btn-outline-primary" onclick="exportSales()">
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
                <div class="stat-icon bg-success">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">₦{{ number_format($totalSales, 2) }}</div>
                    <div class="stat-label">Total Sales</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">₦{{ number_format($todaySales, 2) }}</div>
                    <div class="stat-label">Today's Sales</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">₦{{ number_format($thisMonthSales, 2) }}</div>
                    <div class="stat-label">This Month</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ number_format($totalTransactions) }}</div>
                    <div class="stat-label">Total Transactions</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('anire-craft-store.sales.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Receipt # or Customer..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            <option value="">All Methods</option>
                            <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="card" {{ request('payment_method') === 'card' ? 'selected' : '' }}>Card</option>
                            <option value="transfer" {{ request('payment_method') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                            <option value="mobile_money" {{ request('payment_method') === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Sales Transactions</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Receipt #</th>
                            <th>Date & Time</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Subtotal</th>
                            <th>Tax</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td>
                                    <strong>{{ $sale->receipt_number }}</strong>
                                </td>
                                <td>{{ $sale->sale_date->format('M d, Y H:i') }}</td>
                                <td>
                                    @if($sale->customer)
                                        <a href="{{ route('anire-craft-store.customers.show', $sale->customer->id) }}">
                                            {{ $sale->customer->full_name }}
                                        </a>
                                    @else
                                        <span class="text-muted">Walk-in Customer</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $sale->saleItems->count() }} items</span>
                                </td>
                                <td>₦{{ number_format($sale->subtotal, 2) }}</td>
                                <td>₦{{ number_format($sale->tax_amount, 2) }}</td>
                                <td>
                                    <strong>₦{{ number_format($sale->total_amount, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $sale->payment_method === 'cash' ? 'success' : ($sale->payment_method === 'card' ? 'primary' : 'info') }}">
                                        {{ ucfirst($sale->payment_method) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $sale->status === 'completed' ? 'success' : ($sale->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('anire-craft-store.sales.show', $sale->id) }}" class="btn btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-outline-secondary" onclick="printReceipt({{ $sale->id }})" title="Print Receipt">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="fas fa-receipt fa-3x mb-3"></i>
                                    <br>No sales found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $sales->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
function exportSales() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = '{{ route("anire-craft-store.sales.export") }}?' + params.toString();
}

function printReceipt(saleId) {
    window.open(`{{ route('anire-craft-store.sales.index') }}/${saleId}/receipt`, '_blank');
}
</script>
@endpush
@endsection
