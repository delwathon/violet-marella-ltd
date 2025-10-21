/**
 * Enhanced Rental Amount Calculation with Payment Validation
 * Add this to prop-rental.js or include in new-rental.blade.php
 */

// Enhanced calculation function with better error handling
function calculateRentalAmount() {
    const startDate = document.getElementById('rentalStartDate')?.value;
    const endDate = document.getElementById('rentalEndDate')?.value;
    const propSelect = document.getElementById('rentalProp');
    const dailyRateInput = document.getElementById('dailyRate');
    const totalAmountInput = document.getElementById('totalAmount');
    const amountPaidInput = document.querySelector('input[name="amount_paid"]');
    
    // Reset if inputs are missing
    if (!startDate || !endDate || !propSelect?.value) {
        if (dailyRateInput) dailyRateInput.value = '';
        if (totalAmountInput) totalAmountInput.value = '';
        return;
    }
    
    // Calculate days
    const start = new Date(startDate);
    const end = new Date(endDate);
    const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
    
    // Validate date range
    if (days <= 0) {
        if (dailyRateInput) dailyRateInput.value = '';
        if (totalAmountInput) totalAmountInput.value = '';
        alert('End date must be after start date');
        return;
    }
    
    // Get daily rate from selected option
    const selectedOption = propSelect.options[propSelect.selectedIndex];
    const dailyRate = parseFloat(selectedOption.getAttribute('data-rate')) || 0;
    
    if (dailyRate <= 0) {
        if (dailyRateInput) dailyRateInput.value = '';
        if (totalAmountInput) totalAmountInput.value = '';
        return;
    }
    
    // Calculate total
    const totalAmount = days * dailyRate;
    
    // Update fields
    if (dailyRateInput) {
        dailyRateInput.value = dailyRate.toFixed(2);
    }
    
    if (totalAmountInput) {
        totalAmountInput.value = totalAmount.toFixed(2);
    }
    
    // Validate amount paid
    if (amountPaidInput) {
        validateAmountPaid(amountPaidInput, totalAmount);
    }
    
    // Show balance if any
    updateBalanceDisplay(totalAmount);
}

/**
 * Validate that amount paid doesn't exceed total
 */
function validateAmountPaid(input, totalAmount) {
    const amountPaid = parseFloat(input.value) || 0;
    
    if (amountPaid > totalAmount) {
        input.setCustomValidity('Amount paid cannot exceed total amount');
        input.classList.add('is-invalid');
        
        // Show error message
        let errorDiv = input.nextElementSibling;
        if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            input.parentNode.appendChild(errorDiv);
        }
        errorDiv.textContent = `Amount paid cannot exceed ₦${totalAmount.toLocaleString('en-NG', { minimumFractionDigits: 2 })}`;
    } else {
        input.setCustomValidity('');
        input.classList.remove('is-invalid');
        
        // Remove error message if exists
        const errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
            errorDiv.remove();
        }
    }
    
    // Update balance
    updateBalanceDisplay(totalAmount);
}

/**
 * Update balance display
 */
function updateBalanceDisplay(totalAmount) {
    const amountPaidInput = document.querySelector('input[name="amount_paid"]');
    const amountPaid = parseFloat(amountPaidInput?.value) || 0;
    const balance = totalAmount - amountPaid;
    
    // Find or create balance display
    let balanceDiv = document.getElementById('balanceDisplay');
    if (!balanceDiv) {
        balanceDiv = document.createElement('div');
        balanceDiv.id = 'balanceDisplay';
        balanceDiv.className = 'alert alert-info mt-2';
        
        // Insert after amount paid input
        if (amountPaidInput) {
            const parentCol = amountPaidInput.closest('.col-md-6');
            if (parentCol) {
                parentCol.appendChild(balanceDiv);
            }
        }
    }
    
    if (balance > 0) {
        balanceDiv.innerHTML = `
            <strong>Balance Due:</strong> ₦${balance.toLocaleString('en-NG', { minimumFractionDigits: 2 })}
            <br><small class="text-muted">Customer can pay this on return</small>
        `;
        balanceDiv.style.display = 'block';
    } else if (balance === 0) {
        balanceDiv.innerHTML = `
            <strong><i class="fas fa-check-circle me-2"></i>Fully Paid</strong>
        `;
        balanceDiv.classList.remove('alert-info');
        balanceDiv.classList.add('alert-success');
        balanceDiv.style.display = 'block';
    } else {
        balanceDiv.style.display = 'none';
    }
}

