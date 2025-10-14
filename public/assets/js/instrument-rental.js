/**
 * VIOLET MARELLA LIMITED - INSTRUMENT RENTAL FUNCTIONALITY
 * Musical instrument rental management JavaScript
 */

// Instrument Rental state and data
const InstrumentRentalState = {
    instruments: [],
    rentals: [],
    customers: [],
    currentFilter: 'all',
    calendar: {
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear()
    }
};

// Sample instrument data
const sampleInstruments = [
    {
        id: 'inst-001',
        name: 'Acoustic Guitar - Yamaha FG830',
        category: 'guitars',
        type: 'Acoustic Guitar',
        brand: 'Yamaha',
        model: 'FG830',
        dailyRate: 1500,
        status: 'available',
        condition: 'excellent',
        description: 'Full-size acoustic guitar with solid spruce top',
        image: 'fas fa-guitar',
        serialNumber: 'YAM-001',
        purchaseDate: new Date('2023-01-15'),
        lastMaintenance: new Date('2024-01-10')
    },
    {
        id: 'inst-002',
        name: 'Electric Guitar - Fender Stratocaster',
        category: 'guitars',
        type: 'Electric Guitar',
        brand: 'Fender',
        model: 'Stratocaster',
        dailyRate: 2000,
        status: 'rented',
        condition: 'good',
        description: 'Classic electric guitar with maple neck',
        image: 'fas fa-guitar',
        serialNumber: 'FEN-002',
        purchaseDate: new Date('2023-02-20'),
        lastMaintenance: new Date('2024-01-05')
    },
    {
        id: 'inst-003',
        name: 'Digital Piano - Yamaha P-45',
        category: 'keyboards',
        type: 'Digital Piano',
        brand: 'Yamaha',
        model: 'P-45',
        dailyRate: 2500,
        status: 'available',
        condition: 'excellent',
        description: '88-key weighted digital piano',
        image: 'fas fa-piano',
        serialNumber: 'YAM-003',
        purchaseDate: new Date('2023-03-10'),
        lastMaintenance: new Date('2024-01-08')
    },
    {
        id: 'inst-004',
        name: 'Drum Kit - Pearl Export',
        category: 'drums',
        type: 'Drum Kit',
        brand: 'Pearl',
        model: 'Export',
        dailyRate: 3000,
        status: 'maintenance',
        condition: 'fair',
        description: '5-piece acoustic drum kit with cymbals',
        image: 'fas fa-drum',
        serialNumber: 'PRL-004',
        purchaseDate: new Date('2023-04-05'),
        lastMaintenance: new Date('2024-01-20')
    },
    {
        id: 'inst-005',
        name: 'Trumpet - Bach TR300H2',
        category: 'brass',
        type: 'Trumpet',
        brand: 'Bach',
        model: 'TR300H2',
        dailyRate: 1800,
        status: 'available',
        condition: 'good',
        description: 'Student trumpet with gold brass bell',
        image: 'fas fa-trumpet',
        serialNumber: 'BCH-005',
        purchaseDate: new Date('2023-05-12'),
        lastMaintenance: new Date('2024-01-15')
    },
    {
        id: 'inst-006',
        name: 'Violin - Mendini MV300',
        category: 'strings',
        type: 'Violin',
        brand: 'Mendini',
        model: 'MV300',
        dailyRate: 1200,
        status: 'rented',
        condition: 'excellent',
        description: '4/4 size violin with case and bow',
        image: 'fas fa-violin',
        serialNumber: 'MEN-006',
        purchaseDate: new Date('2023-06-18'),
        lastMaintenance: new Date('2024-01-12')
    }
];

