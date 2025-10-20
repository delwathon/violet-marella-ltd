{{-- resources/views/pages/prop-rental/show.blade.php --}}
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
                <a href="{{ route('prop-rental.index', ['tab' => 'active-rentals']) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Rentals
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Rental Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted">Rental ID</label>
                            <div class="fw-bold">{{ strtoupper($rental->rental_id) }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted">Status</label>
                            <div>
                                <span class="badge {{ $rental->status_badge_class }}">{{ $rental->status_display }}</span>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted">Start Date</label>
                            <div class="fw-bold">{{ $rental->start_date->format('d M Y, h:i A') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted">End Date</label>
                            <div class="fw-bold">{{ $rental->end_date->format('d M Y, h:i A') }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted">Duration</label>
                            <div class="fw-bold">{{ $rental->duration }} days</div>
                        </div>
                        @if($rental->status === 'active')
                            <div class="col-md-6">
                                <label class="text-muted">Days Remaining</label>
                                <div class="fw-bold text-{{ $rental->days_remaining < 2 ? 'danger' : 'success' }}">
                                    {{ $rental->days_remaining }} days
                                </div>
                            </div>
                        @endif
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted">Daily Rate</label>
                            <div class="fw-bold">₦{{ number_format($rental->daily_rate, 2) }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted">Total Amount</label>
                            <div class="fw-bold text-primary">{{ $rental->formatted_total_amount }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted">Security Deposit</label>
                            <div class="fw-bold">₦{{ number_format($rental->security_deposit, 2) }}</div>
                        </div>
                    </div>

                    @if($rental->notes)
                        <hr>
                        <div class="mb-3">
                            <label class="text-muted">Notes</label>
                            <div>{{ $rental->notes }}</div>
                        </div>
                    @endif

                    @if($rental->returned_at)
                        <hr>
                        <div class="mb-3">
                            <label class="text-muted">Returned At</label>
                            <div class="fw-bold">{{ $rental->returned_at->format('d M Y, h:i A') }}</div>
                        </div>
                    @endif

                    @if($rental->creator)
                        <hr>
                        <div class="mb-3">
                            <label class="text-muted">Created By</label>
                            <div>{{ $rental->creator->first_name }} {{ $rental->creator->last_name }}</div>
                            <small class="text-muted">{{ $rental->created_at->format('d M Y, h:i A') }}</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Prop Details -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">Prop Details</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="{{ $rental->prop->image ?? 'fas fa-music' }} fa-4x text-primary"></i>
                    </div>
                    <h6 class="fw-bold">{{ $rental->prop->name }}</h6>
                    <div class="mb-2">
                        <small class="text-muted">Brand:</small> {{ $rental->prop->brand }}
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Model:</small> {{ $rental->prop->model }}
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Serial:</small> {{ $rental->prop->serial_number }}
                    </div>
                    <div>
                        <small class="text-muted">Condition:</small> {{ ucfirst($rental->prop->condition) }}
                    </div>
                </div>
            </div>

            <!-- Customer Details -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">Customer Details</h6>
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
                        <i class="fas fa-phone me-2 text-muted"></i>
                        <a href="tel:{{ $rental->customer->phone }}">{{ $rental->customer->phone }}</a>
                    </div>
                    <div class="mb-2">
                        <i class="fas fa-envelope me-2 text-muted"></i>
                        <a href="mailto:{{ $rental->customer->email }}">{{ $rental->customer->email }}</a>
                    </div>
                    @if($rental->customer->address)
                        <div class="mb-2">
                            <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                            {{ $rental->customer->address }}
                        </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-muted">Total Rentals</small>
                            <div class="fw-bold">{{ $rental->customer->total_rentals }}</div>
                        </div>
                        <div>
                            <small class="text-muted">Total Spent</small>
                            <div class="fw-bold">{{ $rental->customer->formatted_total_spent }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if($rental->status === 'active')
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('prop-rental.rentals.extend-form', $rental->id) }}" class="btn btn-success">
                                <i class="fas fa-calendar-plus me-2"></i>Extend Rental
                            </a>
                            <a href="{{ route('prop-rental.rentals.return-form', $rental->id) }}" class="btn btn-warning">
                                <i class="fas fa-undo me-2"></i>Return Prop
                            </a>
                            <a href="{{ route('prop-rental.rentals.cancel-form', $rental->id) }}" class="btn btn-outline-danger">
                                <i class="fas fa-times me-2"></i>Cancel Rental
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection