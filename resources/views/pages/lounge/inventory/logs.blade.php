@extends('layouts.app')
@section('title', 'Inventory Logs - Violet Marella Limited')
@push('styles')
<link href="{{ asset('assets/css/lounge.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Inventory Logs</h1>
                <p class="page-subtitle">Track all stock movements and changes</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('lounge.inventory.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Inventory
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('lounge.inventory.logs') }}">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Action Type</label>
                        <select name="action_type" class="form-select">
                            <option value="">All Actions</option>
                            <option value="sale" {{ request('action_type') === 'sale' ? 'selected' : '' }}>Sale</option>
                            <option value="purchase" {{ request('action_type') === 'purchase' ? 'selected' : '' }}>Purchase</option>
                            <option value="adjustment" {{ request('action_type') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                            <option value="return" {{ request('action_type') === 'return' ? 'selected' : '' }}>Return</option>
                            <option value="damage" {{ request('action_type') === 'damage' ? 'selected' : '' }}>Damage</option>
                            <option value="expiry" {{ request('action_type') === 'expiry' ? 'selected' : '' }}>Expiry</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Product</label>
                        <select name="product_id" class="form-select">
                            <option value="">All Products</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Stock Movement History</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Product</th>
                            <th>Action</th>
                            <th class="text-center">Change</th>
                            <th class="text-center">Previous</th>
                            <th class="text-center">New Stock</th>
                            <th>Staff</th>
                            <th>Reason</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>{{ $log->action_date->format('M d, Y H:i') }}</td>
                                <td>
                                    @if($log->product)
                                        <a href="{{ route('lounge.products.show', $log->product->id) }}">
                                            {{ $log->product->name }}
                                        </a>
                                        <br><small class="text-muted">{{ $log->product->sku }}</small>
                                    @else
                                        <span class="text-muted">Product Deleted</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $log->action_type === 'purchase' ? 'primary' : ($log->action_type === 'sale' ? 'success' : ($log->action_type === 'damage' || $log->action_type === 'expiry' ? 'danger' : 'warning')) }}">
                                        {{ $log->action_description }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $log->quantity_change > 0 ? 'success' : 'danger' }}">
                                        {{ $log->quantity_change > 0 ? '+' : '' }}{{ $log->quantity_change }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $log->previous_stock }}</td>
                                <td class="text-center">
                                    <strong>{{ $log->new_stock }}</strong>
                                </td>
                                <td>
                                    @if($log->staff)
                                        {{ $log->staff->first_name }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($log->reason)
                                        <small>{{ $log->reason }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->reference_number)
                                        <small>{{ $log->reference_number }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-history fa-3x mb-3"></i>
                                    <br>No inventory logs found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection