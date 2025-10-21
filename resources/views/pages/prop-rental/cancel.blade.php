@extends('layouts.app')

@section('title', 'Cancel Rental')

@section('content')
<div class="content-area">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Cancel Rental</h1>
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
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirm Cancellation
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <h6 class="alert-heading">Warning: This action cannot be undone</h6>
                        <p class="mb-0">Cancelling this rental will make the prop available for other bookings and update the customer's rental history.</p>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h6 class="card-title">Rental Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Customer:</strong> {{ $rental->customer->name }}</p>
                                    <p class="mb-2"><strong>Phone:</strong> {{ $rental->customer->phone }}</p>
                                    <p class="mb-0"><strong>Prop:</strong> {{ $rental->prop->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Start Date:</strong> {{ $rental->start_date->format('d M Y') }}</p>
                                    <p class="mb-2"><strong>End Date:</strong> {{ $rental->end_date->format('d M Y') }}</p>
                                    <p class="mb-0"><strong>Total Amount:</strong> {{ $rental->formatted_total_amount }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>Before Cancelling
                        </h6>
                        <ul class="mb-0">
                            <li>Contact the customer about the cancellation</li>
                            <li>Process any refunds as per your policy</li>
                            <li>Document the reason for cancellation</li>
                            <li>Update any related records</li>
                        </ul>
                    </div>

                    <form action="{{ route('prop-rental.rentals.cancel', $rental->id) }}" method="POST">
                        @csrf
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="confirmCancel" required>
                            <label class="form-check-label" for="confirmCancel">
                                I understand this action cannot be undone and I want to proceed with the cancellation
                            </label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times me-2"></i>Confirm Cancellation
                            </button>
                            <a href="{{ route('prop-rental.rentals.show', $rental->id) }}" class="btn btn-secondary">
                                Go Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection