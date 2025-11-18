@extends('layouts.app')

@section('title', 'Rental Details')

@section('content')
<div class="content-area">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Rental Details</h1>
                <p class="page-subtitle">{{ $rental->rental_id }}</p>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    @if($rental->status === 'active')
                        <a href="{{ route('prop-rental.rentals.extend-form', $rental->id) }}" class="btn btn-success">
                            <i class="fas fa-calendar-plus me-2"></i>Extend
                        </a>
                        <a href="{{ route('prop-rental.rentals.return-form', $rental->id) }}" class="btn btn-warning">
                            <i class="fas fa-undo me-2"></i>Return
                        </a>
                    @endif
                    <a href="{{ route('prop-rental.index', ['tab' => 'active-rentals']) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Alerts -->
    @if($rental->isOverdue())
        <div class="alert alert-danger">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                <div>
                    <h6 class="alert-heading mb-1">Rental Overdue!</h6>
                    <p class="mb-0">This rental was due on {{ $rental->end_date->format('d M Y') }}. Please contact customer immediately.</p>
                </div>
            </div>
        </div>
    @elseif($rental->status === 'active' && $rental->days_remaining <= 1)
        <div class="alert alert-warning">
            <div class="d-flex align-items-center">
                <i class="fas fa-clock fa-2x me-3"></i>
                <div>
                    <h6 class="alert-heading mb-1">Due Soon!</h6>
                    <p class="mb-0">This rental is due {{ $rental->days_remaining === 0 ? 'today' : 'tomorrow' }}.</p>
                </div>
            </div>
        </div>
    @endif

    @if($rental->balance_due > 0 && $rental->status === 'active')
        <div class="alert alert-info">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle fa-2x me-3"></i>
                <div>
                    <h6 class="alert-heading mb-1">Outstanding Balance: {{ $rental->formatted_balance_due }}</h6>
                    <p class="mb-0">This balance will need to be collected when the prop is returned.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Rental Information Card -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Rental Information</h5>
                    <span class="badge {{ $rental->status_badge_class }} fs-6">{{ $rental->status_display }}</span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Rental ID</label>
                            <div class="fw-bold">{{ strtoupper($rental->rental_id) }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Created On</label>
                            <div>{{ $rental->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Start Date</label>
                            <div class="fw-bold">{{ $rental->start_date->format('d M Y, h:i A') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">End Date</label>
                            <div class="fw-bold">{{ $rental->end_date->format('d M Y, h:i A') }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Duration</label>
                            <div class="fw-bold">{{ $rental->duration }} days</div>
                        </div>
                        @if($rental->status === 'active')
                            <div class="col-md-6">
                                <label class="text-muted small">Days Remaining</label>
                                <div class="fw-bold {{ $rental->days_remaining < 2 ? 'text-danger' : 'text-success' }}">
                                    <i class="fas fa-clock me-1"></i>{{ $rental->days_remaining }} days
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Payment Details Card -->
            <div class="card mb-3 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>Payment Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="card bg-light h-100">
                                <div class="card-body text-center">
                                    <label class="text-muted small d-block mb-2">Daily Rate</label>
                                    <div class="fs-4 fw-bold text-primary">
                                        ₦{{ number_format($rental->daily_rate, 2) }}
                                    </div>
                                    <small class="text-muted">per day</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light h-100">
                                <div class="card-body text-center">
                                    <label class="text-muted small d-block mb-2">Total Amount</label>
                                    <div class="fs-4 fw-bold text-info">
                                        {{ $rental->formatted_total_amount }}
                                    </div>
                                    <small class="text-muted">{{ $rental->duration }} × daily rate</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light h-100">
                                <div class="card-body text-center">
                                    <label class="text-muted small d-block mb-2">Security Deposit</label>
                                    <div class="fs-4 fw-bold text-secondary">
                                        {{ $rental->formatted_security_deposit }}
                                    </div>
                                    <small class="text-muted">refundable</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="card bg-success bg-opacity-10 h-100">
                                <div class="card-body text-center">
                                    <label class="text-muted small d-block mb-2">Amount Paid</label>
                                    <div class="fs-3 fw-bold text-success">
                                        {{ $rental->formatted_amount_paid }}
                                    </div>
                                    <small class="text-success">
                                        <i class="fas fa-check-circle me-1"></i>Received
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card {{ $rental->balance_due > 0 ? 'bg-warning' : 'bg-success' }} bg-opacity-10 h-100">
                                <div class="card-body text-center">
                                    <label class="text-muted small d-block mb-2">Balance Due</label>
                                    <div class="fs-3 fw-bold {{ $rental->balance_due > 0 ? 'text-warning' : 'text-success' }}">
                                        {{ $rental->formatted_balance_due }}
                                    </div>
                                    <small class="{{ $rental->balance_due > 0 ? 'text-warning' : 'text-success' }}">
                                        @if($rental->balance_due > 0)
                                            <i class="fas fa-exclamation-triangle me-1"></i>Outstanding
                                        @else
                                            <i class="fas fa-check-circle me-1"></i>Settled
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light h-100">
                                <div class="card-body text-center">
                                    <label class="text-muted small d-block mb-2">Payment Status</label>
                                    <div class="mb-2">
                                        <span class="badge {{ $rental->payment_status_badge_class }} fs-5">
                                            {{ $rental->payment_status }}
                                        </span>
                                    </div>
                                    @if($rental->balance_due > 0)
                                        <small class="text-muted">
                                            {{ number_format(($rental->amount_paid / $rental->total_amount) * 100, 1) }}% paid
                                        </small>
                                    @else
                                        <small class="text-success">
                                            <i class="fas fa-check me-1"></i>100% paid
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($rental->balance_due > 0)
                        <div class="alert alert-warning mb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Balance to be collected:</strong> {{ $rental->formatted_balance_due }}
                                </div>
                                @if($rental->status === 'active')
                                    <a href="{{ route('prop-rental.rentals.return-form', $rental->id) }}" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-hand-holding-usd me-1"></i>Collect on Return
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cancellation/Refund Info (if applicable) -->
            @if($rental->status === 'cancelled' && $rental->refund_amount > 0)
                <div class="card mb-3 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-undo me-2"></i>Cancellation & Refund
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-muted small">Cancelled On</label>
                                <div class="fw-bold">{{ $rental->cancelled_at->format('d M Y, h:i A') }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Refund Amount</label>
                                <div class="fw-bold text-danger fs-4">{{ $rental->formatted_refund_amount }}</div>
                            </div>
                        </div>
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            This rental was cancelled and requires a refund to be processed to the customer.
                        </div>
                    </div>
                </div>
            @endif

            <!-- Return Info (if applicable) -->
            @if($rental->returned_at)
                <div class="card mb-3 border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>Return Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-muted small">Returned On</label>
                                <div class="fw-bold">{{ $rental->returned_at->format('d M Y, h:i A') }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Return Status</label>
                                <div>
                                    @if($rental->returned_at->lte($rental->end_date))
                                        <span class="badge bg-success">On Time</span>
                                    @else
                                        <span class="badge bg-warning">Late Return</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Notes Section -->
            @if($rental->notes)
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-sticky-note me-2"></i>Notes & History
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="notes-content" style="white-space: pre-line; font-family: monospace; font-size: 0.9rem;">{{ $rental->notes }}</div>
                    </div>
                </div>
            @endif

            <!-- Audit Trail -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Audit Trail
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <strong>Rental Created</strong>
                                    <small class="text-muted">{{ $rental->created_at->format('d M Y, h:i A') }}</small>
                                </div>
                                <div class="text-muted small">
                                    @if($rental->creator)
                                        By {{ $rental->creator->first_name }} {{ $rental->creator->last_name }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($rental->returned_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between">
                                        <strong>Prop Returned</strong>
                                        <small class="text-muted">{{ $rental->returned_at->format('d M Y, h:i A') }}</small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($rental->cancelled_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between">
                                        <strong>Rental Cancelled</strong>
                                        <small class="text-muted">{{ $rental->cancelled_at->format('d M Y, h:i A') }}</small>
                                    </div>
                                    @if($rental->canceller)
                                        <div class="text-muted small">
                                            By {{ $rental->canceller->first_name }} {{ $rental->canceller->last_name }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Prop Details -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-guitar me-2"></i>Prop Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="{{ $rental->prop->image ?? 'fas fa-music' }} fa-4x text-primary"></i>
                    </div>
                    <h6 class="fw-bold text-center mb-3">{{ $rental->prop->name }}</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Brand:</td>
                            <td class="fw-semibold">{{ $rental->prop->brand }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Model:</td>
                            <td class="fw-semibold">{{ $rental->prop->model }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Serial:</td>
                            <td class="fw-semibold">{{ $rental->prop->serial_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Category:</td>
                            <td><span class="badge bg-info">{{ ucfirst($rental->prop->category) }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Condition:</td>
                            <td><span class="badge bg-success">{{ ucfirst($rental->prop->condition) }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Customer Details -->
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-user me-2"></i>Customer Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="customer-avatar me-3" style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #8b5cf6); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                            {{ $rental->customer->initials }}
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">{{ $rental->customer->name }}</h6>
                            <small class="text-muted">{{ $rental->customer->customer_id }}</small>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <i class="fas fa-phone text-muted me-2"></i>
                        <a href="tel:{{ $rental->customer->phone }}">{{ $rental->customer->phone }}</a>
                    </div>
                    <div class="mb-2">
                        <i class="fas fa-envelope text-muted me-2"></i>
                        <a href="mailto:{{ $rental->customer->email }}">{{ $rental->customer->email }}</a>
                    </div>
                    @if($rental->customer->address)
                        <div class="mb-2">
                            <i class="fas fa-map-marker-alt text-muted me-2"></i>
                            {{ $rental->customer->address }}
                        </div>
                    @endif
                    
                    <hr>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="fw-bold text-primary fs-4">{{ $rental->customer->total_rentals }}</div>
                                <small class="text-muted">Total Rentals</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-success fs-4">{{ $rental->customer->formatted_total_spent }}</div>
                            <small class="text-muted">Total Spent</small>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('prop-rental.lounge.customers.show', $rental->customer->id) }}" class="btn btn-sm btn-outline-primary w-100">
                            <i class="fas fa-user me-2"></i>View Customer Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            @if($rental->status === 'active')
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>Quick Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('prop-rental.rentals.extend-form', $rental->id) }}" class="btn btn-success">
                                <i class="fas fa-calendar-plus me-2"></i>Extend Rental
                            </a>
                            <a href="{{ route('prop-rental.rentals.return-form', $rental->id) }}" class="btn btn-warning">
                                <i class="fas fa-undo me-2"></i>Return Prop
                                @if($rental->balance_due > 0)
                                    <span class="badge bg-danger ms-2">
                                        Balance: {{ $rental->formatted_balance_due }}
                                    </span>
                                @endif
                            </a>
                            <hr class="my-2">
                            <a href="{{ route('prop-rental.rentals.cancel-form', $rental->id) }}" class="btn btn-outline-danger">
                                <i class="fas fa-times me-2"></i>Cancel Rental
                                @if($rental->amount_paid > 0)
                                    <span class="badge bg-light text-dark ms-2">
                                        Refund: {{ $rental->formatted_amount_paid }}
                                    </span>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Contact Customer -->
            <div class="card mt-3">
                <div class="card-body text-center">
                    <h6 class="mb-3">Contact Customer</h6>
                    <div class="d-grid gap-2">
                        <a href="tel:{{ $rental->customer->phone }}" class="btn btn-primary">
                            <i class="fas fa-phone me-2"></i>Call Customer
                        </a>
                        <a href="mailto:{{ $rental->customer->email }}" class="btn btn-outline-primary">
                            <i class="fas fa-envelope me-2"></i>Send Email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -24px;
    top: 12px;
    height: calc(100% - 12px);
    width: 2px;
    background-color: #dee2e6;
}

.timeline-content {
    padding: 10px 15px;
    background-color: #f8f9fa;
    border-radius: 8px;
    border-left: 3px solid #6366f1;
}
</style>
@endpush
@endsection