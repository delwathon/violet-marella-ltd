/**
 * VIOLET MARELLA LIMITED - PROP RENTAL FUNCTIONALITY
 * Backend-integrated prop rental management
 */

// Prop Rental State
const PropRentalState = {
    props: [],
    rentals: [],
    customers: [],
    currentFilter: 'all',
    calendar: {
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear()
    }
};

// API Base URL
const API_BASE = '/app/prop-rental';

/**
 * Initialize Prop Rental
 */
async function initializePropRental() {
    console.log('Initializing prop rental...');
    
    try {
        // Load data from backend
        await Promise.all([
            loadProps(),
            loadActiveRentals(),
            loadCustomers(),
            loadDueToday()
        ]);
        
        // Bind events
        bindPropRentalEvents();
        
        // Update displays
        updatePropsGrid();
        updateActiveRentalsTable();
        updateCustomersTable();
        updateDueToday();
        initializeCalendar();
        
        console.log('Prop Rental initialized successfully');
    } catch (error) {
        console.error('Failed to initialize prop rental:', error);
        showNotification('Failed to load prop rental data', 'error');
    }
}

/**
 * Load Props from Backend
 */
async function loadProps(category = 'all') {
    try {
        const response = await fetch(`${API_BASE}/props?category=${category}`);
        const data = await response.json();
        
        if (data.success) {
            PropRentalState.props = data.props;
        }
    } catch (error) {
        console.error('Failed to load props:', error);
        throw error;
    }
}

/**
 * Load Active Rentals
 */
async function loadActiveRentals() {
    try {
        const response = await fetch(`${API_BASE}/rentals`);
        const data = await response.json();
        
        if (data.success) {
            PropRentalState.rentals = data.rentals;
        }
    } catch (error) {
        console.error('Failed to load rentals:', error);
        throw error;
    }
}

/**
 * Load Customers
 */
async function loadCustomers() {
    try {
        const response = await fetch(`${API_BASE}/customers`);
        const data = await response.json();
        
        if (data.success) {
            PropRentalState.customers = data.customers;
        }
    } catch (error) {
        console.error('Failed to load customers:', error);
        throw error;
    }
}

/**
 * Load Due Today
 */
async function loadDueToday() {
    try {
        const response = await fetch(`${API_BASE}/rentals/due/today`);
        const data = await response.json();
        
        if (data.success) {
            updateDueTodayDisplay(data.rentals);
        }
    } catch (error) {
        console.error('Failed to load due today:', error);
    }
}

/**
 * Bind Events
 */
function bindPropRentalEvents() {
    // Category filter
    document.querySelectorAll('.category-filter .btn').forEach(btn => {
        btn.addEventListener('click', handleCategoryFilter);
    });
    
    // New rental form
    const newRentalForm = document.getElementById('newRentalForm');
    if (newRentalForm) {
        newRentalForm.addEventListener('submit', handleNewRental);
    }
    
    // New customer form
    const newCustomerForm = document.getElementById('newCustomerForm');
    if (newCustomerForm) {
        newCustomerForm.addEventListener('submit', handleNewCustomer);
    }
    
    // Rental calculations
    const startDateInput = document.getElementById('rentalStartDate');
    const endDateInput = document.getElementById('rentalEndDate');
    const propSelect = document.getElementById('rentalProp');
    
    if (startDateInput && endDateInput && propSelect) {
        startDateInput.addEventListener('change', calculateRentalAmount);
        endDateInput.addEventListener('change', calculateRentalAmount);
        propSelect.addEventListener('change', calculateRentalAmount);
    }
}

/**
 * Handle Category Filter
 */
async function handleCategoryFilter(event) {
    const category = event.target.getAttribute('data-category');
    PropRentalState.currentFilter = category;
    
    // Update active state
    document.querySelectorAll('.category-filter .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Reload props with filter
    await loadProps(category);
    updatePropsGrid();
}

/**
 * Update Props Grid
 */
function updatePropsGrid() {
    const propsGrid = document.getElementById('propsGrid');
    if (!propsGrid) return;
    
    const props = PropRentalState.props;
    
    if (props.length === 0) {
        propsGrid.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="fas fa-guitar fa-3x text-muted mb-3"></i>
                <h5>No props found</h5>
                <p class="text-muted">Try adjusting your category filter</p>
            </div>
        `;
        return;
    }
    
    propsGrid.innerHTML = props.map(prop => `
        <div class="col">
            <div class="prop-card" data-prop-id="${prop.id}">
                <div class="prop-image">
                    <i class="${prop.image || 'fas fa-music'} fa-3x"></i>
                </div>
                <div class="prop-info">
                    <h5 class="prop-name">${prop.name}</h5>
                    <div class="prop-details">
                        <div><strong>Brand:</strong> ${prop.brand}</div>
                        <div><strong>Model:</strong> ${prop.model}</div>
                        <div><strong>Condition:</strong> ${formatCondition(prop.condition)}</div>
                    </div>
                    <div class="rental-rate">₦${parseFloat(prop.daily_rate).toLocaleString()}/day</div>
                    <span class="availability-status ${prop.status}">${formatStatus(prop.status)}</span>
                    <div class="mt-3">
                        ${prop.status === 'available' ? 
                            `<button class="btn btn-primary btn-sm w-100" onclick="rentProp(${prop.id})">
                                <i class="fas fa-calendar-plus me-1"></i>Rent Now
                            </button>` :
                            `<button class="btn btn-outline-secondary btn-sm w-100" disabled>
                                ${prop.status === 'rented' ? 'Currently Rented' : 'Under Maintenance'}
                            </button>`
                        }
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

/**
 * Update Active Rentals Table
 */
function updateActiveRentalsTable() {
    const tbody = document.querySelector('#active-rentals tbody');
    if (!tbody) return;
    
    const rentals = PropRentalState.rentals.filter(r => r.status === 'active');
    
    if (rentals.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <i class="fas fa-calendar fa-3x text-muted mb-3 d-block"></i>
                    <div>No active rentals</div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = rentals.map(rental => `
        <tr>
            <td><strong>${rental.rental_id.toUpperCase()}</strong></td>
            <td>
                <div class="fw-semibold">${rental.customer.name}</div>
                <small class="text-muted">${rental.customer.phone}</small>
            </td>
            <td>${rental.prop.name}</td>
            <td>${formatDate(rental.start_date)}</td>
            <td>${formatDate(rental.end_date)}</td>
            <td><span class="badge ${rental.status_badge_class}">${rental.status_display}</span></td>
            <td>₦${parseFloat(rental.total_amount).toLocaleString()}</td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="viewRental(${rental.id})" title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-success" onclick="extendRental(${rental.id})" title="Extend">
                        <i class="fas fa-calendar-plus"></i>
                    </button>
                    <button class="btn btn-outline-warning" onclick="returnProp(${rental.id})" title="Return">
                        <i class="fas fa-undo"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

/**
 * Update Customers Table
 */
function updateCustomersTable() {
    const tbody = document.querySelector('#customers tbody');
    if (!tbody) return;
    
    if (PropRentalState.customers.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                    <div>No customers found</div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = PropRentalState.customers.map(customer => `
        <tr>
            <td>
                <div class="fw-semibold">${customer.name}</div>
                <small class="text-muted">${customer.email}</small>
            </td>
            <td>
                <div>${customer.phone}</div>
            </td>
            <td>${customer.total_rentals}</td>
            <td>${customer.current_rentals}</td>
            <td>₦${parseFloat(customer.total_spent).toLocaleString()}</td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="viewCustomer(${customer.id})" title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-success" onclick="newRentalForCustomer(${customer.id})" title="New Rental">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

/**
 * Update Due Today Display
 */
function updateDueTodayDisplay(rentals) {
    const container = document.getElementById('dueToday');
    if (!container) return;
    
    if (!rentals || rentals.length === 0) {
        container.innerHTML = `
            <div class="text-center py-3">
                <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                <div>No rentals due today!</div>
            </div>
        `;
        return;
    }
    
    container.innerHTML = rentals.map(rental => `
        <div class="due-item">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-semibold">${rental.customer.name}</div>
                    <small class="text-muted">${rental.prop.name}</small>
                </div>
                <button class="btn btn-sm btn-warning" onclick="contactCustomer(${rental.customer.id})">
                    <i class="fas fa-phone"></i>
                </button>
            </div>
        </div>
    `).join('');
}

/**
 * Handle New Rental
 */
async function handleNewRental(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    try {
        const response = await fetch(`${API_BASE}/rentals`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                customer_id: formData.get('customer_id'),
                prop_id: formData.get('prop_id'),
                start_date: formData.get('start_date'),
                end_date: formData.get('end_date'),
                security_deposit: formData.get('security_deposit'),
                amount_paid: formData.get('amount_paid'),
                notes: formData.get('notes'),
                agreement_signed: formData.get('agreement_signed') ? true : false
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            
            // Reload data
            await loadProps();
            await loadActiveRentals();
            await loadCustomers();
            await loadDueToday();
            
            // Update displays
            updatePropsGrid();
            updateActiveRentalsTable();
            updateCustomersTable();
            
            // Close modal and reset form
            const modal = bootstrap.Modal.getInstance(document.getElementById('newRentalModal'));
            modal.hide();
            form.reset();
        } else {
            showNotification(data.message || 'Failed to create rental', 'error');
        }
    } catch (error) {
        console.error('Failed to create rental:', error);
        showNotification('Failed to create rental', 'error');
    }
}

/**
 * Handle New Customer
 */
async function handleNewCustomer(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    try {
        const response = await fetch(`${API_BASE}/customers`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: formData.get('name'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                address: formData.get('address'),
                id_number: formData.get('id_number')
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            
            // Reload customers
            await loadCustomers();
            updateCustomersTable();
            populateCustomerSelect();
            
            // Close modal and reset form
            const modal = bootstrap.Modal.getInstance(document.getElementById('newCustomerModal'));
            modal.hide();
            form.reset();
        } else {
            showNotification(data.message || 'Failed to add customer', 'error');
        }
    } catch (error) {
        console.error('Failed to add customer:', error);
        showNotification('Failed to add customer', 'error');
    }
}

/**
 * Rent Prop
 */
function rentProp(propId) {
    const prop = PropRentalState.props.find(p => p.id === propId);
    if (!prop || prop.status !== 'available') return;
    
    // Populate dropdowns
    populateCustomerSelect();
    populatePropSelect();
    
    // Pre-select prop
    const propSelect = document.getElementById('rentalProp');
    if (propSelect) {
        propSelect.value = propId;
        calculateRentalAmount();
    }
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('newRentalModal'));
    modal.show();
}

/**
 * View Rental
 */
async function viewRental(rentalId) {
    try {
        const response = await fetch(`${API_BASE}/rentals/${rentalId}`);
        const data = await response.json();
        
        if (data.success) {
            const rental = data.rental;
            alert(`
Rental ID: ${rental.rental_id.toUpperCase()}
Customer: ${rental.customer.name}
Phone: ${rental.customer.phone}
Prop: ${rental.prop.name}
Start Date: ${formatDate(rental.start_date)}
End Date: ${formatDate(rental.end_date)}
Daily Rate: ₦${parseFloat(rental.daily_rate).toLocaleString()}
Total Amount: ₦${parseFloat(rental.total_amount).toLocaleString()}
Security Deposit: ₦${parseFloat(rental.security_deposit).toLocaleString()}
Status: ${rental.status_display}
Notes: ${rental.notes || 'None'}
            `);
        }
    } catch (error) {
        console.error('Failed to load rental:', error);
        showNotification('Failed to load rental details', 'error');
    }
}

/**
 * Extend Rental
 */
async function extendRental(rentalId) {
    const additionalDays = prompt('Enter additional days:', '1');
    if (!additionalDays || isNaN(additionalDays)) return;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    try {
        const response = await fetch(`${API_BASE}/rentals/${rentalId}/extend`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                additional_days: parseInt(additionalDays)
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(`${data.message} Additional charge: ₦${parseFloat(data.additional_amount).toLocaleString()}`, 'success');
            
            // Reload data
            await loadActiveRentals();
            await loadCustomers();
            updateActiveRentalsTable();
            updateCustomersTable();
        } else {
            showNotification(data.message || 'Failed to extend rental', 'error');
        }
    } catch (error) {
        console.error('Failed to extend rental:', error);
        showNotification('Failed to extend rental', 'error');
    }
}

/**
 * Return Prop
 */
async function returnProp(rentalId) {
    if (!confirm('Process return for this rental?')) return;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    try {
        const response = await fetch(`${API_BASE}/rentals/${rentalId}/return`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            
            // Reload data
            await loadProps();
            await loadActiveRentals();
            await loadCustomers();
            await loadDueToday();
            
            updatePropsGrid();
            updateActiveRentalsTable();
            updateCustomersTable();
        } else {
            showNotification(data.message || 'Failed to return prop', 'error');
        }
    } catch (error) {
        console.error('Failed to return prop:', error);
        showNotification('Failed to return prop', 'error');
    }
}

/**
 * View Customer
 */
async function viewCustomer(customerId) {
    try {
        const response = await fetch(`${API_BASE}/customers/${customerId}`);
        const data = await response.json();
        
        if (data.success) {
            const customer = data.customer;
            const recentRentals = customer.rentals.slice(0, 3).map(r => 
                `${r.prop.name} (${formatDate(r.start_date)})`
            ).join('\n');
            
            alert(`
Customer: ${customer.name}
Email: ${customer.email}
Phone: ${customer.phone}
Address: ${customer.address || 'N/A'}
ID Number: ${customer.id_number}
Member Since: ${formatDate(customer.created_at)}
Total Rentals: ${customer.total_rentals}
Current Rentals: ${customer.current_rentals}
Total Spent: ₦${parseFloat(customer.total_spent).toLocaleString()}
Status: ${customer.status}

Recent Rentals:
${recentRentals || 'None'}
            `);
        }
    } catch (error) {
        console.error('Failed to load customer:', error);
        showNotification('Failed to load customer details', 'error');
    }
}

/**
 * New Rental for Customer
 */
function newRentalForCustomer(customerId) {
    populateCustomerSelect();
    populatePropSelect();
    
    const customerSelect = document.getElementById('rentalCustomer');
    if (customerSelect) {
        customerSelect.value = customerId;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('newRentalModal'));
    modal.show();
}

/**
 * Contact Customer
 */
function contactCustomer(customerId) {
    const customer = PropRentalState.customers.find(c => c.id === customerId);
    if (!customer) return;
    
    if (confirm(`Call ${customer.name} at ${customer.phone}?`)) {
        showNotification(`Calling ${customer.name}...`, 'info');
        // In production, integrate with phone system
        window.location.href = `tel:${customer.phone}`;
    }
}

/**
 * Populate Customer Select
 */
function populateCustomerSelect() {
    const select = document.getElementById('rentalCustomer');
    if (!select) return;
    
    select.innerHTML = '<option value="">Select Customer</option>' +
        PropRentalState.customers
            .filter(c => c.status === 'active')
            .map(c => `<option value="${c.id}">${c.name} - ${c.phone}</option>`)
            .join('');
}

/**
 * Populate Prop Select
 */
function populatePropSelect() {
    const select = document.getElementById('rentalProp');
    if (!select) return;
    
    const availableProps = PropRentalState.props.filter(p => p.status === 'available');
    
    select.innerHTML = '<option value="">Select Prop</option>' +
        availableProps.map(p => 
            `<option value="${p.id}" data-rate="${p.daily_rate}">${p.name} - ₦${parseFloat(p.daily_rate).toLocaleString()}/day</option>`
        ).join('');
}

/**
 * Calculate Rental Amount
 */
function calculateRentalAmount() {
    const startDate = document.getElementById('rentalStartDate')?.value;
    const endDate = document.getElementById('rentalEndDate')?.value;
    const propSelect = document.getElementById('rentalProp');
    const dailyRateInput = document.getElementById('dailyRate');
    const totalAmountInput = document.getElementById('totalAmount');
    
    if (!startDate || !endDate || !propSelect?.value) return;
    
    const start = new Date(startDate);
    const end = new Date(endDate);
    const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
    
    if (days <= 0) {
        if (totalAmountInput) totalAmountInput.value = 0;
        return;
    }
    
    const selectedOption = propSelect.options[propSelect.selectedIndex];
    const dailyRate = parseFloat(selectedOption.getAttribute('data-rate')) || 0;
    const totalAmount = days * dailyRate;
    
    if (dailyRateInput) dailyRateInput.value = dailyRate;
    if (totalAmountInput) totalAmountInput.value = totalAmount;
}

/**
 * Initialize Calendar
 */
async function initializeCalendar() {
    await loadCalendarData();
    updateCalendarDisplay();
}

/**
 * Load Calendar Data
 */
async function loadCalendarData() {
    try {
        const { currentMonth, currentYear } = PropRentalState.calendar;
        const response = await fetch(`${API_BASE}/calendar/data?year=${currentYear}&month=${currentMonth + 1}`);
        const data = await response.json();
        
        if (data.success) {
            PropRentalState.calendarRentals = data.rentals;
        }
    } catch (error) {
        console.error('Failed to load calendar data:', error);
    }
}

/**
 * Update Calendar Display
 */
function updateCalendarDisplay() {
    const currentMonthElement = document.getElementById('currentMonth');
    const calendarGrid = document.getElementById('calendarGrid');
    
    if (!currentMonthElement || !calendarGrid) return;
    
    const { currentMonth, currentYear } = PropRentalState.calendar;
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'];
    
    currentMonthElement.textContent = `${monthNames[currentMonth]} ${currentYear}`;
    
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    
    let calendarHTML = '';
    
    // Day headers
    const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    dayHeaders.forEach(day => {
        calendarHTML += `<div class="calendar-day-header text-center fw-bold py-2"><strong>${day}</strong></div>`;
    });
    
    // Empty cells
    for (let i = 0; i < firstDay; i++) {
        calendarHTML += '<div class="calendar-day other-month"></div>';
    }
    
    // Days
    for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(currentYear, currentMonth, day);
        const isToday = date.toDateString() === new Date().toDateString();
        
        const rentalsOnDay = (PropRentalState.calendarRentals || []).filter(r => {
            const startDate = new Date(r.start_date);
            const endDate = new Date(r.end_date);
            return date >= new Date(startDate.toDateString()) && date <= new Date(endDate.toDateString());
        });
        
        calendarHTML += `
            <div class="calendar-day ${isToday ? 'today' : ''}">
                <div class="day-number">${day}</div>
                <div class="day-events">
                    ${rentalsOnDay.slice(0, 3).map(rental => `
                        <div class="day-event" title="${rental.customer.name} - ${rental.prop.name}">
                            ${rental.customer.name.split(' ')[0]}
                        </div>
                    `).join('')}
                    ${rentalsOnDay.length > 3 ? `<div class="day-event">+${rentalsOnDay.length - 3} more</div>` : ''}
                </div>
            </div>
        `;
    }
    
    calendarGrid.innerHTML = calendarHTML;
}

/**
 * Previous Month
 */
async function previousMonth() {
    if (PropRentalState.calendar.currentMonth === 0) {
        PropRentalState.calendar.currentMonth = 11;
        PropRentalState.calendar.currentYear--;
    } else {
        PropRentalState.calendar.currentMonth--;
    }
    await loadCalendarData();
    updateCalendarDisplay();
}

/**
 * Next Month
 */
async function nextMonth() {
    if (PropRentalState.calendar.currentMonth === 11) {
        PropRentalState.calendar.currentMonth = 0;
        PropRentalState.calendar.currentYear++;
    } else {
        PropRentalState.calendar.currentMonth++;
    }
    await loadCalendarData();
    updateCalendarDisplay();
}

/**
 * Export Rentals
 */
function exportRentals() {
    window.location.href = `${API_BASE}/rentals/export/csv`;
}

/**
 * Format Date
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
}

/**
 * Format Status
 */
function formatStatus(status) {
    const statuses = {
        'available': 'Available',
        'rented': 'Rented',
        'maintenance': 'Maintenance'
    };
    return statuses[status] || status;
}

/**
 * Format Condition
 */
function formatCondition(condition) {
    const conditions = {
        'excellent': 'Excellent',
        'good': 'Good',
        'fair': 'Fair',
        'poor': 'Poor'
    };
    return conditions[condition] || condition;
}

/**
 * Show Notification
 */
function showNotification(message, type = 'info') {
    // Use existing notification system or create toast
    if (typeof VioletMarellaCommon !== 'undefined' && VioletMarellaCommon.showNotification) {
        VioletMarellaCommon.showNotification(message, type);
    } else {
        alert(message);
    }
}

/**
 * Initialize on DOM Ready
 */
document.addEventListener('DOMContentLoaded', function() {
    // Populate dropdowns when modals open
    document.getElementById('newRentalModal')?.addEventListener('shown.bs.modal', function() {
        populateCustomerSelect();
        populatePropSelect();
    });
    
    // Initialize
    setTimeout(initializePropRental, 100);
});

// Export functions for global access
window.PropRentalFunctions = {
    rentProp,
    viewRental,
    extendRental,
    returnProp,
    viewCustomer,
    newRentalForCustomer,
    contactCustomer,
    previousMonth,
    nextMonth,
    exportRentals
};