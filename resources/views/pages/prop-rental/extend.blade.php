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
                        <h6 class="alert-heading mb-3">Current Rental Details</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <strong>Customer:</strong> {{ $rental->customer->name }}
                                </div>
                                <div class="mb-2">
                                    <strong>Prop:</strong> {{ $rental->prop->name }}
                                </div>
                                <div class="mb-2">
                                    <strong>Current End Date:</strong> 
                                    <span class="text-danger fw-bold">{{ $rental->end_date->format('d M Y') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <strong>Daily Rate:</strong> ₦{{ number_format($rental->daily_rate, 2) }}
                                </div>
                                <div class="mb-2">
                                    <strong>Original Total:</strong> {{ $rental->formatted_total_amount }}
                                </div>
                                @if($rental->balance_due > 0)
                                    <div class="mb-0">
                                        <strong>Outstanding Balance:</strong> 
                                        <span class="text-warning fw-bold">{{ $rental->formatted_balance_due }}</span>
                                    </div>
                                @else
                                    <div class="mb-0">
                                        <strong>Payment Status:</strong> 
                                        <span class="badge bg-success">Fully Paid</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('prop-rental.rentals.extend', $rental->id) }}" method="POST" id="extendForm">
                        @csrf
                        
                        <!-- Additional Days Input -->
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
                            <small class="text-muted">Enter the number of days to extend the rental</small>
                        </div>

                        <!-- Calculated Fields Display -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <label class="text-muted small">New End Date</label>
                                        <div class="fs-5 fw-bold text-primary" id="newEndDate">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <label class="text-muted small">Extension Charge</label>
                                        <div class="fs-5 fw-bold text-success" id="extensionCharge">₦0.00</div>
                                        <small class="text-muted" id="extensionFormula">0 days × ₦0</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Outstanding Balance Alert (if applicable) -->
                        @if($rental->balance_due > 0)
                            <div class="alert alert-warning">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-exclamation-triangle fa-2x me-3 mt-1"></i>
                                    <div>
                                        <h6 class="alert-heading">Outstanding Balance</h6>
                                        <p class="mb-2">
                                            This rental has an outstanding balance of <strong>{{ $rental->formatted_balance_due }}</strong> 
                                            from the original rental period.
                                        </p>
                                        <p class="mb-0">
                                            <strong>Total Amount Due:</strong> Outstanding Balance + Extension Charge
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Total Calculation Section -->
                        <div class="card border-primary mb-4">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Payment Calculation</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="text-muted small">Previous Balance Due</label>
                                        <input type="text" 
                                               class="form-control fw-bold" 
                                               id="previousBalance" 
                                               value="{{ $rental->formatted_balance_due }}" 
                                               readonly 
                                               style="background-color: #fff3cd;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small">Extension Charge</label>
                                        <input type="text" 
                                               class="form-control fw-bold" 
                                               id="extensionChargeDisplay" 
                                               value="₦0.00" 
                                               readonly 
                                               style="background-color: #d1ecf1;">
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="text-muted small">New Total Amount Due</label>
                                        <input type="text" 
                                               class="form-control form-control-lg fw-bold text-primary" 
                                               id="newTotalAmount" 
                                               value="₦0.00" 
                                               readonly 
                                               style="background-color: #e7f3ff; font-size: 1.25rem;">
                                        <small class="text-muted">Previous Balance + Extension Charge</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Amount to Pay Now (₦) <span class="text-danger">*</span></label>
                                        <input type="number" 
                                               class="form-control @error('amount_paid') is-invalid @enderror" 
                                               name="amount_paid" 
                                               id="amountPaid"
                                               value="{{ old('amount_paid', 0) }}" 
                                               required
                                               min="0" 
                                               step="0.01"
                                               oninput="validateExtensionPayment()">
                                        @error('amount_paid')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Can be partial, balance due on return</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small">Remaining Balance After Payment</label>
                                        <input type="text" 
                                               class="form-control fw-bold" 
                                               id="remainingBalance" 
                                               value="₦0.00" 
                                               readonly 
                                               style="background-color: #f8f9fa;">
                                    </div>
                                </div>

                                <!-- Balance Alert (dynamic) -->
                                <div id="balanceAlert" class="alert alert-info mt-3" style="display: none;">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span id="balanceAlertText"></span>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>Important Notes
                            </h6>
                            <ul class="mb-0">
                                <li>The rental period will be extended by the specified number of days</li>
                                <li>Customer will be charged at the same daily rate (₦{{ number_format($rental->daily_rate, 2) }})</li>
                                @if($rental->balance_due > 0)
                                    <li class="text-warning"><strong>The outstanding balance must be settled before or during return</strong></li>
                                @endif
                                <li>Ensure payment is collected before confirming the extension</li>
                            </ul>
                        </div>

                        <!-- Hidden fields for calculation -->
                        <input type="hidden" id="dailyRate" value="{{ $rental->daily_rate }}">
                        <input type="hidden" id="currentBalanceDue" value="{{ $rental->balance_due }}">
                        <input type="hidden" id="currentEndDate" value="{{ $rental->end_date->format('Y-m-d') }}">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success" id="submitBtn">
                                <i class="fas fa-check me-2"></i>Confirm Extension
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

@include('pages.prop-rental.modals.confirm-extension')

@push('scripts')
<script>
const dailyRate = parseFloat(document.getElementById('dailyRate').value);
const currentBalanceDue = parseFloat(document.getElementById('currentBalanceDue').value);
const currentEndDate = new Date(document.getElementById('currentEndDate').value);

function calculateExtension() {
    const additionalDays = parseInt(document.getElementById('additionalDays').value) || 0;
    
    if (additionalDays > 0) {
        // Calculate new end date
        const newDate = new Date(currentEndDate);
        newDate.setDate(newDate.getDate() + additionalDays);
        
        // Format date
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        document.getElementById('newEndDate').textContent = newDate.toLocaleDateString('en-US', options);
        
        // Calculate extension charge
        const extensionCharge = additionalDays * dailyRate;
        document.getElementById('extensionCharge').textContent = '₦' + extensionCharge.toLocaleString('en-NG', { minimumFractionDigits: 2 });
        document.getElementById('extensionChargeDisplay').value = '₦' + extensionCharge.toLocaleString('en-NG', { minimumFractionDigits: 2 });
        document.getElementById('extensionFormula').textContent = additionalDays + ' days × ₦' + dailyRate.toLocaleString('en-NG', { minimumFractionDigits: 2 });
        
        // Calculate new total amount (previous balance + extension charge)
        const newTotalAmount = currentBalanceDue + extensionCharge;
        document.getElementById('newTotalAmount').value = '₦' + newTotalAmount.toLocaleString('en-NG', { minimumFractionDigits: 2 });
        
        // Validate payment
        validateExtensionPayment();
    } else {
        document.getElementById('newEndDate').textContent = '-';
        document.getElementById('extensionCharge').textContent = '₦0.00';
        document.getElementById('extensionChargeDisplay').value = '₦0.00';
        document.getElementById('extensionFormula').textContent = '0 days × ₦0';
        document.getElementById('newTotalAmount').value = '₦0.00';
    }
}

function validateExtensionPayment() {
    const additionalDays = parseInt(document.getElementById('additionalDays').value) || 0;
    
    if (additionalDays <= 0) {
        return;
    }
    
    const extensionCharge = additionalDays * dailyRate;
    const newTotalAmount = currentBalanceDue + extensionCharge;
    const amountPaid = parseFloat(document.getElementById('amountPaid').value) || 0;
    const remainingBalance = newTotalAmount - amountPaid;
    
    const amountPaidInput = document.getElementById('amountPaid');
    const balanceAlert = document.getElementById('balanceAlert');
    const balanceAlertText = document.getElementById('balanceAlertText');
    const submitBtn = document.getElementById('submitBtn');
    
    // Update remaining balance display
    document.getElementById('remainingBalance').value = '₦' + remainingBalance.toLocaleString('en-NG', { minimumFractionDigits: 2 });
    
    // Validate amount paid
    if (amountPaid > newTotalAmount) {
        amountPaidInput.setCustomValidity('Amount cannot exceed total amount due');
        amountPaidInput.classList.add('is-invalid');
        
        balanceAlert.className = 'alert alert-danger mt-3';
        balanceAlert.style.display = 'block';
        balanceAlertText.innerHTML = '<strong>Error:</strong> Amount paid (₦' + amountPaid.toLocaleString('en-NG', { minimumFractionDigits: 2 }) + 
                                     ') cannot exceed total amount due (₦' + newTotalAmount.toLocaleString('en-NG', { minimumFractionDigits: 2 }) + ')';
        submitBtn.disabled = true;
    } else {
        amountPaidInput.setCustomValidity('');
        amountPaidInput.classList.remove('is-invalid');
        submitBtn.disabled = false;
        
        if (remainingBalance > 0) {
            balanceAlert.className = 'alert alert-warning mt-3';
            balanceAlert.style.display = 'block';
            balanceAlertText.innerHTML = '<strong>Partial Payment:</strong> Balance of ₦' + remainingBalance.toLocaleString('en-NG', { minimumFractionDigits: 2 }) + 
                                        ' will be due on return.';
        } else if (remainingBalance === 0) {
            balanceAlert.className = 'alert alert-success mt-3';
            balanceAlert.style.display = 'block';
            balanceAlertText.innerHTML = '<strong><i class="fas fa-check-circle me-2"></i>Fully Paid:</strong> No balance remaining.';
        } else {
            balanceAlert.style.display = 'none';
        }
    }
}

