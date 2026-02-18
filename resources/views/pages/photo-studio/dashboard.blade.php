@extends('layouts.app')
@section('title', 'Photo Studio Dashboard')
@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title mb-1">Photo Studio Dashboard</h1>
                <p class="text-muted mb-0">
                    Welcome back, {{ $user->first_name }}! 
                    <span class="badge bg-info ms-2">{{ $activeSessions }} Active Sessions</span>
                    <span class="badge bg-secondary ms-1">Offset Time: {{ $offsetTime }}min</span>
                </p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkInModal">
                    <i class="fas fa-user-check me-2"></i>Check In Customer
                </button>
                <a href="{{ route('photo-studio.sessions.active') }}" class="btn btn-outline-primary">
                    <i class="fas fa-clock me-2"></i>Active Sessions
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-camera"></i>
                </div>
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
                <div class="stat-icon bg-success">
                    <i class="fas fa-naira-sign"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Today's Revenue</div>
                    <div class="stat-value">₦{{ number_format($todayStats['revenue'], 0) }}</div>
                    <div class="stat-detail">
                        <span class="text-muted">Pending: ₦{{ number_format($todayStats['pendingPayment'], 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Hours</div>
                    <div class="stat-value">{{ $todayStats['totalHours'] }}</div>
                    <div class="stat-detail">
                        <span class="text-muted">{{ $todayStats['totalMinutes'] }} minutes</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">This Month</div>
                    <div class="stat-value">{{ $monthStats['totalSessions'] }}</div>
                    <div class="stat-detail">
                        <span class="text-success">₦{{ number_format($monthStats['revenue'], 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Studio Categories -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Studio Categories</h5>
                        <a href="{{ route('photo-studio.categories.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-cog me-1"></i>Manage Categories
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($categories as $category)
                        <div class="col-md-4 mb-3">
                            <div class="studio-status-card" onclick="window.location='{{ route('photo-studio.categories.show', $category->id) }}'">
                                <div class="studio-header">
                                    <h5 style="color: {{ $category->color }}">
                                        <i class="fas fa-camera me-2"></i>{{ $category->name }}
                                    </h5>
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
                                        <small class="text-muted">/ {{ $category->base_time }}min</small>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Capacity</small>
                                        <span class="badge bg-info">Max {{ $category->max_occupants }} people</span>
                                    </div>
                                    @if($category->rooms_count > 0)
                                    <div>
                                        <small class="text-muted">
                                            <i class="fas fa-door-open me-1"></i>{{ $category->rooms_count }} Physical Rooms
                                        </small>
                                    </div>
                                    @endif
                                </div>
                                <div class="studio-actions">
                                    <button class="btn btn-sm btn-primary flex-fill" 
                                            onclick="event.stopPropagation(); openCheckInModal({{ $category->id }})">
                                        <i class="fas fa-user-check me-1"></i>Check In
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" 
                                            onclick="event.stopPropagation(); viewCategoryDetails({{ $category->id }})">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-camera fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No Categories Configured</h5>
                                <p class="text-muted">Set up your studio categories to start managing sessions</p>
                                <a href="{{ route('photo-studio.categories.create') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-plus me-2"></i>Create First Category
                                </a>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Sessions & Quick Stats -->
    <div class="row">
        <!-- Recent Sessions -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Sessions</h5>
                        <a href="{{ route('photo-studio.sessions.index') }}" class="btn btn-sm btn-outline-primary">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($recentSessions as $session)
                    <div class="session-item">
                        <div class="row align-items-center">
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
                                <div class="studio-badge">
                                    <i class="fas fa-camera me-1"></i>{{ $session->category->name }}
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-users me-1"></i>{{ $session->number_of_people }} people
                                </small>
                            </div>
                            <div class="col-md-2 text-center">
                                <div class="check-in-time">{{ $session->check_out_time->format('h:i A') }}</div>
                                <small class="text-muted">{{ $session->actual_duration }}min</small>
                            </div>
                            <div class="col-md-2 text-center">
                                <div class="fw-bold text-success">{{ $session->formatted_total_amount }}</div>
                                <span class="badge bg-{{ $session->payment_status === 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($session->payment_status) }}
                                </span>
                            </div>
                            <div class="col-md-1 text-end">
                                <a href="{{ route('photo-studio.sessions.show', $session->id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-history fa-3x text-muted mb-2"></i>
                        <p class="text-muted">No recent sessions</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Customers & Charts -->
        <div class="col-lg-4 mb-4">
            <!-- Top Customers -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Top Customers</h5>
                </div>
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
                    <p class="text-muted text-center">No customers yet</p>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('photo-studio.customers.index') }}" class="quick-action">
                        <i class="fas fa-users"></i>
                        <span>Manage Customers</span>
                    </a>
                    <a href="{{ route('photo-studio.reports.index') }}" class="quick-action">
                        <i class="fas fa-chart-bar"></i>
                        <span>View Reports</span>
                    </a>
                    <a href="{{ route('photo-studio.settings.index') }}" class="quick-action">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    @if($categories->where('has_rooms', true)->count() > 0)
                    <a href="{{ route('photo-studio.rooms.index') }}" class="quick-action">
                        <i class="fas fa-door-open"></i>
                        <span>Manage Rooms</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Check In Modal -->
<div class="modal fade" id="checkInModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-check me-2"></i>Check In Customer
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="checkInForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Offset Time:</strong> {{ $offsetTime }} minutes preparation time will be added before the session timer starts.
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="customer_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="customer_phone" required>
                            <small class="text-muted">Will auto-fill if customer exists</small>
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
                                        data-max-occupants="{{ $category->max_occupants }}"
                                        data-available-slots="{{ $category->availableSlots() }}">
                                    {{ $category->name }} - {{ $category->formatted_base_price }}/{{ $category->base_time }}min
                                    ({{ $category->availableSlots() }} slots)
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Number of People <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="number_of_people" 
                                   id="numberOfPeople" min="1" value="1" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Booked Duration (min) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="booked_duration" 
                                   id="bookedDuration" min="10" value="30" required>
                        </div>
                    </div>

                    <div id="pricePreview" class="alert alert-light d-none">
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted d-block">Base Price</small>
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
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-2"></i>Check In Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('assets/js/photo-studio.js') }}"></script>
<script>
// Check-in form handling
document.getElementById('checkInForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('{{ route('photo-studio.check-in') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('success', result.message);
            $('#checkInModal').modal('hide');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', result.message);
        }
    } catch (error) {
        showAlert('danger', 'An error occurred during check-in');
        console.error(error);
    }
});

// Price calculator
document.querySelectorAll('#categorySelect, #bookedDuration').forEach(el => {
    el.addEventListener('change', calculatePrice);
});

function calculatePrice() {
    const categorySelect = document.getElementById('categorySelect');
    const duration = document.getElementById('bookedDuration').value;
    const offsetTime = {{ $offsetTime }};
    
    if (!categorySelect.value || !duration) return;
    
    const option = categorySelect.selectedOptions[0];
    const basePrice = parseFloat(option.dataset.basePrice);
    const baseTime = parseInt(option.dataset.baseTime);
    
    // Calculate price
    let price = basePrice;
    if (duration > baseTime) {
        const extraTime = duration - baseTime;
        const perMinute = basePrice / baseTime;
        price = basePrice + (extraTime * perMinute);
    }
    
    // Calculate times
    const now = new Date();
    const timerStart = new Date(now.getTime() + offsetTime * 60000);
    const expectedEnd = new Date(timerStart.getTime() + duration * 60000);
    
    // Update preview
    document.getElementById('basePrice').textContent = '₦' + price.toLocaleString();
    document.getElementById('timerStart').textContent = timerStart.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'});
    document.getElementById('expectedEnd').textContent = expectedEnd.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'});
    document.getElementById('pricePreview').classList.remove('d-none');
}

function openCheckInModal(categoryId) {
    document.getElementById('categorySelect').value = categoryId;
    calculatePrice();
    $('#checkInModal').modal('show');
}

function viewCategoryDetails(categoryId) {
    window.location = `/app/photo-studio/categories/${categoryId}`;
}

function showAlert(type, message) {
    // Implement your alert/toast notification
    alert(message);
}
</script>
@endpush
@endsection