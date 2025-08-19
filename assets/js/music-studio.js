/**
 * VIOLET MARELLA LIMITED - MUSIC STUDIO FUNCTIONALITY
 * Music studio management JavaScript functionality
 */

// Music Studio state and data
const MusicStudioState = {
    studios: {
        'studio-a': { name: 'Studio A', status: 'occupied', customer: null, checkInTime: null },
        'studio-b': { name: 'Studio B', status: 'occupied', customer: null, checkInTime: null },
        'studio-c': { name: 'Studio C', status: 'available', customer: null, checkInTime: null },
        'studio-d': { name: 'Studio D', status: 'maintenance', customer: null, checkInTime: null }
    },
    activeSessions: [],
    billing: {
        baseTime: 30, // minutes
        baseAmount: 2000, // Naira
        ratePerMinute: 66.67 // baseAmount / baseTime
    },
    qrCodes: new Map(),
    scanHistory: [],
    updateInterval: null
};

// Sample active sessions
const sampleSessions = [
    {
        id: 'session-001',
        customerId: 'john-smith',
        customerName: 'John Smith',
        customerPhone: '+234 801 234 5678',
        customerAvatar: 'JS',
        studioId: 'studio-a',
        studioName: 'Studio A',
        checkInTime: new Date(Date.now() - 105 * 60 * 1000), // 1h 45m ago
        expectedDuration: 120, // minutes
        qrCode: 'QR_001_1640995200',
        status: 'active'
    },
    {
        id: 'session-002',
        customerId: 'sarah-johnson',
        customerName: 'Sarah Johnson',
        customerPhone: '+234 802 345 6789',
        customerAvatar: 'SJ',
        studioId: 'studio-b',
        studioName: 'Studio B',
        checkInTime: new Date(Date.now() - 20 * 60 * 1000), // 20m ago
        expectedDuration: 60, // minutes
        qrCode: 'QR_002_1640995800',
        status: 'active'
    }
];

/**
 * Initialize Music Studio
 */
function initializeMusicStudio() {
    console.log('Initializing music studio...');
    
    // Load studio data
    loadMusicStudioData();
    
    // Initialize billing calculator
    initializeBillingCalculator();
    
    // Bind events
    bindMusicStudioEvents();
    
    // Start real-time updates
    startSessionTimer();
    
    // Update displays
    updateStudioStatusCards();
    updateActiveSessionsTab();
    updateTodaySummary();
    
    console.log('Music studio initialized successfully');
}

/**
 * Load Music Studio Data
 */
function loadMusicStudioData() {
    // Initialize with sample data
    MusicStudioState.activeSessions = [...sampleSessions];
    
    // Update studio statuses based on active sessions
    updateStudioStatuses();
    
    console.log('Loaded', MusicStudioState.activeSessions.length, 'active sessions');
}

/**
 * Update Studio Statuses
 */
function updateStudioStatuses() {
    // Reset all studios to available
    Object.keys(MusicStudioState.studios).forEach(studioId => {
        if (MusicStudioState.studios[studioId].status !== 'maintenance') {
            MusicStudioState.studios[studioId].status = 'available';
            MusicStudioState.studios[studioId].customer = null;
        }
    });
    
    // Update based on active sessions
    MusicStudioState.activeSessions.forEach(session => {
        if (MusicStudioState.studios[session.studioId]) {
            MusicStudioState.studios[session.studioId].status = 'occupied';
            MusicStudioState.studios[session.studioId].customer = session;
            MusicStudioState.studios[session.studioId].checkInTime = session.checkInTime;
        }
    });
}

/**
 * Initialize Billing Calculator
 */
function initializeBillingCalculator() {
    const baseTimeInput = document.getElementById('baseTime');
    const baseAmountInput = document.getElementById('baseAmount');
    const totalTimeInput = document.getElementById('totalTime');
    
    if (baseTimeInput) {
        baseTimeInput.value = MusicStudioState.billing.baseTime;
        baseTimeInput.addEventListener('input', updateBillingRate);
    }
    
    if (baseAmountInput) {
        baseAmountInput.value = MusicStudioState.billing.baseAmount;
        baseAmountInput.addEventListener('input', updateBillingRate);
    }
    
    if (totalTimeInput) {
        totalTimeInput.addEventListener('input', calculateBill);
    }
    
    // Initial calculation
    calculateBill();
}

/**
 * Update Billing Rate
 */
function updateBillingRate() {
    const baseTime = parseInt(document.getElementById('baseTime')?.value) || 30;
    const baseAmount = parseInt(document.getElementById('baseAmount')?.value) || 2000;
    
    MusicStudioState.billing.baseTime = baseTime;
    MusicStudioState.billing.baseAmount = baseAmount;
    MusicStudioState.billing.ratePerMinute = baseAmount / baseTime;
    
    calculateBill();
}

/**
 * Calculate Bill
 */
function calculateBill() {
    const totalTimeInput = document.getElementById('totalTime');
    const totalBillElement = document.getElementById('totalBill');
    const breakdownElement = document.getElementById('breakdown');
    
    if (!totalTimeInput || !totalBillElement) return;
    
    const totalTime = parseInt(totalTimeInput.value) || 0;
    const { baseTime, baseAmount } = MusicStudioState.billing;
    
    let totalBill = 0;
    let extraTime = 0;
    let extraFee = 0;
    
    if (totalTime > 0) {
        if (totalTime <= baseTime) {
            totalBill = baseAmount;
        } else {
            extraTime = totalTime - baseTime;
            extraFee = (baseAmount / baseTime) * extraTime;
            totalBill = baseAmount + extraFee;
        }
    }
    
    // Update display
    totalBillElement.textContent = VioletMarellaCommon.formatCurrency(totalBill);
    
    // Update breakdown
    if (breakdownElement) {
        if (totalTime > 0) {
            breakdownElement.style.display = 'block';
            document.getElementById('baseTimeDisplay').textContent = `${baseTime} minutes`;
            document.getElementById('baseAmountDisplay').textContent = VioletMarellaCommon.formatCurrency(baseAmount);
            document.getElementById('extraTimeDisplay').textContent = `${extraTime} minutes`;
            document.getElementById('extraFeeDisplay').textContent = VioletMarellaCommon.formatCurrency(extraFee);
        } else {
            breakdownElement.style.display = 'none';
        }
    }
    
    // Visual feedback
    if (totalTime > baseTime) {
        totalBillElement.classList.add('text-warning');
        totalBillElement.classList.remove('text-primary');
    } else {
        totalBillElement.classList.add('text-primary');
        totalBillElement.classList.remove('text-warning');
    }
}

/**
 * Bind Music Studio Events
 */
function bindMusicStudioEvents() {
    // Check-in form submission
    const checkInForm = document.getElementById('checkInForm');
    if (checkInForm) {
        checkInForm.addEventListener('submit', handleCheckIn);
    }
    
    // Studio selection
    const studioCards = document.querySelectorAll('.studio-status-card');
    studioCards.forEach(card => {
        card.addEventListener('click', handleStudioCardClick);
    });
    
    // Tab switches
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', handleTabSwitch);
    });
}

/**
 * Handle Studio Card Click
 */
function handleStudioCardClick(event) {
    const card = event.currentTarget;
    const studioId = card.getAttribute('data-studio-id') || 
                    card.className.match(/studio-([a-d])/)?.[0];
    
    if (studioId && MusicStudioState.studios[studioId]) {
        const studio = MusicStudioState.studios[studioId];
        
        if (studio.status === 'available') {
            selectStudio(studioId);
            const modal = new bootstrap.Modal(document.getElementById('checkInModal'));
            modal.show();
        } else if (studio.status === 'occupied') {
            viewSession(studioId);
        }
    }
}

/**
 * Update Studio Status Cards
 */
function updateStudioStatusCards() {
    Object.keys(MusicStudioState.studios).forEach(studioId => {
        const studio = MusicStudioState.studios[studioId];
        const card = document.querySelector(`.${studioId}, [data-studio-id="${studioId}"]`);
        
        if (card) {
            updateStudioCard(card, studio, studioId);
        }
    });
}

/**
 * Update Single Studio Card
 */
function updateStudioCard(card, studio, studioId) {
    const statusBadge = card.querySelector('.status-badge');
    const studioInfo = card.querySelector('.studio-info');
    const studioActions = card.querySelector('.studio-actions');
    
    // Update status badge
    if (statusBadge) {
        statusBadge.className = `status-badge ${studio.status}`;
        statusBadge.textContent = getStatusText(studio.status);
    }
    
    // Update studio info
    if (studioInfo) {
        if (studio.status === 'occupied' && studio.customer) {
            const duration = getSessionDuration(studio.customer.checkInTime);
            studioInfo.innerHTML = `
                <div class="customer-name">${studio.customer.customerName}</div>
                <div class="session-time">Started: ${VioletMarellaCommon.formatDate(studio.customer.checkInTime, 'time')}</div>
                <div class="duration">Duration: ${duration}</div>
            `;
        } else if (studio.status === 'available') {
            studioInfo.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-door-open"></i>
                    <div>Ready for next customer</div>
                </div>
            `;
        } else if (studio.status === 'maintenance') {
            studioInfo.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-tools"></i>
                    <div>Under maintenance</div>
                </div>
            `;
        }
    }
    
    // Update actions
    if (studioActions) {
        if (studio.status === 'occupied') {
            studioActions.innerHTML = `
                <button class="btn btn-sm btn-outline-primary" onclick="viewSession('${studioId}')">View</button>
                <button class="btn btn-sm btn-success" onclick="checkoutCustomer('${studioId}')">Checkout</button>
            `;
        } else if (studio.status === 'available') {
            studioActions.innerHTML = `
                <button class="btn btn-sm btn-primary" onclick="selectStudio('${studioId}')" data-bs-toggle="modal" data-bs-target="#checkInModal">Check-in</button>
            `;
        } else {
            studioActions.innerHTML = `
                <button class="btn btn-sm btn-outline-secondary" disabled>Unavailable</button>
            `;
        }
    }
}

/**
 * Get Status Text
 */
function getStatusText(status) {
    const statusTexts = {
        'occupied': 'Occupied',
        'available': 'Available',
        'maintenance': 'Maintenance'
    };
    return statusTexts[status] || 'Unknown';
}

/**
 * Get Session Duration
 */
function getSessionDuration(checkInTime) {
    const now = new Date();
    const diff = now - new Date(checkInTime);
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    
    if (hours > 0) {
        return `${hours}h ${minutes}m`;
    }
    return `${minutes}m`;
}

/**
 * Update Active Sessions Tab
 */
