@extends('layouts.app')
@section('title', $customer->name . ' - Customer Details')

@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('photo-studio.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('photo-studio.customers.index') }}">Customers</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $customer->name }}</li>
        </ol>
    </nav>

    <div class="page-header mb-4">
        <div class="row align-items-center gy-3">
            <div class="col-md">
                <div class="d-flex align-items-center gap-3">
                    <div class="customer-avatar" style="width:56px;height:56px;font-size:1.2rem;">{{ $customer->initials }}</div>
                    <div>
                        <h1 class="page-title mb-1">{{ $customer->name }}</h1>
                        <p class="text-muted mb-0">
                            <span class="badge bg-{{ $customer->is_blacklisted ? 'danger' : ($customer->is_active ? 'success' : 'secondary') }}">{{ $customer->status_label }}</span>
                            <span class="badge bg-primary ms-1">{{ $customer->tier }}</span>
                            @if($customer->age)
                            <span class="ms-2">Age: {{ $customer->age }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-auto d-flex gap-2 flex-wrap">
                <a href="{{ route('photo-studio.customers.edit', $customer->id) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
                <a href="{{ route('photo-studio.customers.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-primary"><i class="fas fa-repeat"></i></div>
                <div>
                    <small class="text-muted">Completed Sessions</small>
                    <div class="fw-bold">{{ number_format($completedSessions) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-success"><i class="fas fa-naira-sign"></i></div>
                <div>
                    <small class="text-muted">Total Revenue</small>
                    <div class="fw-bold text-success">₦{{ number_format($totalRevenue, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-warning"><i class="fas fa-clock"></i></div>
                <div>
                    <small class="text-muted">Avg Session</small>
                    <div class="fw-bold">{{ number_format($averageSessionDuration) }} min</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-info"><i class="fas fa-calendar-check"></i></div>
                <div>
                    <small class="text-muted">Last Visit</small>
                    <div class="fw-bold">{{ $daysSinceLastVisit !== null ? $daysSinceLastVisit . ' day(s)' : 'Never' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-5 mb-3">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Customer Profile</h6>
                    <button class="btn btn-sm btn-outline-info" onclick="refreshStats()"><i class="fas fa-sync me-1"></i>Refresh Stats</button>
                </div>
                <div class="card-body">
                    <div class="mb-2"><strong>Phone:</strong> {{ $customer->phone }}</div>
                    <div class="mb-2"><strong>Email:</strong> {{ $customer->email ?: 'N/A' }}</div>
                    <div class="mb-2"><strong>Address:</strong> {{ $customer->address ?: 'N/A' }}</div>
                    <div class="mb-2"><strong>Date of Birth:</strong> {{ $customer->date_of_birth ? $customer->date_of_birth->format('d M Y') : 'N/A' }}</div>
                    <div class="mb-2"><strong>Average Spending:</strong> ₦{{ number_format($averageSpending, 2) }}</div>
                    <div class="mb-2">
                        <strong>Favorite Category:</strong>
                        {{ $favoriteCategory ? $favoriteCategory->name : 'N/A' }}
                    </div>
                    <div class="mb-2"><strong>Total Session Minutes:</strong> {{ number_format($totalMinutes) }}</div>
                    <div class="mb-0"><strong>Registered:</strong> {{ $customer->created_at->format('d M Y') }}</div>

                    @if($customer->notes)
                    <hr>
                    <div><strong>Notes:</strong></div>
                    <p class="mb-0 text-muted">{{ $customer->notes }}</p>
                    @endif

                    @if($customer->is_blacklisted)
                    <hr>
                    <div class="alert alert-danger mb-0">
                        <strong>Blacklist Reason:</strong><br>
                        {{ $customer->blacklist_reason }}
                    </div>
                    @endif
                </div>
                <div class="card-footer d-flex gap-2 flex-wrap">
                    @if($customer->is_blacklisted)
                    <button class="btn btn-sm btn-success" onclick="removeBlacklist()"><i class="fas fa-user-check me-1"></i>Remove Blacklist</button>
                    @else
                    <button class="btn btn-sm btn-warning" onclick="blacklistCustomer()"><i class="fas fa-user-slash me-1"></i>Blacklist</button>
                    @endif
                    <a href="{{ route('photo-studio.customers.edit', $customer->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit me-1"></i>Edit Profile</a>
                </div>
            </div>
        </div>

        <div class="col-lg-7 mb-3">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Recent Sessions</h6>
                    <a href="{{ route('photo-studio.sessions.index', ['date_from' => now()->subMonth()->format('Y-m-d')]) }}" class="btn btn-sm btn-outline-secondary">Session History</a>
                </div>
                <div class="card-body p-0">
                    @if($customer->sessions->isEmpty())
                    <div class="text-center py-5 text-muted">No session records for this customer.</div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Session</th>
                                    <th>Category</th>
                                    <th>Check In</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->sessions as $session)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $session->session_code }}</div>
                                        <small class="text-muted">{{ $session->number_of_people }} people</small>
                                    </td>
                                    <td>{{ $session->category?->name ?? 'N/A' }}</td>
                                    <td>{{ $session->check_in_time->format('d M Y, h:i A') }}</td>
                                    <td><span class="badge bg-{{ $session->status === 'completed' ? 'success' : ($session->status === 'cancelled' ? 'secondary' : 'warning') }}">{{ $session->status_label }}</span></td>
                                    <td class="fw-semibold">{{ $session->formatted_total_amount }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('photo-studio.sessions.show', $session->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const customerId = {{ $customer->id }};
const blacklistUrl = @json(route('photo-studio.customers.blacklist', ['id' => $customer->id]));
const removeBlacklistUrl = @json(route('photo-studio.customers.remove-blacklist', ['id' => $customer->id]));
const refreshStatsUrl = @json(route('photo-studio.customers.update-statistics', ['id' => $customer->id]));

async function blacklistCustomer() {
    const reason = prompt('Reason for blacklisting this customer:');
    if (!reason) return;

    const result = await apiRequest(blacklistUrl, 'POST', { reason });
    if (result?.success) {
        showAppToast(result.message, 'success');
        window.location.reload();
    }
}

async function removeBlacklist() {
    if (!confirm('Remove this customer from blacklist?')) return;

    const result = await apiRequest(removeBlacklistUrl, 'POST');
    if (result?.success) {
        showAppToast(result.message, 'success');
        window.location.reload();
    }
}

async function refreshStats() {
    const result = await apiRequest(refreshStatsUrl, 'POST');
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