// Calculate on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateExtension();

    document.getElementById('extendForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default submission
        showConfirmationModal(); // Show Bootstrap modal instead
    });
});

function showConfirmationModal() {
    const additionalDays = parseInt(document.getElementById('additionalDays').value) || 0;
    const dailyRate = parseFloat(document.getElementById('dailyRate').value);
    const currentBalanceDue = parseFloat(document.getElementById('currentBalanceDue').value);
    const amountPaid = parseFloat(document.getElementById('amountPaid').value) || 0;
    
    const extensionCharge = additionalDays * dailyRate;
    const newTotalDue = currentBalanceDue + extensionCharge;
    const remainingBalance = newTotalDue - amountPaid;
    
    // Calculate new end date
    const currentEndDate = new Date(document.getElementById('currentEndDate').value);
    const newDate = new Date(currentEndDate);
    newDate.setDate(newDate.getDate() + additionalDays);
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    
    // Update modal content
    document.getElementById('modalAdditionalDays').textContent = additionalDays + ' day(s)';
    document.getElementById('modalNewEndDate').textContent = newDate.toLocaleDateString('en-US', options);
    document.getElementById('modalExtensionCharge').textContent = '₦' + extensionCharge.toLocaleString('en-NG', { minimumFractionDigits: 2 });
    document.getElementById('modalTotalDue').textContent = '₦' + newTotalDue.toLocaleString('en-NG', { minimumFractionDigits: 2 });
    document.getElementById('modalPaymentNow').textContent = '₦' + amountPaid.toLocaleString('en-NG', { minimumFractionDigits: 2 });
    
    const remainingBalanceElement = document.getElementById('modalRemainingBalance');
    remainingBalanceElement.textContent = '₦' + remainingBalance.toLocaleString('en-NG', { minimumFractionDigits: 2 });
    
    // Update balance alert
    const modalBalanceAlert = document.getElementById('modalBalanceAlert');
    const modalBalanceAlertText = document.getElementById('modalBalanceAlertText');
    
    if (remainingBalance > 0) {
        remainingBalanceElement.className = 'col-5 text-end fw-bold text-warning';
        modalBalanceAlert.className = 'alert alert-warning';
        modalBalanceAlert.style.display = 'block';
        modalBalanceAlertText.innerHTML = '<strong>Partial Payment:</strong> A balance of ₦' + 
            remainingBalance.toLocaleString('en-NG', { minimumFractionDigits: 2 }) + ' will be due on return.';
    } else if (remainingBalance === 0) {
        remainingBalanceElement.className = 'col-5 text-end fw-bold text-success';
        modalBalanceAlert.className = 'alert alert-success';
        modalBalanceAlert.style.display = 'block';
        modalBalanceAlertText.innerHTML = '<strong><i class="fas fa-check-circle me-2"></i>Fully Paid:</strong> No balance remaining.';
    } else {
        modalBalanceAlert.style.display = 'none';
    }
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('confirmExtensionModal'));
    modal.show();
}

function submitExtensionForm() {
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('confirmExtensionModal'));
    modal.hide();
    
    // Submit form
    document.getElementById('extendForm').submit();
}
</script>
@endpush
@endsection