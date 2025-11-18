/**
 * VIOLET MARELLA LIMITED - MANAGEMENT APPLICATION
 * Main JavaScript functionality
 */

// Application State
const AppState = {
    isLoggedIn: false,
    currentUser: null,
    currentSection: 'dashboard',
    notifications: []
};

// DOM Elements
const elements = {
    loginScreen: document.getElementById('loginScreen'),
    mainApp: document.getElementById('mainApp'),
    loginForm: document.getElementById('loginForm'),
    logoutBtn: document.getElementById('logoutBtn'),
    navLinks: document.querySelectorAll('.nav-link[data-section]'),
    contentSections: document.querySelectorAll('.content-section'),
    currentPageTitle: document.getElementById('currentPageTitle'),
    currentPageSubtitle: document.getElementById('currentPageSubtitle'),
    baseTime: document.getElementById('baseTime'),
    baseAmount: document.getElementById('baseAmount'),
    totalTime: document.getElementById('totalTime'),
    totalBill: document.getElementById('totalBill')
};

// Page Configuration
const pageConfig = {
    'dashboard': {
        title: 'Dashboard',
        subtitle: 'Welcome to Violet Marella Management Suite'
    },
    'anire-craft-store': {
        title: 'Gift Store',
        subtitle: 'Manage inventory, sales, and customer orders'
    },
    'lounge': {
        title: 'Mini Lounge',
        subtitle: 'POS system and inventory management'
    },
    'photo-studio': {
        title: 'Photo Studio',
        subtitle: 'Studio sessions and time-based billing'
    },
    'prop-rental': {
        title: 'Prop Rental',
        subtitle: 'Manage prop bookings and availability'
    },
    'reports': {
        title: 'Reports',
        subtitle: 'Business analytics and insights'
    },
    'settings': {
        title: 'Settings',
        subtitle: 'System configuration and preferences'
    },
    'users': {
        title: 'User Management',
        subtitle: 'Manage user accounts and permissions'
    }
};

// Hardcoded user credentials (for demo purposes)
const demoUsers = [
    {
        email: 'admin@violetmarella.com',
        password: 'admin123',
        name: 'John Doe',
        role: 'Administrator',
        avatar: 'JD'
    },
    {
        email: 'manager@violetmarella.com',
        password: 'manager123',
        name: 'Jane Smith',
        role: 'Manager',
        avatar: 'JS'
    },
    {
        email: 'staff@violetmarella.com',
        password: 'staff123',
        name: 'Mike Johnson',
        role: 'Staff',
        avatar: 'MJ'
    }
];

/**
 * Initialize Application
 */
function initializeApp() {
    console.log('Initializing Violet Marella Management Application...');
    
    // Check if user is already logged in (for demo, we'll start with login screen)
    showLoginScreen();
    
    // Bind event listeners
    bindEventListeners();
    
    // Initialize billing calculator
    initializeBillingCalculator();
    
    console.log('Application initialized successfully');
}

/**
 * Bind Event Listeners
 */
function bindEventListeners() {
    // Login form submission
    if (elements.loginForm) {
        elements.loginForm.addEventListener('submit', handleLogin);
    }
    
    // Logout button
    if (elements.logoutBtn) {
        elements.logoutBtn.addEventListener('click', handleLogout);
    }
    
    // Navigation links
    elements.navLinks.forEach(link => {
        link.addEventListener('click', handleNavigation);
    });
    
    // Quick action buttons
    const quickActionBtns = document.querySelectorAll('.quick-action[data-section]');
    quickActionBtns.forEach(btn => {
        btn.addEventListener('click', handleQuickAction);
    });
    
    // Billing calculator inputs
    if (elements.totalTime) {
        elements.totalTime.addEventListener('input', calculateBill);
    }
    if (elements.baseTime) {
        elements.baseTime.addEventListener('input', calculateBill);
    }
    if (elements.baseAmount) {
        elements.baseAmount.addEventListener('input', calculateBill);
    }
}

/**
 * Handle Login
 */
function handleLogin(event) {
    event.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    // Show loading state
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing In...';
    submitBtn.disabled = true;
    
    // Simulate authentication delay
    setTimeout(() => {
        const user = authenticateUser(email, password);
        
        if (user) {
            AppState.isLoggedIn = true;
            AppState.currentUser = user;
            showMainApp();
            showNotification('Welcome back, ' + user.name + '!', 'success');
        } else {
            showNotification('Invalid email or password. Please try again.', 'error');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }, 1500);
}

/**
 * Authenticate User
 */
function authenticateUser(email, password) {
    return demoUsers.find(user => 
        user.email === email && user.password === password
    );
}

/**
 * Handle Logout
 */
function handleLogout(event) {
    event.preventDefault();
    
    AppState.isLoggedIn = false;
    AppState.currentUser = null;
    AppState.currentSection = 'dashboard';
    
    showLoginScreen();
    showNotification('You have been logged out successfully.', 'info');
}

/**
 * Show Login Screen
 */
function showLoginScreen() {
    if (elements.loginScreen && elements.mainApp) {
        elements.loginScreen.style.display = 'flex';
        elements.mainApp.classList.remove('active');
        elements.mainApp.style.display = 'none';
    }
}

/**
 * Show Main Application
 */
function showMainApp() {
    if (elements.loginScreen && elements.mainApp) {
        elements.loginScreen.style.display = 'none';
        elements.mainApp.style.display = 'block';
        elements.mainApp.classList.add('active');
        
        // Update user profile display
        updateUserProfile();
        
        // Show default section
        showSection('dashboard');
    }
}

/**
 * Update User Profile Display
 */
function updateUserProfile() {
    if (AppState.currentUser) {
        const userAvatar = document.querySelector('.user-avatar');
        const userName = document.querySelector('.user-profile .fw-semibold');
        const userRole = document.querySelector('.user-profile .text-muted');
        
        if (userAvatar) userAvatar.textContent = AppState.currentUser.avatar;
        if (userName) userName.textContent = AppState.currentUser.name;
        if (userRole) userRole.textContent = AppState.currentUser.role;
    }
}

/**
 * Handle Navigation
 */
function handleNavigation(event) {
    event.preventDefault();
    
    const section = event.currentTarget.getAttribute('data-section');
    if (section) {
        showSection(section);
    }
}

/**
 * Handle Quick Action
 */
function handleQuickAction(event) {
    event.preventDefault();
    
    const section = event.currentTarget.getAttribute('data-section');
    if (section) {
        showSection(section);
    }
}

/**
 * Show Section
 */
function showSection(sectionName) {
    // Update current section
    AppState.currentSection = sectionName;
    
    // Hide all content sections
    elements.contentSections.forEach(section => {
        section.style.display = 'none';
    });
    
    // Show target section
    const targetSection = document.getElementById(sectionName + '-section');
    if (targetSection) {
        targetSection.style.display = 'block';
    }
    
    // Update navigation active state
    elements.navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('data-section') === sectionName) {
            link.classList.add('active');
        }
    });
    
    // Update page title and subtitle
    updatePageHeader(sectionName);
    
    // Load section-specific data
    loadSectionData(sectionName);
}

/**
 * Update Page Header
 */
function updatePageHeader(sectionName) {
    const config = pageConfig[sectionName];
    if (config && elements.currentPageTitle && elements.currentPageSubtitle) {
        elements.currentPageTitle.textContent = config.title;
        elements.currentPageSubtitle.textContent = config.subtitle;
    }
}

/**
 * Load Section Data
 */
function loadSectionData(sectionName) {
    switch (sectionName) {
        case 'dashboard':
            loadDashboardData();
            break;
        case 'anire-craft-store':
            loadGiftStoreData();
            break;
        case 'lounge':
            loadLoungeData();
            break;
        case 'photo-studio':
            loadPhotoStudioData();
            break;
        case 'prop-rental':
            loadPropRentalData();
            break;
        default:
            console.log('Loading data for section:', sectionName);
    }
}

/**
 * Load Dashboard Data
 */
function loadDashboardData() {
    // In a real application, this would fetch data from an API
    console.log('Loading dashboard data...');
    
    // Animate statistics cards
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.3s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 50);
        }, index * 100);
    });
}

/**
 * Load Gift Store Data
 */
function loadGiftStoreData() {
    console.log('Loading gift store data...');
    // Here you would typically fetch inventory data from an API
}

/**
 * Load Lounge Data
 */
function loadLoungeData() {
    console.log('Loading lounge data...');
    // Here you would typically fetch POS and inventory data from an API
}

