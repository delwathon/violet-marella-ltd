@extends('layouts.app')

@section('title', 'New Rental')

@section('content')
<div class="content-area">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Create Rental</h1>
                <p class="page-subtitle">Register a new prop rental transaction</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('prop-rental.index', ['tab' => 'active-rentals']) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Rentals
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Rental Details</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('prop-rental.customers.create') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-user-plus me-1"></i>Add Customer
                        </a>
                        <a href="{{ route('prop-rental.props.create') }}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-guitar me-1"></i>Add Prop
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($customers->isEmpty())
                        <div class="alert alert-warning mb-4">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            No active customers available. <a href="{{ route('prop-rental.customers.create') }}" class="alert-link">Create a customer first</a>.
                        </div>
                    @endif

                    @if($availableProps->isEmpty())
                        <div class="alert alert-warning mb-4">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            No available props found. <a href="{{ route('prop-rental.props.create') }}" class="alert-link">Add a prop first</a>.
                        </div>
                    @endif

                    <form action="{{ route('prop-rental.rentals.store') }}" method="POST" id="createRentalForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Customer <span class="text-danger">*</span></label>
                                    <select class="form-select @error('customer_id') is-invalid @enderror" name="customer_id" id="rentalCustomer" required>
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} - {{ $customer->phone }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Prop <span class="text-danger">*</span></label>
                                    <select class="form-select @error('prop_id') is-invalid @enderror" name="prop_id" id="rentalProp" required>
                                        <option value="">Select Prop</option>
                                        @foreach($availableProps as $prop)
                                            <option value="{{ $prop->id }}"
                                                    data-rate="{{ $prop->daily_rate }}"
                                                    {{ old('prop_id') == $prop->id ? 'selected' : '' }}>
                                                {{ $prop->name }} - {{ $prop->formatted_daily_rate }}/day
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('prop_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                           name="start_date" id="rentalStartDate" value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                           name="end_date" id="rentalEndDate" value="{{ old('end_date') }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Daily Rate (₦)</label>
                                    <input type="number" class="form-control" id="dailyRate" readonly placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Total Amount (₦)</label>
                                    <input type="number" class="form-control fw-bold" id="totalAmount" readonly placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Security Deposit (₦) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('security_deposit') is-invalid @enderror"
                                           name="security_deposit" value="{{ old('security_deposit', 0) }}" required min="0" step="0.01">
                                    @error('security_deposit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Amount Paid (₦)</label>
                                    <input type="number" class="form-control @error('amount_paid') is-invalid @enderror"
                                           name="amount_paid" id="amountPaid" value="{{ old('amount_paid', 0) }}" min="0" step="0.01">
                                    @error('amount_paid')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Partial payment allowed. Balance will be due on return.</small>
                                </div>
                            </div>
                        </div>

                        <div id="balanceInfo" class="alert alert-info" style="display: none;"></div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input @error('agreement_signed') is-invalid @enderror"
                                   type="checkbox" name="agreement_signed" id="agreementSigned" value="1"
                                   {{ old('agreement_signed') ? 'checked' : '' }} required>
                            <label class="form-check-label" for="agreementSigned">
                                Rental agreement has been signed <span class="text-danger">*</span>
                            </label>
                            @error('agreement_signed')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="submitBtn" {{ $customers->isEmpty() || $availableProps->isEmpty() ? 'disabled' : '' }}>
                                <i class="fas fa-check me-2"></i>Create Rental
                            </button>
                            <a href="{{ route('prop-rental.index', ['tab' => 'active-rentals']) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function calculateRentalTotals() {
    const startDateInput = document.getElementById('rentalStartDate');
    const endDateInput = document.getElementById('rentalEndDate');
    const propSelect = document.getElementById('rentalProp');
    const amountPaidInput = document.getElementById('amountPaid');

    const dailyRateInput = document.getElementById('dailyRate');
    const totalAmountInput = document.getElementById('totalAmount');
    const balanceInfo = document.getElementById('balanceInfo');
    const submitBtn = document.getElementById('submitBtn');

    const startDate = startDateInput.value;
    const endDate = endDateInput.value;

    if (startDate) {
        endDateInput.min = startDate;
    }

    if (!startDate || !endDate || !propSelect.value) {
        dailyRateInput.value = '';
        totalAmountInput.value = '';
        balanceInfo.style.display = 'none';
        amountPaidInput.setCustomValidity('');
        return;
    }

    const start = new Date(startDate);
    const end = new Date(endDate);
    const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));

    if (days <= 0) {
        dailyRateInput.value = '';
        totalAmountInput.value = '';
        balanceInfo.style.display = 'none';
        amountPaidInput.setCustomValidity('End date must be after start date.');
        submitBtn.disabled = true;
        return;
    }

    const selectedOption = propSelect.options[propSelect.selectedIndex];
    const dailyRate = parseFloat(selectedOption.getAttribute('data-rate')) || 0;
    const totalAmount = days * dailyRate;
    const amountPaid = parseFloat(amountPaidInput.value) || 0;

    dailyRateInput.value = dailyRate.toFixed(2);
    totalAmountInput.value = totalAmount.toFixed(2);

    if (amountPaid > totalAmount) {
        amountPaidInput.setCustomValidity('Amount paid cannot exceed total amount.');
        submitBtn.disabled = true;

        balanceInfo.className = 'alert alert-danger';
        balanceInfo.innerHTML = '<strong>Error:</strong> Amount paid cannot exceed total amount.';
        balanceInfo.style.display = 'block';
        return;
    }

    amountPaidInput.setCustomValidity('');
    submitBtn.disabled = false;

    const balance = totalAmount - amountPaid;
    if (balance > 0) {
        balanceInfo.className = 'alert alert-warning';
        balanceInfo.innerHTML = '<strong>Balance Due:</strong> ₦' + balance.toLocaleString('en-NG', { minimumFractionDigits: 2 }) + ' will be collected on return.';
        balanceInfo.style.display = 'block';
    } else {
        balanceInfo.className = 'alert alert-success';
        balanceInfo.innerHTML = '<strong>Fully Paid:</strong> No outstanding balance for this rental.';
        balanceInfo.style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const today = new Date().toISOString().split('T')[0];
    const startDateInput = document.getElementById('rentalStartDate');
    const endDateInput = document.getElementById('rentalEndDate');
    const propSelect = document.getElementById('rentalProp');
    const amountPaidInput = document.getElementById('amountPaid');

    startDateInput.min = today;

    startDateInput.addEventListener('change', calculateRentalTotals);
    endDateInput.addEventListener('change', calculateRentalTotals);
    propSelect.addEventListener('change', calculateRentalTotals);
    amountPaidInput.addEventListener('input', calculateRentalTotals);

    calculateRentalTotals();
});
</script>
@endpush
