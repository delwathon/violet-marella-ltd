@extends('layouts.app')
@section('title', 'Instrument Rental')
@push('styles')
<link href="{{ asset('assets/css/instrument-rental.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Instrument Rental</h1>
                <p class="page-subtitle">Manage musical instrument bookings and availability</p>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newRentalModal">
                        <i class="fas fa-plus me-2"></i>New Rental
                    </button>
                    <button class="btn btn-outline-success">
                        <i class="fas fa-guitar me-2"></i>Add Instrument
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
                    <div class="stat-value">47</div>
                    <div class="stat-label">Total Instruments</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">18</div>
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
                    <div class="stat-value">5</div>
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
                    <div class="stat-value">₦85K</div>
                    <div class="stat-label">Monthly Revenue</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Main Content Tabs -->
    <ul class="nav nav-tabs mb-4" id="rentalTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="instruments-tab" data-bs-toggle="tab" data-bs-target="#instruments" type="button">
                <i class="fas fa-guitar me-2"></i>Instruments
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="active-rentals-tab" data-bs-toggle="tab" data-bs-target="#active-rentals" type="button">
                <i class="fas fa-list me-2"></i>Active Rentals
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="calendar-tab" data-bs-toggle="tab" data-bs-target="#calendar" type="button">
                <i class="fas fa-calendar me-2"></i>Calendar
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="customers-tab" data-bs-toggle="tab" data-bs-target="#customers" type="button">
                <i class="fas fa-users me-2"></i>Customers
            </button>
        </li>
    </ul>
    <!-- Tab Content -->
    <div class="tab-content" id="rentalTabContent">
        <!-- Instruments Tab -->
        <div class="tab-pane fade show active" id="instruments">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Instrument Categories -->
                    <div class="category-filter mb-4">
                        <button class="btn btn-outline-primary active" data-category="all">All</button>
                        <button class="btn btn-outline-primary" data-category="guitars">Guitars</button>
                        <button class="btn btn-outline-primary" data-category="keyboards">Keyboards</button>
                        <button class="btn btn-outline-primary" data-category="drums">Drums</button>
                        <button class="btn btn-outline-primary" data-category="brass">Brass</button>
                        <button class="btn btn-outline-primary" data-category="strings">Strings</button>
                    </div>
                    <!-- Instruments Grid -->
                    <div class="instruments-grid" id="instrumentsGrid">
                        <!-- Instruments will be loaded here -->
                    </div>
                </div>
                <div class="col-lg-4">
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
                                <button class="btn btn-outline-success">
                                    <i class="fas fa-check me-2"></i>Check In Return
                                </button>
                                <button class="btn btn-outline-warning">
                                    <i class="fas fa-tools me-2"></i>Mark for Maintenance
                                </button>
                                <button class="btn btn-outline-info">
                                    <i class="fas fa-chart-bar me-2"></i>Rental Report
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Due Today -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Due Today</h6>
                        </div>
                        <div class="card-body">
                            <div id="dueToday">
                                <!-- Due items will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Active Rentals Tab -->
        <div class="tab-pane fade" id="active-rentals">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Active Rentals</h5>
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control form-control-sm" placeholder="Search rentals...">
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Rental ID</th>
                                    <th>Customer</th>
                                    <th>Instrument</th>
                                    <th>Start Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Active rentals will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Calendar Tab -->
        <div class="tab-pane fade" id="calendar">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Rental Calendar</h5>
                        </div>
                        <div class="card-body">
                            <div class="calendar-container">
                                <div class="calendar-header mb-3">
                                    <button class="btn btn-outline-secondary">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <h4 class="mb-0" id="currentMonth">January 2024</h4>
                                    <button class="btn btn-outline-secondary">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                                <div class="calendar-grid" id="calendarGrid">
                                    <!-- Calendar will be generated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Today's Schedule</h6>
                        </div>
                        <div class="card-body">
                            <div id="todaySchedule">
                                <!-- Today's schedule will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Customers Tab -->
        <div class="tab-pane fade" id="customers">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Customer Database</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newCustomerModal">
                        <i class="fas fa-user-plus me-2"></i>Add Customer
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
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
                                <!-- Customers will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- New Rental Modal -->
<div class="modal fade" id="newRentalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Rental</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="newRentalForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Customer</label>
                                <select class="form-select" required>
                                    <option value="">Select Customer</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Instrument</label>
                                <select class="form-select" required>
                                    <option value="">Select Instrument</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Daily Rate (₦)</label>
                                <input type="number" class="form-control" required readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total Amount (₦)</label>
                                <input type="number" class="form-control" required readonly>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Security Deposit (₦)</label>
                        <input type="number" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox">
                        <label class="form-check-label">
                            Rental agreement signed
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-check me-2"></i>Create Rental
                </button>
            </div>
        </div>
    </div>
</div>
<!-- New Customer Modal -->
<div class="modal fade" id="newCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="newCustomerForm">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ID Number</label>
                        <input type="text" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>Add Customer
                </button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="{{ asset('assets/js/instrument-rental.js') }}"></script>
@endpush
@endsection
