@extends('layouts.app')

@section('title', 'Prop Rental')

@push('styles')
<link href="{{ asset('assets/css/prop-rental.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Prop Rental</h1>
                <p class="page-subtitle">Manage musical prop bookings and availability</p>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newRentalModal">
                        <i class="fas fa-plus me-2"></i>New Rental
                    </button>
                    <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#newPropModal">
                        <i class="fas fa-guitar me-2"></i>Add Prop
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Rental Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-guitar"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $stats['total_props'] }}</div>
                    <div class="stat-label">Total Props</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $stats['currently_rented'] }}</div>
                    <div class="stat-label">Currently Rented</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $stats['due_today'] }}</div>
                    <div class="stat-label">Due Today</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">₦{{ number_format($stats['monthly_revenue'], 0) }}</div>
                    <div class="stat-label">Monthly Revenue</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Tabs -->
    <ul class="nav nav-tabs mb-4" id="rentalTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'props' ? 'active' : '' }}" id="props-tab" data-bs-toggle="tab" data-bs-target="#props" type="button">
                <i class="fas fa-guitar me-2"></i>Props
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'active-rentals' ? 'active' : '' }}" id="active-rentals-tab" data-bs-toggle="tab" data-bs-target="#active-rentals" type="button">
                <i class="fas fa-list me-2"></i>Active Rentals
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'calendar' ? 'active' : '' }}" id="calendar-tab" data-bs-toggle="tab" data-bs-target="#calendar" type="button">
                <i class="fas fa-calendar me-2"></i>Calendar
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'customers' ? 'active' : '' }}" id="customers-tab" data-bs-toggle="tab" data-bs-target="#customers" type="button">
                <i class="fas fa-users me-2"></i>Customers
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="rentalTabContent">
        <!-- Props Tab -->
        <div class="tab-pane fade {{ $activeTab === 'props' ? 'show active' : '' }}" id="props">
            <div class="row">
                <div class="col-lg-9">
                    <!-- Category Filter -->
                    <div class="category-filter mb-4">
                        <a href="{{ route('prop-rental.index', ['category' => 'all']) }}" class="btn btn-outline-primary {{ $category === 'all' ? 'active' : '' }}">All</a>
                        <a href="{{ route('prop-rental.index', ['category' => 'guitars']) }}" class="btn btn-outline-primary {{ $category === 'guitars' ? 'active' : '' }}">Guitars</a>
                        <a href="{{ route('prop-rental.index', ['category' => 'keyboards']) }}" class="btn btn-outline-primary {{ $category === 'keyboards' ? 'active' : '' }}">Keyboards</a>
                        <a href="{{ route('prop-rental.index', ['category' => 'drums']) }}" class="btn btn-outline-primary {{ $category === 'drums' ? 'active' : '' }}">Drums</a>
                        <a href="{{ route('prop-rental.index', ['category' => 'brass']) }}" class="btn btn-outline-primary {{ $category === 'brass' ? 'active' : '' }}">Brass</a>
                        <a href="{{ route('prop-rental.index', ['category' => 'strings']) }}" class="btn btn-outline-primary {{ $category === 'strings' ? 'active' : '' }}">Strings</a>
                    </div>

                    <!-- Props Grid -->
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        @forelse($props as $prop)
                            <div class="col">
                                <div class="prop-card">
                                    <div class="prop-image">
                                        <i class="{{ $prop->image ?? 'fas fa-music' }} fa-3x"></i>
                                    </div>
                                    <div class="prop-info">
                                        <h5 class="prop-name">{{ $prop->name }}</h5>
                                        <div class="prop-details">
                                            <div><strong>Brand:</strong> {{ $prop->brand }}</div>
                                            <div><strong>Model:</strong> {{ $prop->model }}</div>
                                            <div><strong>Condition:</strong> {{ ucfirst($prop->condition) }}</div>
                                        </div>
                                        <div class="rental-rate">{{ $prop->formatted_daily_rate }}/day</div>
                                        <span class="availability-status {{ $prop->status }}">{{ ucfirst($prop->status) }}</span>
                                        <div class="mt-3 d-flex gap-2">
                                            @if($prop->status === 'available')
                                                <button class="btn btn-primary btn-sm flex-grow-1" data-bs-toggle="modal" data-bs-target="#newRentalModal" onclick="setPropInModal({{ $prop->id }})">
                                                    <i class="fas fa-calendar-plus me-1"></i>Rent
                                                </button>
                                            @else
                                                <button class="btn btn-outline-secondary btn-sm flex-grow-1" disabled>
                                                    {{ $prop->status === 'rented' ? 'Rented' : 'Maintenance' }}
                                                </button>
                                            @endif
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="{{ route('prop-rental.props.edit', $prop->id) }}">
                                                        <i class="fas fa-edit me-2"></i>Edit
                                                    </a></li>
                                                    @if($prop->status !== 'rented')
                                                        @if($prop->status === 'maintenance')
                                                            <li>
                                                                <form action="{{ route('prop-rental.props.complete-maintenance', $prop->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="fas fa-check me-2"></i>Complete Maintenance
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @else
                                                            <li><a class="dropdown-item" href="{{ route('prop-rental.props.maintenance', $prop->id) }}">
                                                                <i class="fas fa-tools me-2"></i>Mark Maintenance
                                                            </a></li>
                                                        @endif
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item text-danger" href="{{ route('prop-rental.props.delete', $prop->id) }}">
                                                            <i class="fas fa-trash me-2"></i>Delete
                                                        </a></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <i class="fas fa-guitar fa-3x text-muted mb-3"></i>
                                <h5>No props found</h5>
                                <p class="text-muted">Try adjusting your category filter</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="col-lg-3">
                    <!-- Quick Actions -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Quick Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newRentalModal">
                                    <i class="fas fa-plus me-2"></i>New Rental
                                </button>
                                <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#newPropModal">
                                    <i class="fas fa-guitar me-2"></i>Add Prop
                                </button>
                                <a href="{{ route('prop-rental.rentals.export') }}" class="btn btn-outline-info">
                                    <i class="fas fa-download me-2"></i>Export Data
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Due Today -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Due Today</h6>
                        </div>
                        <div class="card-body">
                            @forelse($dueToday as $rental)
                                <div class="due-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold">{{ $rental->customer->name }}</div>
                                            <small class="text-muted">{{ $rental->prop->name }}</small>
                                        </div>
                                        <a href="tel:{{ $rental->customer->phone }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-phone"></i>
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-3">
                                    <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                                    <div>No rentals due today!</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Rentals Tab -->
        <div class="tab-pane fade {{ $activeTab === 'active-rentals' ? 'show active' : '' }}" id="active-rentals">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Active Rentals</h5>
                    <a href="{{ route('prop-rental.rentals.export') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-download me-2"></i>Export
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover rental-table">
                            <thead>
                                <tr>
                                    <th>Rental ID</th>
                                    <th>Customer</th>
                                    <th>Prop</th>
                                    <th>Start Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Total Amount</th>
                                    <th>Payment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activeRentals as $rental)
                                    <tr class="{{ $rental->isOverdue() ? 'table-danger' : '' }}">
                                        <td>
                                            <strong>{{ strtoupper($rental->rental_id) }}</strong>
                                            @if($rental->isOverdue())
                                                <span class="badge bg-danger badge-sm ms-1">OVERDUE</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $rental->customer->name }}</div>
                                            <small class="text-muted">{{ $rental->customer->phone }}</small>
                                        </td>
                                        <td>
                                            <div>{{ $rental->prop->name }}</div>
                                            <small class="text-muted">{{ $rental->prop->brand }}</small>
                                        </td>
                                        <td>
                                            <div>{{ $rental->start_date->format('d M Y') }}</div>
                                            <small class="text-muted">{{ $rental->start_date->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            <div class="{{ $rental->days_remaining < 2 ? 'text-danger fw-bold' : '' }}">
                                                {{ $rental->end_date->format('d M Y') }}
                                            </div>
                                            <small class="text-muted">
                                                @if($rental->days_remaining > 0)
                                                    <i class="fas fa-clock me-1"></i>{{ $rental->days_remaining }} day(s) left
                                                @else
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Due today
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $rental->status_badge_class }}">
                                                {{ $rental->status_display }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $rental->formatted_total_amount }}</div>
                                            <small class="text-muted">
                                                {{ $rental->duration }} day(s) @ {{ $rental->prop->formatted_daily_rate }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                <div>
                                                    <span class="badge {{ $rental->payment_status_badge_class }}">
                                                        {{ $rental->payment_status }}
                                                    </span>
                                                </div>
                                                <div class="m-0">
                                                    <small class="text-muted">Paid:</small> 
                                                    <small class="text-success">{{ $rental->formatted_amount_paid }}</small>
                                                </div>
                                                {{-- @if($rental->balance_due > 0)
                                                    <div>
                                                        <small class="text-muted">Balance:</small> 
                                                        <small class="text-warning">{{ $rental->formatted_balance_due }}</small>
                                                    </div>
                                                @endif --}}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('prop-rental.rentals.show', $rental->id) }}" 
                                                class="btn btn-outline-primary" 
                                                title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('prop-rental.rentals.extend-form', $rental->id) }}" 
                                                class="btn btn-outline-success" 
                                                title="Extend Rental">
                                                    <i class="fas fa-calendar-plus"></i>
                                                </a>
                                                <a href="{{ route('prop-rental.rentals.return-form', $rental->id) }}" 
                                                class="btn btn-outline-warning" 
                                                title="Return Prop">
                                                    <i class="fas fa-undo"></i>
                                                </a>
                                                <a href="{{ route('prop-rental.rentals.cancel-form', $rental->id) }}" 
                                                class="btn btn-outline-danger" 
                                                title="Cancel Rental">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="fas fa-calendar fa-3x text-muted mb-3 d-block"></i>
                                            <div>No active rentals</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Tab -->
        <div class="tab-pane fade {{ $activeTab === 'calendar' ? 'show active' : '' }}" id="calendar">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Rental Calendar</h5>
                </div>
                <div class="card-body">
                    <div class="calendar-container">
                        <div class="calendar-header mb-3 d-flex justify-content-between align-items-center">
                            <button class="btn btn-outline-secondary" onclick="changeMonth(-1)">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <h4 class="mb-0" id="currentMonth"></h4>
                            <button class="btn btn-outline-secondary" onclick="changeMonth(1)">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <div class="calendar-grid" id="calendarGrid">
                            <!-- Calendar generated via JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customers Tab -->
        <div class="tab-pane fade {{ $activeTab === 'customers' ? 'show active' : '' }}" id="customers">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Customer Database</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newCustomerModal">
                        <i class="fas fa-user-plus me-2"></i>Add Customer
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover rental-table">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Contact</th>
                                    <th>Total Rentals</th>
                                    <th>Current Rentals</th>
                                    <th>Total Spent</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $customer->name }}</div>
                                            <small class="text-muted">{{ $customer->email }}</small>
                                        </td>
                                        <td>{{ $customer->phone }}</td>
                                        <td>{{ $customer->total_rentals }}</td>
                                        <td>
                                            <span class="badge bg-{{ $customer->current_rentals > 0 ? 'success' : 'secondary' }}">
                                                {{ $customer->current_rentals }}
                                            </span>
                                        </td>
                                        <td>{{ $customer->formatted_total_spent }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('prop-rental.customers.show', $customer->id) }}" class="btn btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#newRentalModal" onclick="setCustomerInModal({{ $customer->id }})" title="New Rental">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                <a href="{{ route('prop-rental.customers.edit', $customer->id) }}" class="btn btn-outline-info" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                                            <div>No customers found</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('pages.prop-rental.modals.new-rental')
@include('pages.prop-rental.modals.new-customer')
@include('pages.prop-rental.modals.new-prop')

@endsection

@push('scripts')
<script>
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let selectedPropId = null;
let selectedCustomerId = null;

function setPropInModal(propId) {
    selectedPropId = propId;
    setTimeout(() => {
        document.getElementById('rentalProp').value = propId;
        calculateRentalAmount();
    }, 100);
}

function setCustomerInModal(customerId) {
    selectedCustomerId = customerId;
    setTimeout(() => {
        document.getElementById('rentalCustomer').value = customerId;
    }, 100);
}

function calculateRentalAmount() {
    const startDate = document.getElementById('rentalStartDate')?.value;
    const endDate = document.getElementById('rentalEndDate')?.value;
    const propSelect = document.getElementById('rentalProp');
    const dailyRateInput = document.getElementById('dailyRate');
    const totalAmountInput = document.getElementById('totalAmount');
    
    if (!startDate || !endDate || !propSelect?.value) {
        if (dailyRateInput) dailyRateInput.value = '';
        if (totalAmountInput) totalAmountInput.value = '';
        return;
    }
    
    const start = new Date(startDate);
    const end = new Date(endDate);
    const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
    
    if (days <= 0) {
        if (dailyRateInput) dailyRateInput.value = '';
        if (totalAmountInput) totalAmountInput.value = '';
        return;
    }
    
    const selectedOption = propSelect.options[propSelect.selectedIndex];
    const dailyRate = parseFloat(selectedOption.getAttribute('data-rate')) || 0;
    const totalAmount = days * dailyRate;
    
    if (dailyRateInput) {
        dailyRateInput.value = dailyRate.toFixed(2);
    }
    
    if (totalAmountInput) {
        totalAmountInput.value = totalAmount.toFixed(2);
    }
}

async function changeMonth(delta) {
    currentMonth += delta;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    } else if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    await loadCalendar();
}

async function loadCalendar() {
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'];
    
    document.getElementById('currentMonth').textContent = `${monthNames[currentMonth]} ${currentYear}`;
    
    try {
        const response = await fetch(`{{ route('prop-rental.calendar-data') }}?year=${currentYear}&month=${currentMonth + 1}`);
        const data = await response.json();
        
        if (data.success) {
            renderCalendar(data.rentals);
        }
    } catch (error) {
        console.error('Failed to load calendar:', error);
    }
}

function renderCalendar(rentals) {
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    
    let calendarHTML = '';
    
    const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    dayHeaders.forEach(day => {
        calendarHTML += `<div class="calendar-day-header text-center fw-bold py-2">${day}</div>`;
    });
    
    for (let i = 0; i < firstDay; i++) {
        calendarHTML += '<div class="calendar-day other-month"></div>';
    }
    
    for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(currentYear, currentMonth, day);
        const isToday = date.toDateString() === new Date().toDateString();
        
        const rentalsOnDay = rentals.filter(r => {
            const startDate = new Date(r.start_date);
            const endDate = new Date(r.end_date);
            return date >= new Date(startDate.toDateString()) && date <= new Date(endDate.toDateString());
        });
        
        calendarHTML += `
            <div class="calendar-day ${isToday ? 'today' : ''}">
                <div class="day-number">${day}</div>
                <div class="day-events">
                    ${rentalsOnDay.slice(0, 3).map(rental => `
                        <div class="day-event" title="${rental.customer.name} - ${rental.prop.name}">
                            ${rental.customer.name.split(' ')[0]}
                        </div>
                    `).join('')}
                    ${rentalsOnDay.length > 3 ? `<div class="day-event">+${rentalsOnDay.length - 3} more</div>` : ''}
                </div>
            </div>
        `;
    }
    
    document.getElementById('calendarGrid').innerHTML = calendarHTML;
}

document.addEventListener('DOMContentLoaded', function() {
    loadCalendar();
    
    const startDateInput = document.getElementById('rentalStartDate');
    const endDateInput = document.getElementById('rentalEndDate');
    const propSelect = document.getElementById('rentalProp');
    
    if (startDateInput) startDateInput.addEventListener('change', calculateRentalAmount);
    if (endDateInput) endDateInput.addEventListener('change', calculateRentalAmount);
    if (propSelect) propSelect.addEventListener('change', calculateRentalAmount);
});
</script>
@endpush