// Sample rental data
const sampleRentals = [
    {
        id: 'rental-001',
        instrumentId: 'inst-002',
        instrumentName: 'Electric Guitar - Fender Stratocaster',
        customerId: 'cust-001',
        customerName: 'John Smith',
        customerPhone: '+234 801 234 5678',
        startDate: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000),
        endDate: new Date(Date.now() + 5 * 24 * 60 * 60 * 1000),
        dailyRate: 2000,
        totalAmount: 14000,
        securityDeposit: 5000,
        status: 'active',
        notes: 'Regular customer, handles equipment well'
    },
    {
        id: 'rental-002',
        instrumentId: 'inst-006',
        instrumentName: 'Violin - Mendini MV300',
        customerId: 'cust-002',
        customerName: 'Sarah Johnson',
        customerPhone: '+234 802 345 6789',
        startDate: new Date(Date.now() - 1 * 24 * 60 * 60 * 1000),
        endDate: new Date(Date.now() + 2 * 24 * 60 * 60 * 1000),
        dailyRate: 1200,
        totalAmount: 3600,
        securityDeposit: 2000,
        status: 'active',
        notes: 'Student rental for music lessons'
    }
];

// Sample customer data
const sampleCustomers = [
    {
        id: 'cust-001',
        name: 'John Smith',
        email: 'john.smith@email.com',
        phone: '+234 801 234 5678',
        address: '123 Music Street, Lagos',
        idNumber: 'ID123456789',
        totalRentals: 5,
        currentRentals: 1,
        totalSpent: 45000,
        memberSince: new Date('2023-06-01'),
        status: 'active'
    },
    {
        id: 'cust-002',
        name: 'Sarah Johnson',
        email: 'sarah.johnson@email.com',
        phone: '+234 802 345 6789',
        address: '456 Harmony Ave, Abuja',
        idNumber: 'ID987654321',
        totalRentals: 3,
        currentRentals: 1,
        totalSpent: 18000,
        memberSince: new Date('2023-08-15'),
        status: 'active'
    },
    {
        id: 'cust-003',
        name: 'Mike Wilson',
        email: 'mike.wilson@email.com',
        phone: '+234 803 456 7890',
        address: '789 Rhythm Road, Ibadan',
        idNumber: 'ID456789123',
        totalRentals: 8,
        currentRentals: 0,
        totalSpent: 72000,
        memberSince: new Date('2023-03-20'),
        status: 'active'
    }
];

/**
 * Initialize Instrument Rental
 */
function initializeInstrumentRental() {
    console.log('Initializing instrument rental...');
    
    // Load data
    loadInstrumentRentalData();
    
    // Bind events
    bindInstrumentRentalEvents();
    
    // Update displays
    updateInstrumentsGrid();
    updateActiveRentalsTable();
    updateCustomersTable();
    updateRentalStats();
    updateDueToday();
    initializeCalendar();
    
    console.log('Instrument rental initialized successfully');
}

/**
 * Load Instrument Rental Data
 */
function loadInstrumentRentalData() {
    InstrumentRentalState.instruments = [...sampleInstruments];
    InstrumentRentalState.rentals = [...sampleRentals];
    InstrumentRentalState.customers = [...sampleCustomers];
    
    console.log('Loaded', InstrumentRentalState.instruments.length, 'instruments');
}

/**
 * Bind Instrument Rental Events
 */
function bindInstrumentRentalEvents() {
    // Category filter buttons
    const categoryButtons = document.querySelectorAll('.category-filter .btn');
    categoryButtons.forEach(btn => {
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
    const instrumentSelect = document.getElementById('rentalInstrument');
    
    if (startDateInput && endDateInput && instrumentSelect) {
        startDateInput.addEventListener('change', calculateRentalAmount);
        endDateInput.addEventListener('change', calculateRentalAmount);
        instrumentSelect.addEventListener('change', calculateRentalAmount);
    }
}

/**
 * Update Rental Statistics
 */
function updateRentalStats() {
    const totalInstruments = InstrumentRentalState.instruments.length;
    const currentlyRented = InstrumentRentalState.instruments.filter(i => i.status === 'rented').length;
    const dueToday = InstrumentRentalState.rentals.filter(r => {
        const today = new Date().toDateString();
        return new Date(r.endDate).toDateString() === today;
    }).length;
    const monthlyRevenue = InstrumentRentalState.rentals.reduce((sum, r) => sum + r.totalAmount, 0);
    
    // Update stat cards
    const statCards = [
        { selector: '.stat-card:nth-child(1) .stat-value', value: totalInstruments },
        { selector: '.stat-card:nth-child(2) .stat-value', value: currentlyRented },
        { selector: '.stat-card:nth-child(3) .stat-value', value: dueToday },
        { selector: '.stat-card:nth-child(4) .stat-value', value: VioletMarellaCommon.formatCurrency(monthlyRevenue) }
    ];
    
    statCards.forEach(card => {
        const element = document.querySelector(card.selector);
        if (element) {
            element.textContent = card.value;
        }
    });
}

/**
 * Update Instruments Grid
 */
function updateInstrumentsGrid() {
    const instrumentsGrid = document.getElementById('instrumentsGrid');
    if (!instrumentsGrid) return;
    
    const filteredInstruments = getFilteredInstruments();
    
    if (filteredInstruments.length === 0) {
        instrumentsGrid.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="fas fa-guitar fa-3x text-muted mb-3"></i>
                <h5>No instruments found</h5>
                <p class="text-muted">Try adjusting your category filter</p>
            </div>
        `;
        return;
    }
    
    instrumentsGrid.innerHTML = filteredInstruments.map(instrument => `
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="instrument-card" data-instrument-id="${instrument.id}">
                <div class="instrument-image">
                    <i class="${instrument.image} fa-3x"></i>
                </div>
                <div class="instrument-info">
                    <h5 class="instrument-name">${instrument.name}</h5>
                    <div class="instrument-details">
                        <div><strong>Brand:</strong> ${instrument.brand}</div>
                        <div><strong>Model:</strong> ${instrument.model}</div>
                        <div><strong>Condition:</strong> ${formatCondition(instrument.condition)}</div>
                    </div>
                    <div class="rental-rate">${VioletMarellaCommon.formatCurrency(instrument.dailyRate)}/day</div>
                    <span class="availability-status ${instrument.status}">${formatStatus(instrument.status)}</span>
                    <div class="mt-3">
                        ${instrument.status === 'available' ? 
                            `<button class="btn btn-primary btn-sm w-100" onclick="rentInstrument('${instrument.id}')">
                                <i class="fas fa-calendar-plus me-1"></i>Rent Now
                            </button>` :
                            `<button class="btn btn-outline-secondary btn-sm w-100" disabled>
                                ${instrument.status === 'rented' ? 'Currently Rented' : 'Under Maintenance'}
                            </button>`
                        }
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

/**
 * Get Filtered Instruments
 */
function getFilteredInstruments() {
    if (InstrumentRentalState.currentFilter === 'all') {
        return InstrumentRentalState.instruments;
    }
    return InstrumentRentalState.instruments.filter(i => i.category === InstrumentRentalState.currentFilter);
}

/**
 * Handle Category Filter
 */
function handleCategoryFilter(event) {
    const category = event.target.getAttribute('data-category');
    InstrumentRentalState.currentFilter = category;
    
    // Update active state
    document.querySelectorAll('.category-filter .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    updateInstrumentsGrid();
}

/**
 * Update Active Rentals Table
 */
function updateActiveRentalsTable() {
    const activeRentalsTable = document.getElementById('activeRentalsTable');
    if (!activeRentalsTable) return;
    
    const activeRentals = InstrumentRentalState.rentals.filter(r => r.status === 'active');
    
    if (activeRentals.length === 0) {
        activeRentalsTable.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                    <div>No active rentals</div>
                    <small class="text-muted">Active rentals will appear here</small>
                </td>
            </tr>
        `;
        return;
    }
    
    activeRentalsTable.innerHTML = activeRentals.map(rental => `
        <tr data-rental-id="${rental.id}">
            <td><strong>${rental.id.toUpperCase()}</strong></td>
            <td>
                <div>
                    <div class="fw-semibold">${rental.customerName}</div>
                    <small class="text-muted">${rental.customerPhone}</small>
                </div>
            </td>
            <td>${rental.instrumentName}</td>
            <td>${VioletMarellaCommon.formatDate(rental.startDate, 'short')}</td>
            <td>${VioletMarellaCommon.formatDate(rental.endDate, 'short')}</td>
            <td>
                <span class="badge ${getRentalStatusClass(rental)}">
                    ${formatRentalStatus(rental)}
                </span>
            </td>
            <td>${VioletMarellaCommon.formatCurrency(rental.totalAmount)}</td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="viewRental('${rental.id}')" title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-success" onclick="extendRental('${rental.id}')" title="Extend">
                        <i class="fas fa-calendar-plus"></i>
                    </button>
                    <button class="btn btn-outline-warning" onclick="returnInstrument('${rental.id}')" title="Return">
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
    const customersTable = document.getElementById('customersTable');
    if (!customersTable) return;
    
    if (InstrumentRentalState.customers.length === 0) {
        customersTable.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <div>No customers found</div>
                    <small class="text-muted">Customer database will appear here</small>
                </td>
            </tr>
        `;
        return;
    }
    
    customersTable.innerHTML = InstrumentRentalState.customers.map(customer => `
        <tr data-customer-id="${customer.id}">
            <td>
                <div>
                    <div class="fw-semibold">${customer.name}</div>
                    <small class="text-muted">${customer.email}</small>
                    <small class="text-muted d-block">${customer.phone}</small>
                </div>
            </td>
            <td>
                <div>${customer.phone}</div>
                <small class="text-muted">${customer.email}</small>
            </td>
            <td>${customer.totalRentals}</td>
            <td>${customer.currentRentals}</td>
            <td>${VioletMarellaCommon.formatCurrency(customer.totalSpent)}</td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="viewCustomer('${customer.id}')" title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-success" onclick="newRentalForCustomer('${customer.id}')" title="New Rental">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button class="btn btn-outline-info" onclick="editCustomer('${customer.id}')" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

/**
 * Update Due Today Section
 */
function updateDueToday() {
    const dueToday = document.getElementById('dueToday');
    if (!dueToday) return;
    
    const today = new Date().toDateString();
    const dueTodayRentals = InstrumentRentalState.rentals.filter(r => 
        new Date(r.endDate).toDateString() === today && r.status === 'active'
    );
    
    if (dueTodayRentals.length === 0) {
        dueToday.innerHTML = `
            <div class="text-center py-3">
                <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                <div>No rentals due today!</div>
            </div>
        `;
        return;
    }
    
    dueToday.innerHTML = dueTodayRentals.map(rental => `
        <div class="due-item">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-semibold">${rental.customerName}</div>
                    <small class="text-muted">${rental.instrumentName}</small>
                </div>
                <button class="btn btn-sm btn-warning" onclick="contactCustomer('${rental.customerId}')">
                    <i class="fas fa-phone"></i>
                </button>
            </div>
        </div>
    `).join('');
}

/**
 * Initialize Calendar
 */
function initializeCalendar() {
    updateCalendarDisplay();
}

/**
 * Update Calendar Display
 */
function updateCalendarDisplay() {
    const currentMonthElement = document.getElementById('currentMonth');
    const calendarGrid = document.getElementById('calendarGrid');
    
    if (!currentMonthElement || !calendarGrid) return;
    
    const { currentMonth, currentYear } = InstrumentRentalState.calendar;
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'];
    
    currentMonthElement.textContent = `${monthNames[currentMonth]} ${currentYear}`;
    
    // Generate calendar days
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    
    let calendarHTML = '';
    
    // Day headers
    const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    dayHeaders.forEach(day => {
        calendarHTML += `<div class="calendar-day-header"><strong>${day}</strong></div>`;
    });
    
    // Empty cells for days before month starts
    for (let i = 0; i < firstDay; i++) {
        calendarHTML += '<div class="calendar-day other-month"></div>';
    }
    
    // Days of the month
    for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(currentYear, currentMonth, day);
        const isToday = date.toDateString() === new Date().toDateString();
        const rentalsOnDay = InstrumentRentalState.rentals.filter(r => {
            const startDate = new Date(r.startDate);
            const endDate = new Date(r.endDate);
            return date >= startDate && date <= endDate && r.status === 'active';
        });
        
        calendarHTML += `
            <div class="calendar-day ${isToday ? 'today' : ''}">
                <div class="day-number">${day}</div>
                <div class="day-events">
                    ${rentalsOnDay.map(rental => `
                        <div class="day-event" title="${rental.customerName} - ${rental.instrumentName}">
                            ${rental.customerName.split(' ')[0]}
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }
    
    calendarGrid.innerHTML = calendarHTML;
}

/**
 * Navigate Calendar
 */
function previousMonth() {
    if (InstrumentRentalState.calendar.currentMonth === 0) {
        InstrumentRentalState.calendar.currentMonth = 11;
        InstrumentRentalState.calendar.currentYear--;
    } else {
        InstrumentRentalState.calendar.currentMonth--;
    }
    updateCalendarDisplay();
}

function nextMonth() {
    if (InstrumentRentalState.calendar.currentMonth === 11) {
        InstrumentRentalState.calendar.currentMonth = 0;
        InstrumentRentalState.calendar.currentYear++;
    } else {
        InstrumentRentalState.calendar.currentMonth++;
    }
    updateCalendarDisplay();
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
 * Get Rental Status Class
 */
function getRentalStatusClass(rental) {
    const today = new Date();
    const endDate = new Date(rental.endDate);
    
    if (endDate < today) return 'bg-danger';
    if (endDate.toDateString() === today.toDateString()) return 'bg-warning';
    return 'bg-success';
}

/**
 * Format Rental Status
 */
function formatRentalStatus(rental) {
    const today = new Date();
    const endDate = new Date(rental.endDate);
    
    if (endDate < today) return 'Overdue';
    if (endDate.toDateString() === today.toDateString()) return 'Due Today';
    return 'Active';
}

/**
 * Rent Instrument
 */
function rentInstrument(instrumentId) {
    const instrument = InstrumentRentalState.instruments.find(i => i.id === instrumentId);
    if (!instrument || instrument.status !== 'available') return;
    
    // Pre-select instrument in modal
    const instrumentSelect = document.getElementById('rentalInstrument');
    if (instrumentSelect) {
        // Populate available instruments
        populateInstrumentSelect();
        instrumentSelect.value = instrumentId;
        calculateRentalAmount();
    }
    
    // Show rental modal
    const modal = new bootstrap.Modal(document.getElementById('newRentalModal'));
    modal.show();
}

/**
 * Populate Customer Select
 */
function populateCustomerSelect() {
    const customerSelect = document.getElementById('rentalCustomer');
    if (!customerSelect) return;
    
    customerSelect.innerHTML = '<option value="">Select Customer</option>' +
        InstrumentRentalState.customers.map(customer => 
            `<option value="${customer.id}">${customer.name} - ${customer.phone}</option>`
        ).join('');
}

/**
 * Populate Instrument Select
 */
function populateInstrumentSelect() {
    const instrumentSelect = document.getElementById('rentalInstrument');
    if (!instrumentSelect) return;
    
    const availableInstruments = InstrumentRentalState.instruments.filter(i => i.status === 'available');
    instrumentSelect.innerHTML = '<option value="">Select Instrument</option>' +
        availableInstruments.map(instrument => 
            `<option value="${instrument.id}" data-rate="${instrument.dailyRate}">${instrument.name} - ${VioletMarellaCommon.formatCurrency(instrument.dailyRate)}/day</option>`
        ).join('');
}

/**
 * Calculate Rental Amount
 */
function calculateRentalAmount() {
    const startDate = document.getElementById('rentalStartDate')?.value;
    const endDate = document.getElementById('rentalEndDate')?.value;
    const instrumentSelect = document.getElementById('rentalInstrument');
    const dailyRateInput = document.getElementById('dailyRate');
    const totalAmountInput = document.getElementById('totalAmount');
    
    if (!startDate || !endDate || !instrumentSelect?.value) return;
    
    const start = new Date(startDate);
    const end = new Date(endDate);
    const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
    
    if (days <= 0) return;
    
    const selectedOption = instrumentSelect.options[instrumentSelect.selectedIndex];
    const dailyRate = parseInt(selectedOption.getAttribute('data-rate')) || 0;
    const totalAmount = days * dailyRate;
    
    if (dailyRateInput) dailyRateInput.value = dailyRate;
    if (totalAmountInput) totalAmountInput.value = totalAmount;
}

/**
 * Handle New Rental
 */
function handleNewRental(event) {
    event.preventDefault();
    createRental();
}

/**
 * Create Rental
 */
function createRental() {
    const form = document.getElementById('newRentalForm');
    if (!form || !VioletMarellaCommon.validateForm(form)) return;
    
    const customerId = document.getElementById('rentalCustomer').value;
    const instrumentId = document.getElementById('rentalInstrument').value;
    const startDate = document.getElementById('rentalStartDate').value;
    const endDate = document.getElementById('rentalEndDate').value;
    const securityDeposit = document.getElementById('securityDeposit').value;
    const notes = document.getElementById('rentalNotes').value;
    const agreementSigned = document.getElementById('agreementSigned').checked;
    
    if (!customerId || !instrumentId || !startDate || !endDate || !agreementSigned) {
        VioletMarellaCommon.showNotification('Please fill in all required fields and confirm agreement', 'error');
        return;
    }
    
    const customer = InstrumentRentalState.customers.find(c => c.id === customerId);
    const instrument = InstrumentRentalState.instruments.find(i => i.id === instrumentId);
    
    if (!customer || !instrument) return;
    
    const start = new Date(startDate);
    const end = new Date(endDate);
    const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
    const totalAmount = days * instrument.dailyRate;
    
    const newRental = {
        id: `rental-${Date.now()}`,
        instrumentId: instrumentId,
        instrumentName: instrument.name,
        customerId: customerId,
        customerName: customer.name,
        customerPhone: customer.phone,
        startDate: start,
        endDate: end,
        dailyRate: instrument.dailyRate,
        totalAmount: totalAmount,
        securityDeposit: parseInt(securityDeposit),
        status: 'active',
        notes: notes
    };
    
    // Add rental
    InstrumentRentalState.rentals.push(newRental);
    
    // Update instrument status
    instrument.status = 'rented';
    
    // Update customer stats
    customer.currentRentals++;
    customer.totalRentals++;
    customer.totalSpent += totalAmount;
    
    // Update displays
    updateInstrumentsGrid();
    updateActiveRentalsTable();
    updateCustomersTable();
    updateRentalStats();
    updateDueToday();
    updateCalendarDisplay();
    
    // Close modal and reset form
    const modal = bootstrap.Modal.getInstance(document.getElementById('newRentalModal'));
    modal.hide();
    form.reset();
    
    VioletMarellaCommon.showNotification(`Rental created for ${customer.name} - ${instrument.name}`, 'success');
}

/**
 * Handle New Customer
 */
function handleNewCustomer(event) {
    event.preventDefault();
    addCustomer();
}

/**
 * Add Customer
 */
function addCustomer() {
    const form = document.getElementById('newCustomerForm');
    if (!form || !VioletMarellaCommon.validateForm(form)) return;
    
    const formData = new FormData(form);
    const newCustomer = {
        id: `cust-${Date.now()}`,
        name: formData.get('name') || form.querySelector('input[type="text"]').value,
        email: formData.get('email') || form.querySelector('input[type="email"]').value,
        phone: formData.get('phone') || form.querySelector('input[type="tel"]').value,
        address: formData.get('address') || form.querySelector('textarea').value,
        idNumber: formData.get('idNumber') || form.querySelectorAll('input[type="text"]')[1].value,
        totalRentals: 0,
        currentRentals: 0,
        totalSpent: 0,
        memberSince: new Date(),
        status: 'active'
    };
    
    // Add customer
    InstrumentRentalState.customers.push(newCustomer);
    
    // Update displays
    updateCustomersTable();
    populateCustomerSelect();
    
    // Close modal and reset form
    const modal = bootstrap.Modal.getInstance(document.getElementById('newCustomerModal'));
    modal.hide();
    form.reset();
    
    VioletMarellaCommon.showNotification(`Customer ${newCustomer.name} added successfully`, 'success');
}

/**
 * View Rental Details
 */
function viewRental(rentalId) {
    const rental = InstrumentRentalState.rentals.find(r => r.id === rentalId);
    if (!rental) return;
    
    const details = `
        Rental ID: ${rental.id.toUpperCase()}
        Customer: ${rental.customerName}
        Phone: ${rental.customerPhone}
        Instrument: ${rental.instrumentName}
        Start Date: ${VioletMarellaCommon.formatDate(rental.startDate, 'datetime')}
        End Date: ${VioletMarellaCommon.formatDate(rental.endDate, 'datetime')}
        Daily Rate: ${VioletMarellaCommon.formatCurrency(rental.dailyRate)}
        Total Amount: ${VioletMarellaCommon.formatCurrency(rental.totalAmount)}
        Security Deposit: ${VioletMarellaCommon.formatCurrency(rental.securityDeposit)}
        Status: ${formatRentalStatus(rental)}
        Notes: ${rental.notes || 'None'}
    `;
    
    alert(details); // In production, use a proper modal
}

/**
 * Extend Rental
 */
function extendRental(rentalId) {
    const rental = InstrumentRentalState.rentals.find(r => r.id === rentalId);
    if (!rental) return;
    
    const additionalDays = prompt('Enter additional days:', '1');
    if (!additionalDays || isNaN(additionalDays)) return;
    
    const days = parseInt(additionalDays);
    const additionalAmount = days * rental.dailyRate;
    
    // Update rental
    rental.endDate = new Date(rental.endDate.getTime() + days * 24 * 60 * 60 * 1000);
    rental.totalAmount += additionalAmount;
    
    // Update customer spending
    const customer = InstrumentRentalState.customers.find(c => c.id === rental.customerId);
    if (customer) {
        customer.totalSpent += additionalAmount;
    }
    
    // Update displays
    updateActiveRentalsTable();
    updateCustomersTable();
    updateCalendarDisplay();
    
    VioletMarellaCommon.showNotification(`Rental extended by ${days} day(s) - Additional charge: ${VioletMarellaCommon.formatCurrency(additionalAmount)}`, 'success');
}

/**
 * Return Instrument
 */
function returnInstrument(rentalId) {
    const rental = InstrumentRentalState.rentals.find(r => r.id === rentalId);
    if (!rental) return;
    
    const instrument = InstrumentRentalState.instruments.find(i => i.id === rental.instrumentId);
    const customer = InstrumentRentalState.customers.find(c => c.id === rental.customerId);
    
    if (!instrument || !customer) return;
    
    if (confirm(`Process return for ${rental.customerName}?\n\nInstrument: ${rental.instrumentName}\nTotal paid: ${VioletMarellaCommon.formatCurrency(rental.totalAmount)}`)) {
        // Update rental status
        rental.status = 'completed';
        
        // Update instrument status
        instrument.status = 'available';
        
        // Update customer stats
        customer.currentRentals--;
        
        // Update displays
        updateInstrumentsGrid();
        updateActiveRentalsTable();
        updateCustomersTable();
        updateRentalStats();
        updateDueToday();
        updateCalendarDisplay();
        
        VioletMarellaCommon.showNotification(`${rental.instrumentName} returned by ${rental.customerName}`, 'success');
    }
}

/**
 * View Customer Details
 */
function viewCustomer(customerId) {
    const customer = InstrumentRentalState.customers.find(c => c.id === customerId);
    if (!customer) return;
    
    const customerRentals = InstrumentRentalState.rentals.filter(r => r.customerId === customerId);
    
    const details = `
        Customer: ${customer.name}
        Email: ${customer.email}
        Phone: ${customer.phone}
        Address: ${customer.address}
        ID Number: ${customer.idNumber}
        Member Since: ${VioletMarellaCommon.formatDate(customer.memberSince, 'short')}
        Total Rentals: ${customer.totalRentals}
        Current Rentals: ${customer.currentRentals}
        Total Spent: ${VioletMarellaCommon.formatCurrency(customer.totalSpent)}
        Status: ${customer.status}
        
        Recent Rentals:
        ${customerRentals.slice(0, 3).map(r => `${r.instrumentName} (${VioletMarellaCommon.formatDate(r.startDate, 'short')})`).join('\n')}
    `;
    
    alert(details); // In production, use a proper modal
}

/**
 * New Rental for Customer
 */
function newRentalForCustomer(customerId) {
    // Pre-select customer in modal
    populateCustomerSelect();
    populateInstrumentSelect();
    
    const customerSelect = document.getElementById('rentalCustomer');
    if (customerSelect) {
        customerSelect.value = customerId;
    }
    
    // Show rental modal
    const modal = new bootstrap.Modal(document.getElementById('newRentalModal'));
    modal.show();
}

/**
 * Edit Customer
 */
function editCustomer(customerId) {
    const customer = InstrumentRentalState.customers.find(c => c.id === customerId);
    if (!customer) return;
    
    VioletMarellaCommon.showNotification(`Edit functionality for ${customer.name} would be implemented here`, 'info');
}

/**
 * Contact Customer
 */
function contactCustomer(customerId) {
    const customer = InstrumentRentalState.customers.find(c => c.id === customerId);
    if (!customer) return;
    
    // In production, this would open a communication modal or dial the number
    if (confirm(`Call ${customer.name} at ${customer.phone}?`)) {
        VioletMarellaCommon.showNotification(`Calling ${customer.name}...`, 'info');
    }
}

/**
 * Add Instrument
 */
function addInstrument() {
    VioletMarellaCommon.showNotification('Add instrument functionality would be implemented here', 'info');
}

/**
 * Check Instrument
 */
function checkInstrument() {
    VioletMarellaCommon.showNotification('Instrument check-in functionality would be implemented here', 'info');
}

/**
 * Mark for Maintenance
 */
function markMaintenance() {
    VioletMarellaCommon.showNotification('Mark for maintenance functionality would be implemented here', 'info');
}

/**
 * Generate Report
 */
function generateReport() {
    VioletMarellaCommon.showNotification('Generating rental report...', 'info');
    setTimeout(() => {
        window.location.href = 'reports.html?type=instrument-rental';
    }, 1000);
}

/**
 * Export Rentals
 */
function exportRentals() {
    const data = InstrumentRentalState.rentals.map(rental => ({
        'Rental ID': rental.id.toUpperCase(),
        'Customer': rental.customerName,
        'Phone': rental.customerPhone,
        'Instrument': rental.instrumentName,
        'Start Date': VioletMarellaCommon.formatDate(rental.startDate, 'short'),
        'End Date': VioletMarellaCommon.formatDate(rental.endDate, 'short'),
        'Daily Rate': rental.dailyRate,
        'Total Amount': rental.totalAmount,
        'Security Deposit': rental.securityDeposit,
        'Status': formatRentalStatus(rental),
        'Notes': rental.notes || ''
    }));
    
    const filename = `instrument-rentals-${new Date().toISOString().split('T')[0]}.csv`;
    VioletMarellaCommon.exportToCSV(data, filename);
    VioletMarellaCommon.showNotification('Rentals exported successfully', 'success');
}

/**
 * Initialize on DOM Content Loaded
 */
document.addEventListener('DOMContentLoaded', function() {
    // Populate dropdowns when modal opens
    document.getElementById('newRentalModal')?.addEventListener('shown.bs.modal', function() {
        populateCustomerSelect();
        populateInstrumentSelect();
    });
    
    // Small delay to ensure common.js has loaded
    setTimeout(initializeInstrumentRental, 100);
});

// Export instrument rental functions for global access
window.VioletMarellaInstrumentRental = {
    rentInstrument,
    viewRental,
    extendRental,
    returnInstrument,
    viewCustomer,
    newRentalForCustomer,
    editCustomer,
    contactCustomer,
    addInstrument,
    checkInstrument,
    markMaintenance,
    generateReport,
    exportRentals,
    createRental,
    addCustomer,
    previousMonth,
    nextMonth
};