function updateActiveSessionsTab() {
    const sessionsContainer = document.querySelector('#sessions .card-body');
    if (!sessionsContainer) return;
    
    if (MusicStudioState.activeSessions.length === 0) {
        sessionsContainer.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-music fa-3x text-muted mb-3"></i>
                <div>No active sessions</div>
                <small class="text-muted">Check in customers to see active sessions here</small>
            </div>
        `;
        return;
    }
    
    sessionsContainer.innerHTML = MusicStudioState.activeSessions.map(session => `
        <div class="session-item" data-session-id="${session.id}">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="customer-info">
                        <div class="customer-avatar">${session.customerAvatar}</div>
                        <div>
                            <div class="fw-semibold">${session.customerName}</div>
                            <small class="text-muted">${session.customerPhone}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="studio-badge">${session.studioName}</div>
                </div>
                <div class="col-md-2">
                    <div class="time-info">
                        <div class="check-in-time">${VioletMarellaCommon.formatDate(session.checkInTime, 'time')}</div>
                        <small class="text-muted">Check-in</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="duration-badge ${getDurationClass(session.checkInTime, session.expectedDuration)}">
                        ${getSessionDuration(session.checkInTime)}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="session-actions">
                        <button class="btn btn-sm btn-outline-info" onclick="showQR('${session.customerId}')" title="Show QR Code">
                            <i class="fas fa-qrcode"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-primary" onclick="extendSession('${session.customerId}')" title="Extend Session">
                            <i class="fas fa-clock"></i>
                        </button>
                        <button class="btn btn-sm btn-success" onclick="checkoutCustomer('${session.customerId}')" title="Checkout">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

/**
 * Get Duration Class
 */
function getDurationClass(checkInTime, expectedDuration) {
    const actualMinutes = Math.floor((new Date() - new Date(checkInTime)) / (1000 * 60));
    
    if (actualMinutes > expectedDuration + 30) return 'overtime';
    if (actualMinutes > expectedDuration) return 'warning';
    return 'active';
}

/**
 * Update Today's Summary
 */
function updateTodaySummary() {
    const summary = calculateTodaySummary();
    
    // Update summary cards
    const summaryItems = [
        { selector: '.summary-item:nth-child(1) .summary-value', value: summary.totalSessions },
        { selector: '.summary-item:nth-child(2) .summary-value', value: summary.totalHours },
        { selector: '.summary-item:nth-child(3) .summary-value', value: VioletMarellaCommon.formatCurrency(summary.revenue) }
    ];
    
    summaryItems.forEach(item => {
        const element = document.querySelector(item.selector);
        if (element) {
            element.textContent = item.value;
        }
    });
}

/**
 * Calculate Today's Summary
 */
function calculateTodaySummary() {
    // Mock data for today's summary
    return {
        totalSessions: 24,
        totalHours: '48h 30m',
        revenue: 96600
    };
}

/**
 * Start Session Timer
 */
function startSessionTimer() {
    // Update session times every minute
    MusicStudioState.updateInterval = setInterval(() => {
        updateStudioStatusCards();
        updateActiveSessionsTab();
    }, 60000); // 60 seconds
}

/**
 * Handle Check In
 */
function handleCheckIn(event) {
    event.preventDefault();
    processCheckIn();
}

/**
 * Process Check In
 */
function processCheckIn() {
    const form = document.getElementById('checkInForm');
    if (!form || !VioletMarellaCommon.validateForm(form)) return;
    
    const formData = new FormData(form);
    const customerName = formData.get('customerName') || form.querySelector('input[placeholder*="name"]')?.value;
    const customerPhone = formData.get('customerPhone') || form.querySelector('input[type="tel"]')?.value;
    const customerEmail = formData.get('customerEmail') || form.querySelector('input[type="email"]')?.value;
    const studioId = formData.get('studio') || document.getElementById('studioSelect')?.value;
    const expectedDuration = parseInt(formData.get('expectedDuration') || '60');
    const generateQR = formData.get('generateQR') !== null || document.getElementById('generateQR')?.checked;
    
    if (!customerName || !customerPhone || !studioId) {
        VioletMarellaCommon.showNotification('Please fill in all required fields', 'error');
        return;
    }
    
    // Check if studio is available
    if (MusicStudioState.studios[studioId]?.status !== 'available') {
        VioletMarellaCommon.showNotification('Selected studio is not available', 'error');
        return;
    }
    
    // Create new session
    const session = {
        id: `session-${Date.now()}`,
        customerId: generateCustomerId(customerName),
        customerName: customerName,
        customerPhone: customerPhone,
        customerEmail: customerEmail,
        customerAvatar: getInitials(customerName),
        studioId: studioId,
        studioName: MusicStudioState.studios[studioId].name,
        checkInTime: new Date(),
        expectedDuration: expectedDuration,
        qrCode: generateQR ? generateQRCode(customerName) : null,
        status: 'active'
    };
    
    // Add to active sessions
    MusicStudioState.activeSessions.push(session);
    
    // Update studio status
    updateStudioStatuses();
    
    // Update displays
    updateStudioStatusCards();
    updateActiveSessionsTab();
    updateTodaySummary();
    
    // Close modal and reset form
    const modal = bootstrap.Modal.getInstance(document.getElementById('checkInModal'));
    modal.hide();
    form.reset();
    
    VioletMarellaCommon.showNotification(`${customerName} checked into ${MusicStudioState.studios[studioId].name}`, 'success');
    
    // Show QR code if requested
    if (generateQR && session.qrCode) {
        setTimeout(() => showQR(session.customerId), 1000);
    }
}

/**
 * Generate Customer ID
 */
function generateCustomerId(customerName) {
    return customerName.toLowerCase().replace(/\s+/g, '-') + '-' + Date.now();
}

/**
 * Get Initials
 */
function getInitials(name) {
    return name.split(' ').map(word => word.charAt(0).toUpperCase()).join('').substring(0, 2);
}

/**
 * Generate QR Code
 */
function generateQRCode(customerName) {
    const qrCode = `QR_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
    
    // Store QR code mapping
    MusicStudioState.qrCodes.set(qrCode, {
        customerName: customerName,
        generatedAt: new Date(),
        used: false
    });
    
    return qrCode;
}

/**
 * Select Studio
 */
function selectStudio(studioId) {
    const studioSelect = document.getElementById('studioSelect');
    if (studioSelect) {
        studioSelect.value = studioId;
    }
}

/**
 * View Session
 */
function viewSession(studioIdOrCustomerId) {
    const session = MusicStudioState.activeSessions.find(s => 
        s.studioId === studioIdOrCustomerId || s.customerId === studioIdOrCustomerId
    );
    
    if (session) {
        const duration = getSessionDuration(session.checkInTime);
        const details = `
            Customer: ${session.customerName}
            Phone: ${session.customerPhone}
            Studio: ${session.studioName}
            Check-in: ${VioletMarellaCommon.formatDate(session.checkInTime, 'datetime')}
            Duration: ${duration}
            Expected: ${session.expectedDuration} minutes
            QR Code: ${session.qrCode || 'Not generated'}
        `;
        
        alert(details); // In production, use a proper modal
    }
}

/**
 * Show QR Code
 */
function showQR(customerId) {
    const session = MusicStudioState.activeSessions.find(s => s.customerId === customerId);
    
    if (session && session.qrCode) {
        // In production, this would display an actual QR code image
        const qrInfo = `
            QR Code for ${session.customerName}
            
            Code: ${session.qrCode}
            Studio: ${session.studioName}
            Check-in: ${VioletMarellaCommon.formatDate(session.checkInTime, 'time')}
            
            Scan this code for checkout
        `;
        
        alert(qrInfo); // In production, use a proper QR code modal
        VioletMarellaCommon.showNotification('QR code displayed', 'info');
    } else {
        VioletMarellaCommon.showNotification('No QR code available for this session', 'warning');
    }
}

/**
 * Extend Session
 */
function extendSession(customerId) {
    const session = MusicStudioState.activeSessions.find(s => s.customerId === customerId);
    
    if (session) {
        const extension = prompt('Enter additional time (minutes):', '30');
        if (extension && !isNaN(extension)) {
            session.expectedDuration += parseInt(extension);
            updateActiveSessionsTab();
            VioletMarellaCommon.showNotification(`Session extended by ${extension} minutes`, 'success');
        }
    }
}

/**
 * Checkout Customer
 */
function checkoutCustomer(studioIdOrCustomerId) {
    const sessionIndex = MusicStudioState.activeSessions.findIndex(s => 
        s.studioId === studioIdOrCustomerId || s.customerId === studioIdOrCustomerId
    );
    
    if (sessionIndex !== -1) {
        const session = MusicStudioState.activeSessions[sessionIndex];
        const duration = Math.floor((new Date() - new Date(session.checkInTime)) / (1000 * 60));
        const bill = calculateSessionBill(duration);
        
        const confirmation = confirm(`
            Checkout ${session.customerName}?
            
            Duration: ${duration} minutes
            Total Bill: ${VioletMarellaCommon.formatCurrency(bill)}
        `);
        
        if (confirmation) {
            // Remove from active sessions
            MusicStudioState.activeSessions.splice(sessionIndex, 1);
            
            // Update studio status
            updateStudioStatuses();
            
            // Update displays
            updateStudioStatusCards();
            updateActiveSessionsTab();
            updateTodaySummary();
            
            VioletMarellaCommon.showNotification(`${session.customerName} checked out - ${VioletMarellaCommon.formatCurrency(bill)}`, 'success');
        }
    }
}

/**
 * Calculate Session Bill
 */
function calculateSessionBill(totalMinutes) {
    const { baseTime, baseAmount } = MusicStudioState.billing;
    
    if (totalMinutes <= baseTime) {
        return baseAmount;
    }
    
    const extraTime = totalMinutes - baseTime;
    const extraFee = (baseAmount / baseTime) * extraTime;
    return baseAmount + extraFee;
}

/**
 * Start Scanner
 */
function startScanner() {
    VioletMarellaCommon.showNotification('QR code scanner would be activated here', 'info');
    
    // Simulate scanner finding a code
    setTimeout(() => {
        const mockScan = {
            code: 'QR_002_1640995800',
            customerName: 'Sarah Johnson',
            scannedAt: new Date()
        };
        
        processScanResult(mockScan);
    }, 2000);
}

/**
 * Process Scan Result
 */
function processScanResult(scanResult) {
    // Add to scan history
    MusicStudioState.scanHistory.unshift(scanResult);
    
    // Update recent scans display
    updateRecentScans();
    
    VioletMarellaCommon.showNotification(`Scanned QR code for ${scanResult.customerName}`, 'success');
}

/**
 * Process Checkout via QR
 */
function processCheckout(customerId) {
    checkoutCustomer(customerId);
}

/**
 * Update Recent Scans
 */
function updateRecentScans() {
    const recentScansContainer = document.querySelector('.recent-scans .scan-item').parentNode;
    if (!recentScansContainer) return;
    
    const scansHTML = MusicStudioState.scanHistory.slice(0, 5).map(scan => `
        <div class="scan-item">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-semibold">${scan.customerName}</div>
                    <small class="text-muted">${scan.code} • Scanned ${VioletMarellaCommon.formatDate(scan.scannedAt, 'time')}</small>
                </div>
                <button class="btn btn-sm btn-outline-primary" onclick="processCheckout('${scan.customerName.toLowerCase().replace(/\s+/g, '-')}')">Process Checkout</button>
            </div>
        </div>
    `).join('');
    
    recentScansContainer.innerHTML = scansHTML || '<div class="text-center text-muted">No recent scans</div>';
}

/**
 * Handle Tab Switch
 */
function handleTabSwitch(event) {
    const tabId = event.target.getAttribute('data-bs-target');
    
    if (tabId === '#billing') {
        // Refresh billing calculator when tab is shown
        calculateBill();
    } else if (tabId === '#sessions') {
        // Refresh sessions when tab is shown
        updateActiveSessionsTab();
    }
}

/**
 * Generate Daily Report
 */
function generateDailyReport() {
    VioletMarellaCommon.showNotification('Generating daily studio report...', 'info');
    setTimeout(() => {
        window.location.href = 'reports.html?type=studio-daily';
    }, 1000);
}

/**
 * Print QR Codes
 */
function printQRCodes() {
    VioletMarellaCommon.showNotification('QR code printing functionality would be implemented here', 'info');
}

/**
 * Export Sessions
 */
function exportSessions() {
    const data = MusicStudioState.activeSessions.map(session => ({
        'Customer Name': session.customerName,
        'Phone': session.customerPhone,
        'Studio': session.studioName,
        'Check-in Time': VioletMarellaCommon.formatDate(session.checkInTime, 'datetime'),
        'Duration': getSessionDuration(session.checkInTime),
        'Expected Duration': `${session.expectedDuration} minutes`,
        'QR Code': session.qrCode || 'Not generated',
        'Status': session.status
    }));
    
    const filename = `studio-sessions-${new Date().toISOString().split('T')[0]}.csv`;
    VioletMarellaCommon.exportToCSV(data, filename);
    VioletMarellaCommon.showNotification('Sessions exported successfully', 'success');
}

/**
 * Cleanup on page unload
 */
window.addEventListener('beforeunload', () => {
    if (MusicStudioState.updateInterval) {
        clearInterval(MusicStudioState.updateInterval);
    }
});

/**
 * Initialize on DOM Content Loaded
 */
document.addEventListener('DOMContentLoaded', function() {
    // Small delay to ensure common.js has loaded
    setTimeout(initializeMusicStudio, 100);
});

// Export music studio functions for global access
window.VioletMarellaMusicStudio = {
    selectStudio,
    viewSession,
    showQR,
    extendSession,
    checkoutCustomer,
    processCheckIn,
    startScanner,
    processCheckout,
    generateDailyReport,
    printQRCodes,
    exportSessions,
    calculateBill
};