/**
 * VIOLET MARELLA LIMITED - PHOTO STUDIO FUNCTIONALITY
 * Photo studio management JavaScript functionality
 */

let selectedStudioId = null;
let refreshInterval = null;

/**
 * Initialize Photo Studio
 */
document.addEventListener('DOMContentLoaded', function() {
    initializePhotoStudio();
});

function initializePhotoStudio() {
    console.log('Initializing photo studio...');
    
    // Initialize check-in form
    initializeCheckInForm();
    
    // Initialize billing calculator
    initializeBillingCalculator();
    
    // Start auto-refresh for active sessions
    startAutoRefresh();
    
    // Initialize checkout functionality
    initializeCheckoutModal();
    
    // Initialize check-in modal functionality
    initializeCheckInModal();
    
    // Initialize extend session modal
    initializeExtendSessionModal();
    
    // Initialize tooltips if present
    initializeTooltips();
    
    console.log('Photo studio initialized successfully');
}

/**
 * Initialize Tooltips
 */
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Initialize Check-in Form
 */
function initializeCheckInForm() {
    const checkInForm = document.getElementById('checkInForm');
    if (!checkInForm) return;
    
    checkInForm.addEventListener('submit', function(e) {
        e.preventDefault();
        handleCheckIn();
    });
}

/**
 * Handle Check-in
 */