/**
 * Load Photo Studio Data
 */
function loadPhotoStudioData() {
    console.log('Loading music studio data...');
    // Here you would typically fetch active sessions and billing data from an API
    
    // Start time updates for active sessions
    updateActiveSessionTimes();
}

/**
 * Load Prop Rental Data
 */
function loadPropRentalData() {
    console.log('Loading prop rental data...');
    // Here you would typically fetch rental data from an API
}

/**
 * Initialize Billing Calculator
 */
function initializeBillingCalculator() {
    calculateBill(); // Initial calculation
}

/**
 * Calculate Studio Bill
 */
function calculateBill() {
    if (!elements.baseTime || !elements.baseAmount || !elements.totalTime || !elements.totalBill) {
        return;
    }
    
    const baseTime = parseInt(elements.baseTime.value) || 30;
    const baseAmount = parseInt(elements.baseAmount.value) || 2000;
    const totalTime = parseInt(elements.totalTime.value) || 0;
    
    let totalBill = 0;
    
    if (totalTime > 0) {
        if (totalTime <= baseTime) {
            totalBill = baseAmount;
        } else {
            const extraTime = totalTime - baseTime;
            const extraTimeFee = (baseAmount / baseTime) * extraTime;
            totalBill = baseAmount + extraTimeFee;
        }
    }
    
    // Format the bill amount
    elements.totalBill.textContent = '₦' + totalBill.toLocaleString();
    
    // Add visual feedback
    if (totalTime > baseTime) {
        elements.totalBill.classList.add('text-warning');
        elements.totalBill.classList.remove('text-primary');
    } else {
        elements.totalBill.classList.add('text-primary');
        elements.totalBill.classList.remove('text-warning');
    }
}

/**
 * Update Active Session Times
 */
function updateActiveSessionTimes() {
    // This would typically track real session times
    // For demo purposes, we'll just simulate time updates
    console.log('Updating active session times...');
}

/**
 * Show Notification
 */
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${getBootstrapAlertClass(type)} alert-dismissible fade show position-fixed`;
    notification.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    
    notification.innerHTML = `
        <i class="fas ${getNotificationIcon(type)} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove notification after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

/**
 * Get Bootstrap Alert Class
 */
function getBootstrapAlertClass(type) {
    const classes = {
        'success': 'success',
        'error': 'danger',
        'warning': 'warning',
        'info': 'info'
    };
    return classes[type] || 'info';
}

/**
 * Get Notification Icon
 */
function getNotificationIcon(type) {
    const icons = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
    };
    return icons[type] || 'fa-info-circle';
}

/**
 * QR Code Generation (Placeholder)
 */
function generateQRCode(customerId, customerName) {
    // This would integrate with a QR code library in a real application
    console.log(`Generating QR code for customer: ${customerName} (ID: ${customerId})`);
    
    // For demo purposes, return a placeholder
    return {
        id: customerId,
        name: customerName,
        checkInTime: new Date(),
        qrCode: `QR_${customerId}_${Date.now()}`
    };
}

/**
 * Barcode Scanner (Placeholder)
 */
function startBarcodeScanner() {
    // This would integrate with a barcode scanning library in a real application
    console.log('Starting barcode scanner...');
    showNotification('Barcode scanner would be activated here in a real implementation.', 'info');
}

/**
 * Print Receipt/Tag (Placeholder)
 */
function printCustomerTag(customer) {
    // This would integrate with a printing service in a real application
    console.log('Printing customer tag:', customer);
    showNotification(`Customer tag printed for ${customer.name}`, 'success');
}

/**
 * Export Data (Placeholder)
 */
function exportData(type, format = 'csv') {
    console.log(`Exporting ${type} data in ${format} format`);
    showNotification(`${type} data export would be generated here.`, 'info');
}

/**
 * Responsive Menu Toggle (for mobile)
 */
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('active');
    }
}

// Initialize application when DOM is loaded
document.addEventListener('DOMContentLoaded', initializeApp);

// Handle window resize for responsive design
window.addEventListener('resize', () => {
    // Add any responsive handling code here
    console.log('Window resized');
});

// Export functions for global access (if needed)
window.VioletMarellaApp = {
    generateQRCode,
    startBarcodeScanner,
    printCustomerTag,
    exportData,
    toggleSidebar,
    showNotification
};