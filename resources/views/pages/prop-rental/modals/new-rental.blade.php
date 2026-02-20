<div class="modal fade" id="newRentalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('prop-rental.rentals.store') }}" method="POST" id="newRentalForm">
                @csrf
                <input type="hidden" name="form_context" value="new_rental">
                <div class="modal-header">
                    <h5 class="modal-title">New Rental</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Customer <span class="text-danger">*</span></label>
                                <select class="form-select @error('customer_id') is-invalid @enderror" 
                                        name="customer_id" 
                                        id="rentalCustomer" 
                                        required>
                                    <option value="">Select Customer</option>
                                    @foreach($activeCustomers as $customer)
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
                                <select class="form-select @error('prop_id') is-invalid @enderror" 
                                        name="prop_id" 
                                        id="rentalProp" 
                                        required>
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
                                <input type="date" 
                                       class="form-control @error('start_date') is-invalid @enderror" 
                                       name="start_date" 
                                       id="rentalStartDate" 
                                       value="{{ old('start_date') }}" 
                                       required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('end_date') is-invalid @enderror" 
                                       name="end_date" 
                                       id="rentalEndDate" 
                                       value="{{ old('end_date') }}" 
                                       required>
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
                                <input type="number" 
                                       class="form-control" 
                                       id="dailyRate" 
                                       readonly 
                                       style="background-color: #e9ecef;"
                                       placeholder="0.00">
                                <small class="text-muted">Auto-filled based on selected prop</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total Amount (₦)</label>
                                <input type="number" 
                                       class="form-control fw-bold" 
                                       id="totalAmount" 
                                       readonly 
                                       style="background-color: #e9ecef; font-weight: 600;"
                                       placeholder="0.00">
                                <small class="text-muted">Calculated: Days × Daily Rate</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Security Deposit (₦) <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('security_deposit') is-invalid @enderror" 
                                       name="security_deposit" 
                                       value="{{ old('security_deposit', 0) }}" 
                                       required 
                                       min="0" 
                                       step="0.01">
                                @error('security_deposit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Amount Paid (₦)</label>
                                <input type="number" 
                                       class="form-control @error('amount_paid') is-invalid @enderror" 
                                       name="amount_paid" 
                                       value="{{ old('amount_paid', 0) }}" 
                                       min="0" 
                                       step="0.01"
                                       placeholder="0.00">
                                <small class="text-muted">Can be partial, balance due on return</small>
                                @error('amount_paid')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Balance Display (dynamically added by JavaScript) -->
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" 
                                  name="notes" 
                                  rows="3"
                                  placeholder="Any special instructions or notes...">{{ old('notes') }}</textarea>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input @error('agreement_signed') is-invalid @enderror" 
                               type="checkbox" 
                               name="agreement_signed" 
                               id="agreementSigned" 
                               value="1" 
                               {{ old('agreement_signed') ? 'checked' : '' }} 
                               required>
                        <label class="form-check-label" for="agreementSigned">
                            Rental agreement signed <span class="text-danger">*</span>
                        </label>
                        @error('agreement_signed')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-2"></i>Create Rental
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Ensure calculation functions are available when modal is used
document.addEventListener('DOMContentLoaded', function() {
    const rentalModal = document.getElementById('newRentalModal');
    
    if (rentalModal) {
        rentalModal.addEventListener('shown.bs.modal', function() {
            // Set minimum date to today
            const startDateInput = document.getElementById('rentalStartDate');
            const endDateInput = document.getElementById('rentalEndDate');
            
            if (startDateInput && !startDateInput.value) {
                const today = new Date().toISOString().split('T')[0];
                startDateInput.setAttribute('min', today);
            }
            
            // If prop is already selected (from quick action), calculate immediately
            const propSelect = document.getElementById('rentalProp');
            if (propSelect && propSelect.value) {
                const selectedOption = propSelect.options[propSelect.selectedIndex];
                const dailyRate = parseFloat(selectedOption.getAttribute('data-rate')) || 0;
                const dailyRateInput = document.getElementById('dailyRate');
                if (dailyRateInput) {
                    dailyRateInput.value = dailyRate.toFixed(2);
                }
            }
        });
    }
});
</script>
@endpush
