@extends('layouts.app')
@section('title', 'Studio Customers')

@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-area">
    <div class="page-header mb-4">
        <div class="row align-items-center gy-3">
            <div class="col-md">
                <h1 class="page-title mb-1">Studio Customers</h1>
                <p class="text-muted mb-0">Manage customer profiles, status, and studio spending history.</p>
            </div>
            <div class="col-md-auto d-flex gap-2 flex-wrap">
                <a href="{{ route('photo-studio.customers.export') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-file-csv me-2"></i>Export CSV
                </a>
                <a href="{{ route('photo-studio.customers.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>Add Customer
                </a>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('photo-studio.customers.index') }}" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" name="search" value="{{ $search }}" placeholder="Name, phone, email">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Filter</label>
                    <select name="filter" class="form-select">
                        <option value="">All</option>
                        <option value="active" {{ $filter === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="blacklisted" {{ $filter === 'blacklisted' ? 'selected' : '' }}>Blacklisted</option>
                        <option value="vip" {{ $filter === 'vip' ? 'selected' : '' }}>VIP</option>
                        <option value="new" {{ $filter === 'new' ? 'selected' : '' }}>New</option>
                        <option value="recent" {{ $filter === 'recent' ? 'selected' : '' }}>Recent</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-search me-2"></i>Apply</button>
                    <a href="{{ route('photo-studio.customers.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($customers->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No customers found</h5>
                <p class="text-muted">Try changing your filters or add a new customer.</p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Sessions</th>
                            <th>Total Spent</th>
                            <th>Tier</th>
                            <th>Status</th>
                            <th>Last Visit</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="customer-avatar-sm">{{ $customer->initials }}</div>
                                    <div>
                                        <div class="fw-semibold">{{ $customer->name }}</div>
                                        <small class="text-muted">ID: {{ $customer->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>{{ $customer->phone }}</div>
                                <small class="text-muted">{{ $customer->email ?: 'No email' }}</small>
                            </td>
                            <td>{{ number_format($customer->total_sessions) }}</td>
                            <td class="fw-semibold text-success">{{ $customer->formatted_total_spent }}</td>
                            <td><span class="badge bg-primary">{{ $customer->tier }}</span></td>
                            <td>
                                <span class="badge bg-{{ $customer->is_blacklisted ? 'danger' : ($customer->is_active ? 'success' : 'secondary') }}">
                                    {{ $customer->status_label }}
                                </span>
                            </td>
                            <td>{{ $customer->last_visit ? $customer->last_visit->format('d M Y') : 'Never' }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('photo-studio.customers.show', $customer->id) }}" class="btn btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('photo-studio.customers.edit', $customer->id) }}" class="btn btn-outline-secondary" title="Edit"><i class="fas fa-edit"></i></a>
                                    <button class="btn btn-outline-info" onclick="updateCustomerStats({{ $customer->id }})" title="Refresh stats"><i class="fas fa-sync"></i></button>
                                    @if($customer->is_blacklisted)
                                    <button class="btn btn-outline-success" onclick="removeBlacklist({{ $customer->id }})" title="Remove blacklist"><i class="fas fa-user-check"></i></button>
                                    @else
                                    <button class="btn btn-outline-warning" onclick="blacklistCustomer({{ $customer->id }})" title="Blacklist"><i class="fas fa-user-slash"></i></button>
                                    @endif
                                    <button class="btn btn-outline-danger" onclick="deleteCustomer({{ $customer->id }})" title="Delete"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        @if($customers->hasPages())
        <div class="card-footer">
            {{ $customers->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
const blacklistTemplate = @json(route('photo-studio.customers.blacklist', ['id' => '__ID__']));
const unblacklistTemplate = @json(route('photo-studio.customers.remove-blacklist', ['id' => '__ID__']));
const updateStatsTemplate = @json(route('photo-studio.customers.update-statistics', ['id' => '__ID__']));
const deleteCustomerTemplate = @json(route('photo-studio.customers.destroy', ['id' => '__ID__']));

function pathFromTemplate(template, id) {
    return template.replace('__ID__', String(id));
}

async function blacklistCustomer(id) {
    const reason = prompt('Reason for blacklisting this customer:');
    if (!reason) return;

    const result = await apiRequest(pathFromTemplate(blacklistTemplate, id), 'POST', { reason });
    if (result?.success) {
        showAppToast(result.message, 'success');
        window.location.reload();
    }
}

async function removeBlacklist(id) {
    if (!confirm('Remove this customer from blacklist?')) return;

    const result = await apiRequest(pathFromTemplate(unblacklistTemplate, id), 'POST');
    if (result?.success) {
        showAppToast(result.message, 'success');
        window.location.reload();
    }
}

async function updateCustomerStats(id) {
    const result = await apiRequest(pathFromTemplate(updateStatsTemplate, id), 'POST');
    if (result?.success) {
        showAppToast(result.message, 'success');
        window.location.reload();
    }
}

async function deleteCustomer(id) {
    if (!confirm('Delete this customer? This action cannot be undone.')) return;

    const result = await apiRequest(pathFromTemplate(deleteCustomerTemplate, id), 'DELETE');
    if (result?.success) {
        showAppToast(result.message, 'success');
        window.location.reload();
    }
}

async function apiRequest(url, method, payload = null) {
    try {
        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: payload ? JSON.stringify(payload) : null
        });

        const result = await response.json();
        if (!response.ok || !result.success) {
            showAppToast(result.message || 'Request failed', 'error');
            return null;
        }

        return result;
    } catch (error) {
        showAppToast('Request failed', 'error');
        return null;
    }
}

function showAppToast(message, type = 'info') {
    if (typeof showToast === 'function') {
        showToast(message, type);
    } else {
        alert(message);
    }
}
</script>
@endpush
