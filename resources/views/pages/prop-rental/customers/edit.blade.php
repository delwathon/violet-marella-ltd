@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
<div class="content-area">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Edit Customer</h1>
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
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i>Edit Customer Details
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('prop-rental.customers.update', $customer->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name', $customer->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           name="email" value="{{ old('email', $customer->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           name="phone" value="{{ old('phone', $customer->phone) }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      name="address" rows="3">{{ old('address', $customer->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Full residential or business address</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ID Number</label>
                            <input type="text" class="form-control @error('id_number') is-invalid @enderror" 
                                   name="id_number" value="{{ old('id_number', $customer->id_number) }}">
                            <div class="form-text">National ID, Driver's License, Passport, etc.</div>
                            @error('id_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> Customer status and statistics are managed automatically and cannot be edited directly.
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Customer
                            </button>
                            <a href="{{ route('prop-rental.customers.show', $customer->id) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Current Information Summary -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Current Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-2">
                                <small class="text-muted">Customer ID</small>
                                <div class="fw-bold">{{ $customer->customer_id }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-2">
                                <small class="text-muted">Status</small>
                                <div>
                                    <span class="badge bg-{{ $customer->status == 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($customer->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-2">
                                <small class="text-muted">Member Since</small>
                                <div>{{ $customer->created_at->format('d M Y') }}</div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="fs-3 fw-bold text-primary">{{ $customer->total_rentals }}</div>
                                <small class="text-muted">Total Rentals</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="fs-3 fw-bold text-warning">{{ $customer->current_rentals }}</div>
                                <small class="text-muted">Current Rentals</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="fs-3 fw-bold text-success">{{ $customer->formatted_total_spent }}</div>
                                <small class="text-muted">Total Spent</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection