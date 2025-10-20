{{-- resources/views/pages/prop-rental/return.blade.php --}}
@extends('layouts.app')

@section('title', 'Return Prop')

@section('content')
<div class="content-area">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Return Prop</h1>
                <p class="page-subtitle">{{ $rental->rental_id }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('prop-rental.rentals.show', $rental->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-undo me-2"></i>Confirm Return
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Rental Summary</h6>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Customer:</strong> {{ $rental->customer->name }}</p>
                                <p class="mb-2"><strong>Phone:</strong> {{ $rental->customer->phone }}</p>
                                <p class="mb-0"><strong>Email:</strong> {{ $rental->customer->email }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Prop:</strong> {{ $rental->prop->name }}</p>
                                <p class="mb-2"><strong>Serial:</strong> {{ $rental->prop->serial_number }}</p>
                                <p class="mb-0"><strong>Rental Period:</strong> {{ $rental->duration }} days</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <label class="text-muted small">Total Amount</label>
                                    <div class="fs-4 fw-bold">{{ $rental->formatted_total_amount }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <label class="text-muted small">Security Deposit</label>
                                    <div class="fs-4 fw-bold">₦{{ number_format($rental->security_deposit, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <label class="text-muted small">Status</label>
                                    <div><span class="badge {{ $rental->status_badge_class }} fs-6">{{ $rental->status_display }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>Before Processing Return
                        </h6>
                        <ul class="mb-0">
                            <li>Inspect the prop for any damage</li>
                            <li>Verify all accessories are returned</li>
                            <li>Confirm payment has been received</li>
                            <li>Process security deposit refund if applicable</li>
                        </ul>
                    </div>

                    <form action="{{ route('prop-rental.rentals.return', $rental->id) }}" method="POST">
                        @csrf
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-check me-2"></i>Confirm Return
                            </button>
                            <a href="{{ route('prop-rental.rentals.show', $rental->id) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection