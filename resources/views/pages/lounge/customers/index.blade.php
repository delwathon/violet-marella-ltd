@extends('layouts.app')
@section('title', 'Customer Management')

@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Customer Management</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Customers</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <a href="{{ route('lounge.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-cash-register"></i> Back to POS
                    </a>
                    <a href="{{ route('lounge.customers.export') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-download"></i> Export
                    </a>
                    <a href="{{ route('lounge.customers.create') }}" class="btn btn-success">
                        <i class="fas fa-user-plus"></i> Add Customer
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
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $totalCustomers }}</div>
                    <div class="stat-label">Total Customers</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $activeCustomers }}</div>
                    <div class="stat-label">Active Customers</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">₦{{ number_format($totalSpent, 2) }}</div>
                    <div class="stat-label">Total Revenue</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">₦{{ number_format($avgOrderValue, 2) }}</div>
                    <div class="stat-label">Avg Order Value</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('lounge.customers.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search by name, email, or phone..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="customer_type" class="form-select">
                            <option value="">All Types</option>
                            <option value="walk-in" {{ request('customer_type') == 'walk-in' ? 'selected' : '' }}>Walk-in</option>
                            <option value="regular" {{ request('customer_type') == 'regular' ? 'selected' : '' }}>Regular</option>
                            <option value="wholesale" {{ request('customer_type') == 'wholesale' ? 'selected' : '' }}>Wholesale</option>
                            <option value="staff" {{ request('customer_type') == 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>
                    <div class="col-md-3">
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
                            <a href="{{ route('lounge.customers.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Customer Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Type</th>
                            <th>Total Orders</th>
                            <th>Total Spent</th>
                            <th>Loyalty Points</th>
                            <th>Last Purchase</th>
                            <th>Status</th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td>
                                    <strong>{{ $customer->full_name }}</strong>
                                    <br><small class="text-muted">ID: {{ $customer->id }}</small>
                                </td>
                                <td>
                                    @if($customer->email)
                                        <i class="fas fa-envelope text-muted"></i> {{ $customer->email }}<br>
                                    @endif
                                    <i class="fas fa-phone text-muted"></i> {{ $customer->phone }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $customer->customer_type === 'wholesale' ? 'warning' : ($customer->customer_type === 'regular' ? 'info' : 'secondary') }}">
                                        {{ ucfirst($customer->customer_type) }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $customer->total_orders }}</strong>
                                </td>
                                <td>
                                    <strong class="text-success">₦{{ number_format($customer->total_spent, 2) }}</strong>
                                    @if($customer->total_orders > 0)
                                        <br><small class="text-muted">Avg: ₦{{ number_format($customer->average_order_value, 2) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-warning">
                                        <i class="fas fa-star"></i> {{ number_format($customer->loyalty_points, 0) }}
                                    </span>
                                </td>
                                <td>
                                    @if($customer->last_purchase_date)
                                        {{ $customer->last_purchase_date->format('M d, Y') }}
                                        <br><small class="text-muted">{{ $customer->last_purchase_date->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted">Never</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $customer->is_active ? 'success' : 'secondary' }}">
                                        {{ $customer->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('lounge.customers.show', $customer->id) }}" 
                                           class="btn btn-outline-info" 
                                           title="View Profile">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('lounge.customers.edit', $customer->id) }}" 
                                           class="btn btn-outline-primary" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-outline-danger" 
                                                onclick="deleteCustomer({{ $customer->id }})" 
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <br><h5>No customers found</h5>
                                    <p>Try adjusting your filters or <a href="{{ route('lounge.customers.create') }}">add a new customer</a></p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($customers->hasPages())
                <div class="mt-3">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteCustomer(customerId) {
    if (!confirm('Are you sure you want to delete this customer? This action cannot be undone.')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `{{ route('lounge.customers.index') }}/${customerId}`;
    
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