/**
 * Initialize rental form calculations
 */
function initializeRentalForm() {
    const startDateInput = document.getElementById('rentalStartDate');
    const endDateInput = document.getElementById('rentalEndDate');
    const propSelect = document.getElementById('rentalProp');
    const amountPaidInput = document.querySelector('input[name="amount_paid"]');
    
    // Bind calculation events
    if (startDateInput) {
        startDateInput.addEventListener('change', calculateRentalAmount);
    }
    
    if (endDateInput) {
        endDateInput.addEventListener('change', calculateRentalAmount);
    }
    
    if (propSelect) {
        propSelect.addEventListener('change', function() {
            calculateRentalAmount();
            // Auto-fill daily rate immediately when prop is selected
            const selectedOption = this.options[this.selectedIndex];
            const dailyRate = parseFloat(selectedOption.getAttribute('data-rate')) || 0;
            const dailyRateInput = document.getElementById('dailyRate');
            if (dailyRateInput && dailyRate > 0) {
                dailyRateInput.value = dailyRate.toFixed(2);
            }
        });
    }
    
    if (amountPaidInput) {
        amountPaidInput.addEventListener('input', function() {
            const totalAmountInput = document.getElementById('totalAmount');
            const totalAmount = parseFloat(totalAmountInput?.value) || 0;
            validateAmountPaid(this, totalAmount);
        });
        
        amountPaidInput.addEventListener('blur', function() {
            const totalAmountInput = document.getElementById('totalAmount');
            const totalAmount = parseFloat(totalAmountInput?.value) || 0;
            validateAmountPaid(this, totalAmount);
        });
    }
    
    // Set minimum date to today for start date
    if (startDateInput) {
        const today = new Date().toISOString().split('T')[0];
        startDateInput.setAttribute('min', today);
    }
    
    // Update end date minimum when start date changes
    if (startDateInput && endDateInput) {
        startDateInput.addEventListener('change', function() {
            const startDate = new Date(this.value);
            const nextDay = new Date(startDate);
            nextDay.setDate(nextDay.getDate() + 1);
            endDateInput.setAttribute('min', nextDay.toISOString().split('T')[0]);
        });
    }
}

/**
 * Pre-populate form when modal opens
 */
function setupModalEventListeners() {
    const rentalModal = document.getElementById('newRentalModal');
    
    if (rentalModal) {
        rentalModal.addEventListener('shown.bs.modal', function() {
            // Initialize form
            initializeRentalForm();
            
            // If prop is pre-selected, trigger calculation
            const propSelect = document.getElementById('rentalProp');
            if (propSelect && propSelect.value) {
                calculateRentalAmount();
            }
        });
        
        // Reset form when modal closes
        rentalModal.addEventListener('hidden.bs.modal', function() {
            const form = this.querySelector('form');
            if (form) {
                form.reset();
                
                // Clear calculated fields
                const dailyRateInput = document.getElementById('dailyRate');
                const totalAmountInput = document.getElementById('totalAmount');
                if (dailyRateInput) dailyRateInput.value = '';
                if (totalAmountInput) totalAmountInput.value = '';
                
                // Remove balance display
                const balanceDiv = document.getElementById('balanceDisplay');
                if (balanceDiv) balanceDiv.remove();
                
                // Remove validation errors
                const invalidInputs = form.querySelectorAll('.is-invalid');
                invalidInputs.forEach(input => {
                    input.classList.remove('is-invalid');
                    input.setCustomValidity('');
                });
            }
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    setupModalEventListeners();
    initializeRentalForm();
});

// Export for use in other scripts
if (typeof window.PropRentalFunctions !== 'undefined') {
    window.PropRentalFunctions.calculateRentalAmount = calculateRentalAmount;
    window.PropRentalFunctions.validateAmountPaid = validateAmountPaid;
}