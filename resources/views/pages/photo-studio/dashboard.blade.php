@extends('layouts.app')
@section('title', 'Photo Studio Dashboard')

@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-area">
    <div class="page-header mb-4">
        <div class="row align-items-center gy-3">
            <div class="col-md">
                <h1 class="page-title mb-1">Photo Studio Dashboard</h1>
                <p class="text-muted mb-0">
                    Welcome back, {{ $user->first_name }}.
                    <span class="badge bg-info ms-2">{{ $activeSessions }} Active Sessions</span>
                    <span class="badge bg-secondary ms-1">Offset Time: {{ $offsetTime }} min</span>
                </p>
            </div>
            <div class="col-md-auto d-flex gap-2 flex-wrap">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkInModal">
                    <i class="fas fa-user-check me-2"></i>Check In Customer
                </button>
                <a href="{{ route('photo-studio.sessions.active') }}" class="btn btn-outline-primary">
                    <i class="fas fa-clock me-2"></i>Active Sessions
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary"><i class="fas fa-camera"></i></div>
                <div class="stat-content">
                    <div class="stat-label">Today's Sessions</div>
                    <div class="stat-value">{{ $todayStats['totalSessions'] }}</div>
                    <div class="stat-detail">
                        <span class="text-success">{{ $todayStats['completedSessions'] }} completed</span> •
                        <span class="text-warning">{{ $todayStats['activeSessions'] }} active</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-success"><i class="fas fa-naira-sign"></i></div>
                <div class="stat-content">
                    <div class="stat-label">Today's Revenue</div>
                    <div class="stat-value">₦{{ number_format($todayStats['revenue'], 0) }}</div>
                    <div class="stat-detail">Pending: ₦{{ number_format($todayStats['pendingPayment'], 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-info"><i class="fas fa-hourglass-half"></i></div>
                <div class="stat-content">
                    <div class="stat-label">Total Time</div>
                    <div class="stat-value">{{ $todayStats['totalHours'] }}</div>
                    <div class="stat-detail">{{ $todayStats['totalMinutes'] }} minutes</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning"><i class="fas fa-users"></i></div>
                <div class="stat-content">
                    <div class="stat-label">This Month</div>
                    <div class="stat-value">{{ $monthStats['totalSessions'] }}</div>
                    <div class="stat-detail">Revenue: ₦{{ number_format($monthStats['revenue'], 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Studio Categories</h5>
            <a href="{{ route('photo-studio.categories.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-cog me-1"></i>Manage Categories
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse($categories as $category)
                <div class="col-md-4 mb-3">
                    <div class="studio-status-card" onclick="window.location='{{ route('photo-studio.categories.show', $category->id) }}'">
                        <div class="studio-header">
                            <h5 style="color: {{ $category->color }}"><i class="fas fa-camera me-2"></i>{{ $category->name }}</h5>
                            <span class="status-badge {{ $category->is_active ? 'available' : 'maintenance' }}">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="studio-info">
                            <div class="mb-2">
                                <small class="text-muted d-block">Active Sessions</small>
                                <strong class="text-primary">{{ $category->active_sessions_count }} / {{ $category->max_concurrent_sessions }}</strong>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted d-block">Pricing</small>
                                <strong class="text-success">{{ $category->formatted_base_price }}</strong>
                                <small class="text-muted">/ {{ $category->base_time }} min</small>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted d-block">Capacity</small>
                                <span class="badge bg-info">Max {{ $category->max_occupants }} people</span>
                            </div>
                            @if($category->rooms_count > 0)
                            <small class="text-muted"><i class="fas fa-door-open me-1"></i>{{ $category->rooms_count }} room(s)</small>
                            @endif
                        </div>
                        <div class="studio-actions">
                            <button class="btn btn-sm btn-primary flex-fill" onclick="event.stopPropagation(); openCheckInModal({{ $category->id }})">
                                <i class="fas fa-user-check me-1"></i>Check In
                            </button>
                            <a href="{{ route('photo-studio.categories.show', $category->id) }}" class="btn btn-sm btn-outline-secondary" onclick="event.stopPropagation();">
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-camera fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No Categories Configured</h5>
                    <p class="text-muted">Create your first category to start taking sessions.</p>
                    <a href="{{ route('photo-studio.categories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Category
                    </a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Sessions</h5>
                    <a href="{{ route('photo-studio.sessions.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @forelse($recentSessions as $session)
                    <div class="session-item">
                        <div class="row align-items-center gy-2">
                            <div class="col-md-4">
                                <div class="customer-info">
                                    <div class="customer-avatar">{{ $session->customer->initials }}</div>
                                    <div>
                                        <div class="fw-semibold">{{ $session->customer->name }}</div>
                                        <small class="text-muted">{{ $session->customer->phone }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="studio-badge"><i class="fas fa-camera me-1"></i>{{ $session->category->name }}</div>
                                <small class="text-muted d-block mt-1"><i class="fas fa-users me-1"></i>{{ $session->number_of_people }} people</small>
                            </div>
                            <div class="col-md-2 text-md-center">
                                <div class="check-in-time">{{ optional($session->check_out_time)->format('h:i A') ?? '--:--' }}</div>
                                <small class="text-muted">{{ $session->actual_duration ?? 0 }} min</small>
                            </div>
                            <div class="col-md-2 text-md-center">
                                <div class="fw-bold text-success">{{ $session->formatted_total_amount }}</div>
                                <span class="badge bg-{{ $session->payment_status === 'paid' ? 'success' : ($session->payment_status === 'partial' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($session->payment_status) }}
                                </span>
                            </div>
                            <div class="col-md-1 text-md-end">
                                <a href="{{ route('photo-studio.sessions.show', $session->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-history fa-3x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No completed sessions yet.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Top Customers</h5></div>
                <div class="card-body">
                    @forelse($topCustomers as $customer)
                    <div class="top-customer-item">
                        <div class="d-flex align-items-center">
                            <div class="customer-avatar-sm me-2">{{ $customer->initials }}</div>
                            <div class="flex-fill">
                                <div class="fw-semibold">{{ $customer->name }}</div>
                                <small class="text-muted">{{ $customer->total_sessions }} sessions</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success">{{ $customer->formatted_total_spent }}</div>
                                <span class="badge bg-primary">{{ $customer->tier }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center mb-0">No customer records yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">Quick Actions</h5></div>
                <div class="card-body">
                    <a href="{{ route('photo-studio.customers.index') }}" class="quick-action"><i class="fas fa-users"></i><span>Manage Customers</span></a>
                    <a href="{{ route('photo-studio.rooms.index') }}" class="quick-action"><i class="fas fa-door-open"></i><span>Manage Rooms</span></a>
                    <a href="{{ route('photo-studio.sessions.index') }}" class="quick-action"><i class="fas fa-history"></i><span>Session History</span></a>
                    <a href="{{ route('photo-studio.reports.index') }}" class="quick-action"><i class="fas fa-chart-bar"></i><span>Reports</span></a>
                    <a href="{{ route('photo-studio.settings.index') }}" class="quick-action"><i class="fas fa-cog"></i><span>Settings</span></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="checkInModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-check me-2"></i>Check In Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="checkInForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Session timer starts {{ $offsetTime }} minutes after check-in.
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="customer_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="customer_phone" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" name="customer_email">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category_id" id="categorySelect" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    data-base-price="{{ $category->base_price }}"
                                    data-base-time="{{ $category->base_time }}"
                                    data-per-minute="{{ $category->per_minute_rate }}"
                                    data-max-occupants="{{ $category->max_occupants }}"
                                    data-available-slots="{{ $category->availableSlots() }}">
                                    {{ $category->name }} - {{ $category->formatted_base_price }}/{{ $category->base_time }}min ({{ $category->availableSlots() }} slots)
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">People <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="number_of_people" id="numberOfPeople" min="1" value="1" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Duration (min) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="booked_duration" id="bookedDuration" min="10" value="30" required>
                        </div>
                    </div>

                    <div id="pricePreview" class="alert alert-light d-none">
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted d-block">Estimated Cost</small>
                                <strong id="basePrice">₦0</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Timer Starts</small>
                                <strong id="timerStart">--:--</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Expected End</small>
                                <strong id="expectedEnd">--:--</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="checkInSubmitBtn">
                        <i class="fas fa-check me-2"></i>Check In Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const checkInRoute = @json(route('photo-studio.check-in'));

const checkInModalEl = document.getElementById('checkInModal');
const checkInModal = checkInModalEl ? new bootstrap.Modal(checkInModalEl) : null;

const checkInForm = document.getElementById('checkInForm');
if (checkInForm) {
    checkInForm.addEventListener('submit', handleCheckInSubmit);
}

document.querySelectorAll('#categorySelect, #bookedDuration, #numberOfPeople').forEach((el) => {
    el.addEventListener('change', calculatePricePreview);
    el.addEventListener('input', calculatePricePreview);
});

function openCheckInModal(categoryId) {
    const categorySelect = document.getElementById('categorySelect');
    if (categorySelect && categoryId) {
        categorySelect.value = String(categoryId);
    }
    calculatePricePreview();
    checkInModal?.show();
}

async function handleCheckInSubmit(e) {
    e.preventDefault();

    const form = e.currentTarget;
    const formData = new FormData(form);
    const payload = Object.fromEntries(formData.entries());

    const submitBtn = document.getElementById('checkInSubmitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Checking In...';

    try {
        const response = await fetch(checkInRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            showAppToast(result.message || 'Check-in failed', 'error');
            return;
        }

        showAppToast(result.message, 'success');
        checkInModal?.hide();
        form.reset();
        document.getElementById('pricePreview').classList.add('d-none');
        setTimeout(() => window.location.reload(), 600);
    } catch (error) {
        showAppToast('An unexpected error occurred during check-in', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Check In Customer';
    }
}

function calculatePricePreview() {
    const categorySelect = document.getElementById('categorySelect');
    const numberOfPeople = parseInt(document.getElementById('numberOfPeople').value || '1', 10);
    const duration = parseInt(document.getElementById('bookedDuration').value || '0', 10);

    if (!categorySelect || !categorySelect.value || duration <= 0) {
        document.getElementById('pricePreview').classList.add('d-none');
        return;
    }

    const selected = categorySelect.selectedOptions[0];
    const basePrice = parseFloat(selected.dataset.basePrice || '0');
    const baseTime = parseInt(selected.dataset.baseTime || '30', 10);
    const perMinute = parseFloat(selected.dataset.perMinute || (basePrice / Math.max(baseTime, 1)));
    const maxOccupants = parseInt(selected.dataset.maxOccupants || '1', 10);

    let price = basePrice;
    if (duration > baseTime) {
        price += (duration - baseTime) * perMinute;
    }

    if (numberOfPeople > maxOccupants) {
        showAppToast(`Selected category supports up to ${maxOccupants} people.`, 'warning');
    }

    const now = new Date();
    const timerStart = new Date(now.getTime() + ({{ $offsetTime }} * 60000));
    const expectedEnd = new Date(timerStart.getTime() + (duration * 60000));

    document.getElementById('basePrice').textContent = '₦' + price.toLocaleString(undefined, { maximumFractionDigits: 2 });
    document.getElementById('timerStart').textContent = timerStart.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    document.getElementById('expectedEnd').textContent = expectedEnd.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    document.getElementById('pricePreview').classList.remove('d-none');
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
@endsection
