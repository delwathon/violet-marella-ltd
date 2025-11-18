@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')
<div class="content-area">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Customer Details</h1>
                <p class="page-subtitle">{{ $customer->customer_id }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('prop-rental.index', ['tab' => 'customers']) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Customers
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <!-- Customer Profile Card -->
            <div class="card mb-3">
                <div class="card-body text-center">
                    <div class="customer-avatar mx-auto mb-3" style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #8b5cf6); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 2.5rem;">
                        {{ $customer->initials }}
                    </div>
                    <h4 class="fw-bold">{{ $customer->name }}</h4>
                    <p class="text-muted mb-3">{{ $customer->customer_id }}</p>
                    <span class="badge bg-{{ $customer->status == 'active' ? 'success' : 'secondary' }} px-3 py-2">
                        {{ ucfirst($customer->status) }}
                    </span>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-address-card me-2"></i>Contact Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Email</small>
                        <div>
                            <a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a>
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Phone</small>
                        <div>
                            <a href="tel:{{ $customer->phone }}">{{ $customer->phone }}</a>
                        </div>
                    </div>
                    @if($customer->address)
                    <div class="mb-3">
                        <small class="text-muted">Address</small>
                        <div>{{ $customer->address }}</div>
                    </div>
                    @endif
                    @if($customer->id_number)
                    <div>
                        <small class="text-muted">ID Number</small>
                        <div>{{ $customer->id_number }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <small class="text-muted">Total Rentals</small>
                            <div class="fs-4 fw-bold text-primary">{{ $customer->total_rentals }}</div>
                        </div>
                        <i class="fas fa-calendar-check fa-2x text-primary opacity-25"></i>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <small class="text-muted">Current Rentals</small>
                            <div class="fs-4 fw-bold text-warning">{{ $customer->current_rentals }}</div>
                        </div>
                        <i class="fas fa-clock fa-2x text-warning opacity-25"></i>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Total Spent</small>
                            <div class="fs-4 fw-bold text-success">{{ $customer->formatted_total_spent }}</div>
                        </div>
                        <i class="fas fa-money-bill-wave fa-2x text-success opacity-25"></i>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-cog me-2"></i>Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newRentalModal" onclick="setCustomerInModal({{ $customer->id }})">
                            <i class="fas fa-plus me-2"></i>New Rental
                        </button>
                        <a href="{{ route('prop-rental.lounge.customers.edit', $customer->id) }}" class="btn btn-outline-info">
                            <i class="fas fa-edit me-2"></i>Edit Details
                        </a>
                        @if($customer->status == 'active' && $customer->current_rentals == 0)
                        <a href="{{ route('prop-rental.lounge.customers.deactivate-form', $customer->id) }}" class="btn btn-outline-danger">
                            <i class="fas fa-ban me-2"></i>Deactivate
                        </a>
                        @elseif($customer->status == 'inactive')
                        <form action="{{ route('prop-rental.lounge.customers.activate', $customer->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-success w-100">
                                <i class="fas fa-check me-2"></i>Activate
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Rental History -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Rental History
                    </h5>
                    <span class="badge bg-primary">{{ $customer->total_rentals }} Total</span>
                </div>
                <div class="card-body">
                    @forelse($customer->rentals as $rental)
                    <div class="card mb-3 border">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <i class="{{ $rental->prop->image ?? 'fas fa-music' }} fa-2x text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1">{{ $rental->prop->name }}</h6>
                                            <div class="text-muted small">
                                                {{ $rental->prop->brand }} {{ $rental->prop->model }}
                                            </div>
                                            <div class="mt-2">
                                                <span class="badge {{ $rental->status_badge_class }}">
                                                    {{ $rental->status_display }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <div class="mb-2">
                                        <small class="text-muted">Rental ID</small>
                                        <div class="fw-bold">{{ strtoupper($rental->rental_id) }}</div>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">Amount</small>
                                        <div class="fw-bold text-success">{{ $rental->formatted_total_amount }}</div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row small">
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <i class="fas fa-calendar-alt text-muted me-2"></i>
                                        <strong>Start:</strong> {{ $rental->start_date->format('d M Y') }}
                                    </div>
                                    <div>
                                        <i class="fas fa-calendar-check text-muted me-2"></i>
                                        <strong>End:</strong> {{ $rental->end_date->format('d M Y') }}
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <div class="mb-1">
                                        <i class="fas fa-clock text-muted me-2"></i>
                                        <strong>Duration:</strong> {{ $rental->duration }} days
                                    </div>
                                    <div>
                                        <i class="fas fa-money-bill text-muted me-2"></i>
                                        <strong>Daily Rate:</strong> ₦{{ number_format($rental->daily_rate, 2) }}
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('prop-rental.rentals.show', $rental->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                                @if($rental->status == 'active')
                                <a href="{{ route('prop-rental.rentals.return-form', $rental->id) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-undo me-1"></i>Return
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5>No Rental History</h5>
                        <p class="text-muted">This customer hasn't rented any props yet.</p>
                        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#newRentalModal" onclick="setCustomerInModal({{ $customer->id }})">
                            <i class="fas fa-plus me-2"></i>Create First Rental
                        </button>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Customer Timeline -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-stream me-2"></i>Timeline
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <div class="fw-bold">Customer Registered</div>
                                <small class="text-muted">{{ $customer->created_at->format('d M Y, h:i A') }}</small>
                            </div>
                        </div>
                        @foreach($customer->rentals->take(5) as $rental)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-{{ $rental->status == 'completed' ? 'success' : ($rental->status == 'active' ? 'warning' : 'danger') }}"></div>
                            <div class="timeline-content">
                                <div class="fw-bold">
                                    {{ $rental->status == 'completed' ? 'Completed Rental' : ($rental->status == 'active' ? 'Active Rental' : 'Cancelled Rental') }}
                                </div>
                                <div class="small">{{ $rental->prop->name }}</div>
                                <small class="text-muted">{{ $rental->created_at->format('d M Y, h:i A') }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e5e7eb;
}
.timeline-item {
    position: relative;
    margin-bottom: 20px;
}
.timeline-marker {
    position: absolute;
    left: -30px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 0 0 2px currentColor;
}
.timeline-content {
    padding-left: 10px;
}
</style>
@endsection