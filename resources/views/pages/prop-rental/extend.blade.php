{{-- resources/views/pages/prop-rental/extend.blade.php --}}
@extends('layouts.app')

@section('title', 'Extend Rental')

@section('content')
<div class="content-area">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Extend Rental</h1>
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
                <div class="card-header">
                    <h5 class="mb-0">Extend Rental Period</h5>
                </div>
                <div class="card-body">
                    <!-- Current Rental Info -->
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Customer:</strong> {{ $rental->customer->name }}<br>
                                <strong>Prop:</strong> {{ $rental->prop->name }}
                            </div>
                            <div class="col-md-6">
                                <strong>Current End Date:</strong> {{ $rental->end_date->format('d M Y') }}<br>
                                <strong>Daily Rate:</strong> ₦{{ number_format($rental->daily_rate, 2) }}
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('prop-rental.rentals.extend', $rental->id) }}" method="POST" id="extendForm">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label">Additional Days <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control form-control-lg @error('additional_days') is-invalid @enderror" 
                                   name="additional_days" 
                                   id="additionalDays"
                                   value="{{ old('additional_days', 1) }}" 
                                   required 
                                   min="1" 
                                   max="365"
                                   onchange="calculateExtension()">
                            @error('additional_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <label class="text-muted small">New End Date</label>
                                        <div class="fs-5 fw-bold" id="newEndDate">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <label class="text-muted small">Additional Charge</label>
                                        <div class="fs-5 fw-bold text-primary" id="additionalCharge">₦0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Note:</strong> The customer will be charged for the additional days. Please ensure payment is collected.
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-2"></i>Extend Rental
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

@push('scripts')
<script>
const dailyRate = {{ $rental->daily_rate }};
const currentEndDate = new Date('{{ $rental->end_date->format('Y-m-d') }}');

function calculateExtension() {
    const additionalDays = parseInt(document.getElementById('additionalDays').value) || 0;
    
    if (additionalDays > 0) {
        // Calculate new end date
        const newDate = new Date(currentEndDate);
        newDate.setDate(newDate.getDate() + additionalDays);
        
        // Format date
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        document.getElementById('newEndDate').textContent = newDate.toLocaleDateString('en-US', options);
        
        // Calculate additional charge
        const additionalCharge = additionalDays * dailyRate;
        document.getElementById('additionalCharge').textContent = '₦' + additionalCharge.toLocaleString('en-NG', { minimumFractionDigits: 2 });
    } else {
        document.getElementById('newEndDate').textContent = '-';
        document.getElementById('additionalCharge').textContent = '₦0.00';
    }
}

// Calculate on page load
document.addEventListener('DOMContentLoaded', calculateExtension);
</script>
@endpush
@endsection