function handleCheckIn() {
    const form = document.getElementById('checkInForm');
    const formData = new FormData(form);
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Checking in...';
    
    fetch('/app/photo-studio/check-in', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification(data.message, 'success');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('checkInModal'));
            if (modal) {
                modal.hide();
            }
            
            // Reset form
            form.reset();
            
            // Refresh page data
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'Check-in failed', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred during check-in', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

/**
 * Select Studio
 */
function selectStudio(studioId) {
    selectedStudioId = studioId;
    const studioSelect = document.getElementById('studioSelect');
    if (studioSelect) {
        studioSelect.value = studioId;
        // Trigger calculation after setting the value
        calculateEstimatedCost();
    }
}

/**
 * View Session
 */
function viewSession(sessionId) {
    fetch(`/app/photo-studio/session/${sessionId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const session = data.session;
            const duration = data.formattedDuration;
            const overtime = data.isOvertime ? ' (OVERTIME)' : '';
            
            const details = `
Customer: ${session.customer.name}
Phone: ${session.customer.phone}
Email: ${session.customer.email || 'N/A'}
Studio: ${session.studio.name}
Check-in: ${new Date(session.check_in_time).toLocaleString()}
Duration: ${duration}${overtime}
Expected: ${session.expected_duration} minutes
QR Code: ${session.qr_code}
Status: ${session.status.toUpperCase()}
            `.trim();
            
            alert(details);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to load session details', 'error');
    });
}

/**
 * Show QR Code
 */
function showQRCode(sessionId) {
    fetch(`/app/photo-studio/session/${sessionId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const session = data.session;
            const qrInfo = `
QR Code for ${session.customer.name}

Code: ${session.qr_code}
Studio: ${session.studio.name}
Check-in: ${new Date(session.check_in_time).toLocaleTimeString()}

Scan this code for quick checkout
            `.trim();
            
            alert(qrInfo);
            showNotification('QR code displayed', 'info');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to load QR code', 'error');
    });
}

/**
 * Extend Session
 */
function extendSession(sessionId) {
    const extension = prompt('Enter additional time (minutes):', '30');
    
    if (extension && !isNaN(extension) && parseInt(extension) > 0) {
        fetch(`/app/photo-studio/extend/${sessionId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                additional_time: parseInt(extension)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                refreshActiveSessions();
            } else {
                showNotification(data.message || 'Failed to extend session', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while extending session', 'error');
        });
    }
}

/**
 * Initialize Billing Calculator
 */
function initializeBillingCalculator() {
    const baseTimeInput = document.getElementById('baseTime');
    const baseAmountInput = document.getElementById('baseAmount');
    const totalTimeInput = document.getElementById('totalTime');
    
    if (totalTimeInput) {
        totalTimeInput.addEventListener('input', calculateBilling);
    }
    
    if (baseTimeInput) {
        baseTimeInput.addEventListener('input', calculateBilling);
    }
    
    if (baseAmountInput) {
        baseAmountInput.addEventListener('input', calculateBilling);
    }
}

/**
 * Calculate Billing
 */
function calculateBilling() {
    const baseTime = parseInt(document.getElementById('baseTime').value) || 30;
    const baseAmount = parseFloat(document.getElementById('baseAmount').value) || 2000;
    const totalTime = parseInt(document.getElementById('totalTime').value) || 0;
    
    if (totalTime === 0) {
        document.getElementById('totalAmount').textContent = '₦0';
        document.getElementById('calculationBreakdown').style.display = 'none';
        return;
    }
    
    let totalBill = 0;
    let extraTime = 0;
    let extraFee = 0;
    
    if (totalTime <= baseTime) {
        totalBill = baseAmount;
    } else {
        extraTime = totalTime - baseTime;
        const perMinuteRate = baseAmount / baseTime;
        extraFee = extraTime * perMinuteRate;
        totalBill = baseAmount + extraFee;
    }
    
    // Update display
    document.getElementById('totalAmount').textContent = '₦' + formatCurrency(totalBill);
    document.getElementById('breakdownBaseTime').textContent = baseTime + ' minutes';
    document.getElementById('breakdownBaseAmount').textContent = '₦' + formatCurrency(baseAmount);
    document.getElementById('breakdownExtraTime').textContent = extraTime + ' minutes';
    document.getElementById('breakdownExtraFee').textContent = '₦' + formatCurrency(extraFee);
    document.getElementById('calculationBreakdown').style.display = 'block';
    
    // Visual feedback
    const totalAmountEl = document.getElementById('totalAmount');
    if (totalTime > baseTime) {
        totalAmountEl.classList.add('text-warning');
        totalAmountEl.classList.remove('text-primary');
    } else {
        totalAmountEl.classList.add('text-primary');
        totalAmountEl.classList.remove('text-warning');
    }
}

/**
 * Format Currency
 */
function formatCurrency(amount) {
    const num = Number(amount);
    if (isNaN(num)) return '0.00';
    return num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

/**
 * Start Auto Refresh
 */
function startAutoRefresh() {
    // Refresh active sessions every 30 seconds
    refreshInterval = setInterval(() => {
        refreshActiveSessions();
    }, 30000);
}

/**
 * Refresh Active Sessions
 */
function refreshActiveSessions() {
    fetch('/app/photo-studio/active-sessions', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateActiveSessionsDisplay(data.sessions);
        }
    })
    .catch(error => {
        console.error('Error refreshing sessions:', error);
    });
}

/**
 * Update Active Sessions Display
 */
function updateActiveSessionsDisplay(sessions) {
    const container = document.getElementById('activeSessionsContainer');
    if (!container) return;
    
    if (sessions.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                <div>No active sessions</div>
                <small class="text-muted">Check in customers to see active sessions here</small>
            </div>
        `;
        return;
    }
    
    let html = '';
    sessions.forEach(session => {
        const duration = calculateDuration(session.check_in_time);
        const durationClass = getDurationClass(duration.minutes, session.expected_duration);
        
        html += `
            <div class="session-item" data-session-id="${session.id}">
                <div class="row align-items-center">
                    <div class="col-md-3 mb-2 mb-md-0">
                        <div class="customer-info">
                            <div class="customer-avatar">${getInitials(session.customer.name)}</div>
                            <div>
                                <div class="fw-semibold">${session.customer.name}</div>
                                <small class="text-muted">${session.customer.phone}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <div class="studio-badge">${session.studio.name}</div>
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <div class="time-info">
                            <div class="check-in-time">${formatTime(session.check_in_time)}</div>
                            <small class="text-muted">Check-in</small>
                        </div>
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <div class="duration-badge ${durationClass}">${duration.text}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="session-actions">
                            <button class="btn btn-sm btn-outline-info" onclick="showQRCode(${session.id})" title="Show QR Code">
                                <i class="fas fa-qrcode"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary" onclick="extendSession(${session.id})" title="Extend Session">
                                <i class="fas fa-clock"></i>
                            </button>
                            <button class="btn btn-sm btn-success" onclick="showCheckoutModal(${session.id})" title="Checkout">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

/**
 * Calculate Duration
 */
function calculateDuration(checkInTime) {
    const start = new Date(checkInTime);
    const now = new Date();
    const diffMs = now - start;
    const minutes = Math.floor(diffMs / 60000);
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    
    let text = mins + 'm';
    if (hours > 0) {
        text = hours + 'h ' + mins + 'm';
    }
    
    return { minutes, hours, mins, text };
}

/**
 * Get Duration Class
 */
function getDurationClass(currentMinutes, expectedDuration) {
    if (currentMinutes > expectedDuration + 30) return 'overtime';
    if (currentMinutes > expectedDuration) return 'warning';
    return 'active';
}

/**
 * Get Initials
 */
function getInitials(name) {
    return name.split(' ')
        .map(word => word.charAt(0).toUpperCase())
        .slice(0, 2)
        .join('');
}

/**
 * Format Time
 */
function formatTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', { 
        hour: 'numeric', 
        minute: '2-digit',
        hour12: true 
    });
}

/**
 * Show Notification
 */
function showNotification(message, type = 'info') {
    // Map type to Bootstrap alert class
    const alertType = type === 'error' ? 'danger' : type;
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${alertType} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 150);
    }, 5000);
}

/**
 * Export Sessions
 */
function exportSessions() {
    const confirmExport = confirm('Export all active sessions to CSV?');
    if (!confirmExport) return;
    
    showNotification('Preparing export...', 'info');
    
    window.location.href = '/app/photo-studio/sessions/export/csv';
}

/**
 * Cleanup on page unload
 */
window.addEventListener('beforeunload', () => {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});

// ============================================================================
// CHECK-IN MODAL FUNCTIONALITY - FIXED VERSION
// ============================================================================

/**
 * Initialize Check-in Modal - FIXED
 */
function initializeCheckInModal() {
    // Customer Search
    const searchInput = document.getElementById('customerSearch');
    const searchBtn = document.getElementById('searchCustomerBtn');
    const resultsDiv = document.getElementById('customerSearchResults');
    
    if (searchBtn) {
        searchBtn.addEventListener('click', performCustomerSearch);
    }
    
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                performCustomerSearch();
            }
        });
    }
    
    // Studio selection and cost calculation
    const studioSelect = document.getElementById('studioSelect');
    const durationSelect = document.getElementById('expectedDuration');
    const customDurationDiv = document.getElementById('customDurationDiv');
    const customDurationInput = document.getElementById('customDuration');
    
    // Listen for studio change
    if (studioSelect) {
        studioSelect.addEventListener('change', calculateEstimatedCost);
    }
    
    // Listen for duration change
    if (durationSelect) {
        durationSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                customDurationDiv.style.display = 'block';
                customDurationInput.required = true;
            } else {
                customDurationDiv.style.display = 'none';
                customDurationInput.required = false;
            }
            calculateEstimatedCost();
        });
    }
    
    if (customDurationInput) {
        customDurationInput.addEventListener('input', calculateEstimatedCost);
    }
    
    // FIXED: Listen for modal show event and use setTimeout to ensure DOM is ready
    const checkInModal = document.getElementById('checkInModal');
    if (checkInModal) {
        checkInModal.addEventListener('shown.bs.modal', function() {
            // Use setTimeout to ensure the modal is fully rendered
            setTimeout(() => {
                const studioSelectInModal = document.getElementById('studioSelect');
                // Trigger calculation if a studio is already selected
                if (studioSelectInModal && studioSelectInModal.value) {
                    calculateEstimatedCost();
                }
            }, 100);
        });
    }
}

/**
 * Perform Customer Search
 */
function performCustomerSearch() {
    const searchInput = document.getElementById('customerSearch');
    const resultsDiv = document.getElementById('customerSearchResults');
    const term = searchInput.value.trim();
    
    if (term.length < 2) return;
    
    fetch(`/app/photo-studio/customers/search?term=${encodeURIComponent(term)}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.customers.length > 0) {
            resultsDiv.innerHTML = data.customers.map(customer => `
                <a href="#" class="list-group-item list-group-item-action" onclick="selectCustomer(${customer.id}, '${customer.name}', '${customer.phone}', '${customer.email || ''}'); return false;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${customer.name}</strong>
                            <br>
                            <small class="text-muted">${customer.phone}</small>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>
            `).join('');
            resultsDiv.style.display = 'block';
        } else {
            resultsDiv.innerHTML = '<div class="list-group-item text-muted">No customers found</div>';
            resultsDiv.style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Search error:', error);
    });
}

/**
 * Select Customer
 */
function selectCustomer(id, name, phone, email) {
    document.getElementById('customerName').value = name;
    document.getElementById('customerPhone').value = phone;
    document.getElementById('customerEmail').value = email;
    document.getElementById('customerSearchResults').style.display = 'none';
    document.getElementById('customerSearch').value = '';
}

/**
 * Calculate Estimated Cost - FIXED VERSION
 */
function calculateEstimatedCost() {
    const studioSelect = document.getElementById('studioSelect');
    const durationSelect = document.getElementById('expectedDuration');
    const customDurationInput = document.getElementById('customDuration');
    const estimatedCostEl = document.getElementById('estimatedCost');
    const rateInfoEl = document.getElementById('rateInfo');
    
    if (!studioSelect || !estimatedCostEl || !rateInfoEl) return;
    
    const selectedOption = studioSelect.options[studioSelect.selectedIndex];
    
    if (!selectedOption || !selectedOption.value) {
        estimatedCostEl.textContent = '₦0.00';
        rateInfoEl.textContent = 'Select a studio to see pricing';
        return;
    }
    
    // Get studio pricing data from data attributes
    const baseTime = parseInt(selectedOption.dataset.baseTime) || 30;
    const baseAmount = parseFloat(selectedOption.dataset.baseAmount) || 2000;
    
    // Get selected duration
    const duration = durationSelect.value === 'custom' ? 
        parseInt(customDurationInput.value) || 0 : 
        parseInt(durationSelect.value) || 0;
    
    let estimatedCost = 0;
    
    if (duration > 0) {
        if (duration <= baseTime) {
            estimatedCost = baseAmount;
        } else {
            const extraTime = duration - baseTime;
            const perMinuteRate = baseAmount / baseTime;
            const extraCharge = extraTime * perMinuteRate;
            estimatedCost = baseAmount + extraCharge;
        }
    }
    
    // Update display
    estimatedCostEl.textContent = '₦' + estimatedCost.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    
    // Update rate info
    const perMinuteRate = baseAmount / baseTime;
    const hourlyRate = perMinuteRate * 60;
    rateInfoEl.textContent = `Base rate: ₦${baseAmount.toLocaleString()} for ${baseTime} minutes (₦${hourlyRate.toLocaleString()}/hour)`;
}

// ============================================================================
// CHECKOUT MODAL FUNCTIONALITY - FIXED WITH EXTRA AMOUNT
// ============================================================================

/**
 * Initialize Checkout Modal
 */
function initializeCheckoutModal() {
    const discountInput = document.getElementById('discountAmount');
    const checkoutForm = document.getElementById('checkoutForm');
    
    if (discountInput) {
        discountInput.addEventListener('input', function() {
            updateCheckoutFinalAmount();
        });
    }

    // Handle checkout form submission
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const sessionId = document.getElementById('checkoutSessionId').value;
            const formData = new FormData(this);
            
            // Disable submit button
            const submitBtn = document.getElementById('checkoutSubmitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            
            fetch(`/app/photo-studio/checkout/${sessionId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || formData.get('_token')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showNotification(data.message, 'success');
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('checkoutModal'));
                    modal.hide();
                    
                    // Reload page or update UI
                    window.location.reload();
                } else {
                    showNotification(data.message || 'Checkout failed', 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Complete Checkout';
                }
            })
            .catch(error => {
                showNotification('Checkout failed: ' + error.message, 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Complete Checkout';
            });
        });
    }
}

/**
 * Show Checkout Modal - FIXED WITH PROPER EXTRA AMOUNT CALCULATION
 */
function showCheckoutModal(sessionId) {
    // Reset form
    document.getElementById('checkoutForm').reset();
    document.getElementById('checkoutSessionId').value = sessionId;
    
    // Show loading state
    document.getElementById('checkoutLoading').style.display = 'block';
    document.getElementById('checkoutDetails').style.display = 'none';
    document.getElementById('checkoutPaymentSection').style.display = 'none';
    document.getElementById('checkoutSubmitBtn').disabled = true;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('checkoutModal'));
    modal.show();
    
    // Fetch session details
    fetch(`/app/photo-studio/session/${sessionId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        
        if (data.success && data.session) {
            const session = data.session;
            
            // Get base amount and calculate extra amount properly
            const baseAmount = parseFloat(session.base_amount || 0);
            const totalAmount = parseFloat(session.total_amount || 0);
            const extraAmount = Math.max(0, totalAmount - baseAmount);
            
            // Populate session details
            document.getElementById('checkoutCustomerName').textContent = session.customer?.name || 'N/A';
            document.getElementById('checkoutCustomerPhone').textContent = session.customer?.phone || 'N/A';
            document.getElementById('checkoutStudioName').textContent = session.studio?.name || 'N/A';
            document.getElementById('checkoutCheckInTime').textContent = formatCheckoutDateTime(session.check_in_time);
            document.getElementById('checkoutDuration').textContent = data.formattedDuration || `${data.currentDuration} minutes`;
            document.getElementById('checkoutBaseAmount').textContent = '₦' + baseAmount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            
            // Display extra amount (overtime charge)
            document.getElementById('checkoutOvertimeCharge').textContent = '₦' + extraAmount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            
            // Set total amount
            document.getElementById('checkoutTotalAmount').textContent = '₦' + totalAmount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById('checkoutDetails').setAttribute('data-total-amount', totalAmount);
            
            // Hide loading, show details
            document.getElementById('checkoutLoading').style.display = 'none';
            document.getElementById('checkoutDetails').style.display = 'block';
            document.getElementById('checkoutPaymentSection').style.display = 'block';
            document.getElementById('checkoutSubmitBtn').disabled = false;
            
            // Update final amount
            updateCheckoutFinalAmount();
        } else {
            console.error('Invalid response:', data);
            showNotification(data.message || 'Failed to load session details', 'error');
            modal.hide();
        }
    })
    .catch(error => {
        console.error('Error loading session:', error);
        showNotification('Failed to load session: ' + error.message, 'error');
        modal.hide();
    });
}

/**
 * Update Checkout Final Amount
 */
function updateCheckoutFinalAmount() {
    const totalAmount = parseFloat(document.getElementById('checkoutDetails')?.dataset.totalAmount || 0);
    const discount = parseFloat(document.getElementById('discountAmount')?.value || 0);
    const finalAmount = Math.max(0, totalAmount - discount);
    
    const finalAmountEl = document.getElementById('finalAmount');
    if (finalAmountEl) {
        finalAmountEl.textContent = '₦' + finalAmount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        
        if (discount > totalAmount) {
            finalAmountEl.classList.add('text-danger');
            finalAmountEl.classList.remove('text-success');
        } else {
            finalAmountEl.classList.add('text-success');
            finalAmountEl.classList.remove('text-danger');
        }
    }
}

/**
 * Format Checkout DateTime
 */
function formatCheckoutDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
}

// ============================================================================
// EXTEND SESSION MODAL FUNCTIONALITY
// ============================================================================

/**
 * Initialize Extend Session Modal
 */
function initializeExtendSessionModal() {
    const additionalTimeSelect = document.getElementById('additionalTime');
    if (additionalTimeSelect) {
        additionalTimeSelect.addEventListener('change', function() {
            const sessionId = document.getElementById('extendSessionId').value;
            if (sessionId) {
                fetch(`/app/photo-studio/session/${sessionId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateNewExpectedDuration(data.session.expected_duration);
                    }
                });
            }
        });
    }

    const extendForm = document.getElementById('extendSessionForm');
    if (extendForm) {
        extendForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const sessionId = document.getElementById('extendSessionId').value;
            const additionalTime = document.getElementById('additionalTime').value;
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Extending...';
            
            fetch(`/app/photo-studio/extend/${sessionId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    additional_time: parseInt(additionalTime)
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    
                    const modal = bootstrap.Modal.getInstance(document.getElementById('extendSessionModal'));
                    if (modal) {
                        modal.hide();
                    }
                    
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.message || 'Failed to extend session', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }
}

/**
 * Show Extend Modal
 */
function showExtendModal(sessionId) {
    document.getElementById('extendSessionId').value = sessionId;
    
    fetch(`/app/photo-studio/session/${sessionId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const session = data.session;
            document.getElementById('extendSessionDetails').innerHTML = `
                <div class="d-flex align-items-center mb-2">
                    <div class="customer-avatar me-2">${getInitials(session.customer.name)}</div>
                    <div>
                        <strong>${session.customer.name}</strong><br>
                        <small class="text-muted">${session.studio.name}</small>
                    </div>
                </div>
                <div class="alert alert-light">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Current Duration:</small><br>
                            <strong>${data.formattedDuration}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Expected:</small><br>
                            <strong>${session.expected_duration} minutes</strong>
                        </div>
                    </div>
                </div>
            `;
            
            updateNewExpectedDuration(session.expected_duration);
            
            const modal = new bootstrap.Modal(document.getElementById('extendSessionModal'));
            modal.show();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to load session details', 'error');
    });
}

/**
 * Update New Expected Duration
 */
function updateNewExpectedDuration(currentExpected) {
    const additionalTime = parseInt(document.getElementById('additionalTime').value) || 0;
    const newExpected = currentExpected + additionalTime;
    document.getElementById('newExpectedDuration').textContent = newExpected + ' minutes';
}

// ============================================================================
// QR CODE MODAL FUNCTIONALITY
// ============================================================================

let currentQRCode = null;
let currentSessionId = null;

/**
 * Show QR Code Modal
 */
function showQRCodeModal(sessionId) {
    currentSessionId = sessionId;
    
    fetch(`/app/photo-studio/session/${sessionId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const session = data.session;
            
            document.getElementById('qrCodeDetails').innerHTML = `
                <div class="mb-3">
                    <h6>${session.customer.name}</h6>
                    <p class="text-muted mb-1">${session.studio.name}</p>
                    <small class="text-muted">Session: ${session.session_code}</small>
                </div>
            `;
            
            // Clear previous QR code
            document.getElementById('qrCodeImage').innerHTML = '';
            
            // Generate new QR code
            currentQRCode = new QRCode(document.getElementById('qrCodeImage'), {
                text: session.qr_code,
                width: 256,
                height: 256,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
            
            const modal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
            modal.show();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to load QR code', 'error');
    });
}

/**
 * Print QR Code
 */
function printQRCode() {
    const printWindow = window.open('', '', 'height=600,width=800');
    const qrImage = document.querySelector('#qrCodeImage img');
    const sessionDetails = document.getElementById('qrCodeDetails').innerHTML;
    
    printWindow.document.write('<html><head><title>Print QR Code</title>');
    printWindow.document.write('<style>body{text-align:center;font-family:Arial,sans-serif;}img{margin:20px;}</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(sessionDetails);
    printWindow.document.write('<img src="' + qrImage.src + '" />');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

/**
 * Download QR Code
 */
function downloadQRCode() {
    const qrImage = document.querySelector('#qrCodeImage img');
    if (qrImage) {
        const link = document.createElement('a');
        link.download = `qr-session-${currentSessionId}.png`;
        link.href = qrImage.src;
        link.click();
    }
}

/**
 * Open QR Scanner
 */
function openQRScanner() {
    const modal = new bootstrap.Modal(document.getElementById('qrScannerModal'));
    modal.show();
}

/**
 * Search By QR Code
 */
function searchByQRCode() {
    const qrCode = document.getElementById('manualQRInput').value.trim();
    if (!qrCode) {
        showNotification('Please enter a QR code', 'warning');
        return;
    }
    
    fetch('/app/photo-studio/scan-qr', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ qr_code: qrCode })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const session = data.session;
            document.getElementById('scanResult').innerHTML = `
                <div class="alert alert-success">
                    <h6 class="mb-2">Session Found!</h6>
                    <div class="mb-2">
                        <strong>Customer:</strong> ${session.customer.name}<br>
                        <strong>Studio:</strong> ${session.studio.name}<br>
                        <strong>Status:</strong> ${session.status}
                    </div>
                    ${data.can_checkout ? `
                        <button class="btn btn-success btn-sm" onclick="showCheckoutModal(${session.id}); bootstrap.Modal.getInstance(document.getElementById('qrScannerModal')).hide();">
                            <i class="fas fa-sign-out-alt me-1"></i>Proceed to Checkout
                        </button>
                    ` : `
                        <span class="badge bg-warning">Cannot checkout - session not active</span>
                    `}
                </div>
            `;
            document.getElementById('scanResult').style.display = 'block';
        } else {
            showNotification(data.message || 'Invalid QR code', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while scanning', 'error');
    });
}

/**
 * Initialize QR Scanner Button
 */
document.addEventListener('DOMContentLoaded', function() {
    const startScanBtn = document.getElementById('startScanBtn');
    if (startScanBtn) {
        startScanBtn.addEventListener('click', function() {
            showNotification('Camera scanning feature requires additional setup', 'info');
            // Implement with html5-qrcode library in production
        });
    }
});

// ============================================================================
// SESSION DETAILS MODAL FUNCTIONALITY
// ============================================================================

/**
 * View Session Details
 */
function viewSessionDetails(sessionId) {
    const modal = new bootstrap.Modal(document.getElementById('sessionDetailsModal'));
    modal.show();
    
    fetch(`/app/photo-studio/session/${sessionId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const session = data.session;
            const statusClass = session.status === 'active' ? 'success' : session.status === 'completed' ? 'primary' : 'secondary';
            const paymentClass = session.payment_status === 'paid' ? 'success' : session.payment_status === 'pending' ? 'warning' : 'secondary';
            
            document.getElementById('sessionDetailsBody').innerHTML = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Customer Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="customer-avatar me-3">${getInitials(session.customer.name)}</div>
                                    <div>
                                        <h6 class="mb-0">${session.customer.name}</h6>
                                        <small class="text-muted">Customer ID: ${session.customer.id}</small>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <i class="fas fa-phone text-muted me-2"></i>
                                    <strong>Phone:</strong> ${session.customer.phone}
                                </div>
                                ${session.customer.email ? `
                                <div class="mb-2">
                                    <i class="fas fa-envelope text-muted me-2"></i>
                                    <strong>Email:</strong> ${session.customer.email}
                                </div>
                                ` : ''}
                                <div class="mb-2">
                                    <i class="fas fa-history text-muted me-2"></i>
                                    <strong>Total Sessions:</strong> ${session.customer.total_sessions}
                                </div>
                                <div>
                                    <i class="fas fa-money-bill text-muted me-2"></i>
                                    <strong>Total Spent:</strong> ₦${formatCurrency(session.customer.total_spent)}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Session Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <i class="fas fa-barcode text-muted me-2"></i>
                                    <strong>Session Code:</strong> ${session.session_code}
                                </div>
                                <div class="mb-2">
                                    <i class="fas fa-door-open text-muted me-2"></i>
                                    <strong>Studio:</strong> ${session.studio.name}
                                </div>
                                <div class="mb-2">
                                    <i class="fas fa-clock text-muted me-2"></i>
                                    <strong>Check-in:</strong> ${new Date(session.check_in_time).toLocaleString()}
                                </div>
                                ${session.check_out_time ? `
                                <div class="mb-2">
                                    <i class="fas fa-sign-out-alt text-muted me-2"></i>
                                    <strong>Check-out:</strong> ${new Date(session.check_out_time).toLocaleString()}
                                </div>
                                ` : ''}
                                <div class="mb-2">
                                    <i class="fas fa-hourglass-half text-muted me-2"></i>
                                    <strong>Duration:</strong> ${data.formattedDuration} 
                                    ${data.isOvertime ? '<span class="badge bg-danger ms-1">Overtime</span>' : ''}
                                </div>
                                <div class="mb-2">
                                    <i class="fas fa-calendar-check text-muted me-2"></i>
                                    <strong>Expected:</strong> ${session.expected_duration} minutes
                                </div>
                                <div>
                                    <i class="fas fa-tag text-muted me-2"></i>
                                    <strong>Status:</strong> <span class="badge bg-${statusClass}">${session.status.toUpperCase()}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Payment Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <div class="text-muted small">Base Amount</div>
                                        <h5 class="mb-0">₦${formatCurrency(session.base_amount)}</h5>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted small">Extra Amount</div>
                                        <h5 class="mb-0">₦${formatCurrency(session.extra_amount)}</h5>
                                    </div>
                                </div>
                                ${session.discount_amount > 0 ? `
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="text-muted small">Discount</div>
                                        <h5 class="mb-0 text-danger">-₦${formatCurrency(session.discount_amount)}</h5>
                                    </div>
                                </div>
                                ` : ''}
                                <hr>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <div class="text-muted small">Total Amount</div>
                                        <h4 class="mb-0 text-success">₦${formatCurrency(session.total_amount)}</h4>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted small">Payment Status</div>
                                        <h5 class="mb-0">
                                            <span class="badge bg-${paymentClass}">${session.payment_status.toUpperCase()}</span>
                                        </h5>
                                    </div>
                                </div>
                                ${session.payment_method ? `
                                <div class="row">
                                    <div class="col-12">
                                        <div class="text-muted small">Payment Method</div>
                                        <div><strong>${session.payment_method.toUpperCase()}</strong></div>
                                    </div>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>

                ${session.notes ? `
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Notes</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">${session.notes}</p>
                            </div>
                        </div>
                    </div>
                </div>
                ` : ''}

                ${session.payments && session.payments.length > 0 ? `
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Payment History</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Method</th>
                                                <th>Reference</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${session.payments.map(payment => `
                                                <tr>
                                                    <td>${new Date(payment.payment_date).toLocaleString()}</td>
                                                    <td>₦${formatCurrency(payment.amount)}</td>
                                                    <td>${payment.payment_method.toUpperCase()}</td>
                                                    <td><small>${payment.reference}</small></td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                ` : ''}
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('sessionDetailsBody').innerHTML = `
            <div class="alert alert-danger">
                Failed to load session details
            </div>
        `;
    });
}

// ============================================================================
// CUSTOMER MANAGEMENT FUNCTIONS
// ============================================================================

/**
 * Edit Customer
 */
function editCustomer(customerId) {
    fetch(`/app/photo-studio/customers/${customerId}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const customer = data.customer;
            document.getElementById('editCustomerForm').action = `/app/photo-studio/customers/${customerId}`;
            document.getElementById('editCustomerBody').innerHTML = `
                <div class="mb-3">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="${customer.name}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control" name="phone" value="${customer.phone}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="email" value="${customer.email || ''}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address" rows="2">${customer.address || ''}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" name="date_of_birth" value="${customer.date_of_birth || ''}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" name="notes" rows="2">${customer.notes || ''}</textarea>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" ${customer.is_active ? 'checked' : ''}>
                    <label class="form-check-label">Active Customer</label>
                </div>
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('editCustomerModal'));
            modal.show();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to load customer details', 'error');
    });
}

/**
 * Delete Customer
 */
function deleteCustomer(customerId) {
    if (!confirm('Are you sure you want to delete this customer? This action cannot be undone.')) {
        return;
    }
    
    fetch(`/app/photo-studio/customers/${customerId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Failed to delete customer', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

/**
 * Export Customers
 */
function exportCustomers() {
    window.location.href = '/app/photo-studio/customers/export';
}

/**
 * Select Customer For Check-in
 */
function selectCustomerForCheckIn(customerId) {
    // This function can be used to pre-populate check-in form with customer data
    fetch(`/app/photo-studio/customers/${customerId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const customer = data.customer;
            document.getElementById('customerName').value = customer.name;
            document.getElementById('customerPhone').value = customer.phone;
            document.getElementById('customerEmail').value = customer.email || '';
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// ============================================================================
// STUDIO RATE MANAGEMENT FUNCTIONS
// ============================================================================

/**
 * Edit Rate
 */
function editRate(rateId) {
    fetch(`/app/photo-studio/rates/${rateId}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const rate = data.rate;
            document.getElementById('editRateForm').action = `/app/photo-studio/rates/${rateId}`;
            document.getElementById('editRateBody').innerHTML = `
                <div class="mb-3">
                    <label class="form-label">Rate Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="${rate.name}" required>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Base Time (minutes) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="base_time" value="${rate.base_time}" required min="1">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Base Amount (₦) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="base_amount" value="${rate.base_amount}" required min="0" step="0.01">
                        </div>
                    </div>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_default" value="1" ${rate.is_default ? 'checked' : ''}>
                    <label class="form-check-label">Set as default rate</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" ${rate.is_active ? 'checked' : ''}>
                    <label class="form-check-label">Rate is active</label>
                </div>
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('editRateModal'));
            modal.show();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to load rate details', 'error');
    });
}

/**
 * Set Default Rate
 */
function setDefaultRate(rateId) {
    if (!confirm('Set this as the default rate?')) return;
    
    fetch(`/app/photo-studio/rates/${rateId}/set-default`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Failed to set default rate', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

/**
 * Delete Rate
 */
function deleteRate(rateId) {
    if (!confirm('Are you sure you want to delete this rate?')) return;
    
    fetch(`/app/photo-studio/rates/${rateId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Failed to delete rate', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

// ============================================================================
// STUDIO MANAGEMENT FUNCTIONS
// ============================================================================

/**
 * Add Equipment Field
 */
function addEquipmentField() {
    const container = document.getElementById('equipmentList');
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <input type="text" class="form-control" name="equipment[]" placeholder="Enter equipment name">
        <button class="btn btn-outline-danger" type="button" onclick="removeEquipment(this)">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(div);
}

/**
 * Remove Equipment
 */
function removeEquipment(button) {
    button.closest('.input-group').remove();
}

/**
 * Edit Studio
 */
function editStudio(studioId) {
    fetch(`/app/photo-studio/studios/${studioId}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const studio = data.studio;
            document.getElementById('editStudioForm').action = `/app/photo-studio/studios/${studioId}`;
            
            // Get available rates
            fetch('/app/photo-studio/rates-list', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(ratesData => {
                let rateOptions = '<option value="">Select Rate Plan</option>';
                ratesData.rates.forEach(rate => {
                    const selected = studio.studio_rate_id === rate.id ? 'selected' : '';
                    const defaultLabel = rate.is_default ? ' (Default)' : '';
                    rateOptions += `<option value="${rate.id}" ${selected}>${rate.name} - ₦${parseFloat(rate.base_amount).toFixed(2)} for ${rate.base_time}min${defaultLabel}</option>`;
                });
            
                let equipmentHtml = '';
                if (studio.equipment && studio.equipment.length > 0) {
                    studio.equipment.forEach(eq => {
                        equipmentHtml += `
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="equipment[]" value="${eq}">
                                <button class="btn btn-outline-danger" type="button" onclick="removeEquipment(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                    });
                } else {
                    equipmentHtml = `
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" name="equipment[]" placeholder="Enter equipment name">
                            <button class="btn btn-outline-danger" type="button" onclick="removeEquipment(this)" disabled>
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                }
                
                document.getElementById('editStudioBody').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Studio Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" value="${studio.name}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Studio Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="code" value="${studio.code}" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2">${studio.description || ''}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Rate Plan <span class="text-danger">*</span></label>
                                <select class="form-select" name="studio_rate_id" required>
                                    ${rateOptions}
                                </select>
                                <small class="text-muted">Select the pricing plan for this studio</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Capacity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="capacity" value="${studio.capacity}" required min="1">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Equipment</label>
                        <div id="equipmentList">
                            ${equipmentHtml}
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addEquipmentField()">
                            <i class="fas fa-plus me-1"></i>Add Equipment
                        </button>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" ${studio.is_active ? 'checked' : ''}>
                        <label class="form-check-label">Studio is active</label>
                    </div>
                `;
                
                const modal = new bootstrap.Modal(document.getElementById('editStudioModal'));
                modal.show();
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to load studio details', 'error');
    });
}

/**
 * Update Studio Status
 */
function updateStudioStatus(studioId, status) {
    if (!confirm(`Are you sure you want to change the studio status to "${status}"?`)) {
        return;
    }
    
    fetch(`/app/photo-studio/studios/${studioId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Failed to update status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

/**
 * Toggle Studio Active
 */
function toggleStudioActive(studioId, isActive) {
    const action = isActive === 'true' ? 'activate' : 'deactivate';
    if (!confirm(`Are you sure you want to ${action} this studio?`)) {
        return;
    }
    
    fetch(`/app/photo-studio/studios/${studioId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ is_active: isActive === 'true' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Failed to update studio', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}