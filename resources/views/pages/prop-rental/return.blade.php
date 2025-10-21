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
                    <!-- Rental Summary -->
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

                    <!-- Payment Summary -->
                    <div class="card border-primary mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">Payment Summary</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="text-muted small">Total Amount</label>
                                    <div class="fs-5 fw-bold">{{ $rental->formatted_total_amount }}</div>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted small">Amount Paid</label>
                                    <div class="fs-5 fw-bold text-success">{{ $rental->formatted_amount_paid }}</div>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted small">Security Deposit</label>
                                    <div class="fs-5 fw-bold">{{ $rental->formatted_security_deposit }}</div>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted small">Balance Due</label>
                                    <div class="fs-5 fw-bold {{ $rental->balance_due > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $rental->formatted_balance_due }}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <label class="text-muted small">Payment Status</label>
                                    <div>
                                        <span class="badge {{ $rental->payment_status_badge_class }} fs-6">
                                            {{ $rental->payment_status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Balance Collection (if applicable) -->
                    @if($rental->balance_due > 0)
                        <div class="alert alert-warning">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-exclamation-triangle fa-2x me-3 mt-1"></i>
                                <div>
                                    <h6 class="alert-heading">Outstanding Balance</h6>
                                    <p class="mb-0">
                                        This rental has an outstanding balance of <strong>{{ $rental->formatted_balance_due }}</strong> 
                                        that needs to be collected before completing the return.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('prop-rental.rentals.return', $rental->id) }}" method="POST" id="returnForm">
                            @csrf
                            
                            <div class="card border-success mb-4">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Collect Final Payment</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">Final Payment Amount (₦) <span class="text-danger">*</span></label>
                                            <input type="number" 
                                                   class="form-control form-control-lg @error('final_payment') is-invalid @enderror" 
                                                   name="final_payment" 
                                                   id="finalPayment"
                                                   value="{{ old('final_payment', $rental->balance_due) }}" 
                                                   required
                                                   min="0" 
                                                   max="{{ $rental->balance_due }}"
                                                   step="0.01"
                                                   oninput="updateRemainingBalance()">
                                            @error('final_payment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Enter amount collected from customer</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-muted small">Remaining After Payment</label>
                                            <input type="text" 
                                                   class="form-control form-control-lg fw-bold" 
                                                   id="remainingBalance" 
                                                   value="{{ $rental->formatted_balance_due }}" 
                                                   readonly 
                                                   style="background-color: #f8f9fa;">
                                        </div>
                                    </div>

                                    <div id="paymentAlert" class="alert mt-3" style="display: none;"></div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <h6 class="alert-heading">
                                    <i class="fas fa-info-circle me-2"></i>Before Processing Return
                                </h6>
                                <ul class="mb-0">
                                    <li><strong>Inspect the prop</strong> for any damage or issues</li>
                                    <li><strong>Verify all accessories</strong> are returned</li>
                                    <li><strong>Collect the final payment</strong> of {{ $rental->formatted_balance_due }}</li>
                                    <li><strong>Process security deposit refund</strong> if applicable</li>
                                    <li><strong>Update prop condition</strong> if needed</li>
                                </ul>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning" id="submitBtn">
                                    <i class="fas fa-check me-2"></i>Confirm Return & Collect Payment
                                </button>
                                <a href="{{ route('prop-rental.rentals.show', $rental->id) }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    @else
                        <!-- No balance due - simple return -->
                        <div class="alert alert-success">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle fa-2x me-3"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Payment Complete</h6>
                                    <p class="mb-0">All payments have been settled. You can proceed with the return.</p>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>Before Processing Return
                            </h6>
                            <ul class="mb-0">
                                <li><strong>Inspect the prop</strong> for any damage or issues</li>
                                <li><strong>Verify all accessories</strong> are returned</li>
                                <li><strong>Process security deposit refund</strong> if applicable</li>
                                <li><strong>Update prop condition</strong> if needed</li>
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
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($rental->balance_due > 0)
@push('scripts')
<script>
    const balanceDue = {{ $rental->balance_due }};

    function updateRemainingBalance() {
        const finalPayment = parseFloat(document.getElementById('finalPayment').value) || 0;
        const remaining = balanceDue - finalPayment;
        const paymentAlert = document.getElementById('paymentAlert');
        const submitBtn = document.getElementById('submitBtn');
        const finalPaymentInput = document.getElementById('finalPayment');
        
        // Update remaining balance display
        document.getElementById('remainingBalance').value = '₦' + remaining.toLocaleString('en-NG', { minimumFractionDigits: 2 });
        
        // Validation and alerts
        if (finalPayment > balanceDue) {
            finalPaymentInput.setCustomValidity('Payment cannot exceed balance due');
            finalPaymentInput.classList.add('is-invalid');
            
            paymentAlert.className = 'alert alert-danger mt-3';
            paymentAlert.style.display = 'block';
            paymentAlert.innerHTML = '<strong>Error:</strong> Payment amount (₦' + finalPayment.toLocaleString('en-NG', { minimumFractionDigits: 2 }) + 
                                    ') cannot exceed balance due (₦' + balanceDue.toLocaleString('en-NG', { minimumFractionDigits: 2 }) + ')';
            submitBtn.disabled = true;
        } else if (finalPayment < 0) {
            finalPaymentInput.setCustomValidity('Payment must be positive');
            finalPaymentInput.classList.add('is-invalid');
            
            paymentAlert.className = 'alert alert-danger mt-3';
            paymentAlert.style.display = 'block';
            paymentAlert.innerHTML = '<strong>Error:</strong> Payment amount cannot be negative';
            submitBtn.disabled = true;
        } else {
            finalPaymentInput.setCustomValidity('');
            finalPaymentInput.classList.remove('is-invalid');
            submitBtn.disabled = false;
            
            if (remaining > 0) {
                paymentAlert.className = 'alert alert-warning mt-3';
                paymentAlert.style.display = 'block';
                paymentAlert.innerHTML = '<strong>Partial Payment:</strong> A balance of ₦' + remaining.toLocaleString('en-NG', { minimumFractionDigits: 2 }) + 
                                        ' will remain outstanding after this payment.';
            } else if (remaining === 0) {
                paymentAlert.className = 'alert alert-success mt-3';
                paymentAlert.style.display = 'block';
                paymentAlert.innerHTML = '<strong><i class="fas fa-check-circle me-2"></i>Full Payment:</strong> All amounts will be settled with this payment.';
            } else {
                paymentAlert.style.display = 'none';
            }
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateRemainingBalance();
        
        // Form submission confirmation
        const returnForm = document.getElementById('returnForm');
        if (returnForm) {
            returnForm.addEventListener('submit', function(e) {
                const finalPayment = parseFloat(document.getElementById('finalPayment').value) || 0;
                const remaining = balanceDue - finalPayment;
                
                let confirmMessage = 'Confirm prop return?\n\n';
                confirmMessage += `Final payment collected: ₦${finalPayment.toLocaleString('en-NG', { minimumFractionDigits: 2 })}\n`;
                
                if (remaining > 0) {
                    confirmMessage += `Remaining balance: ₦${remaining.toLocaleString('en-NG', { minimumFractionDigits: 2 })} (outstanding)`;
                } else {
                    confirmMessage += `Status: Fully paid`;
                }
                
                if (!confirm(confirmMessage)) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endpush
@endif
@endsection