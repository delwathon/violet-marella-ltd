@extends('layouts.app')

@section('title', 'Deactivate Customer')

@section('content')
<div class="content-area">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Deactivate Customer</h1>
                <p class="page-subtitle">{{ $customer->name }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('prop-rental.customers.show', $customer->id) }}" class="btn btn-outline-secondary">
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
                        <i class="fas fa-user-slash me-2"></i>Confirm Deactivation
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        Deactivated customers cannot create new rentals until they are reactivated.
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Name:</strong> {{ $customer->name }}</p>
                                    <p class="mb-2"><strong>Customer ID:</strong> {{ $customer->customer_id }}</p>
                                    <p class="mb-0"><strong>Phone:</strong> {{ $customer->phone }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Email:</strong> {{ $customer->email }}</p>
                                    <p class="mb-2"><strong>Total Rentals:</strong> {{ $customer->total_rentals }}</p>
                                    <p class="mb-0"><strong>Current Rentals:</strong> {{ $customer->current_rentals }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('prop-rental.customers.deactivate', $customer->id) }}" method="POST">
                        @csrf
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="confirmDeactivate" required>
                            <label class="form-check-label" for="confirmDeactivate">
                                I confirm I want to deactivate this customer
                            </label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-user-slash me-2"></i>Deactivate Customer
                            </button>
                            <a href="{{ route('prop-rental.customers.show', $customer->id) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
