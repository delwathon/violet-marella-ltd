/**
 * VIOLET MARELLA LIMITED - COMMON FUNCTIONALITY
 * Shared JavaScript functions across all pages
 */

// Application State
const AppState = {
    isLoggedIn: false,
    currentUser: null,
    currentPage: '',
    notifications: []
};

// Common DOM Elements
const commonElements = {
    sidebar: document.getElementById('sidebar'),
    topNavbar: document.getElementById('topNavbar')
};

/**
 * Initialize Common Components
 */
function initializeCommon() {
    console.log('Initializing common components...');
    
    // Check authentication first
    checkAuthentication();
    
    // Load components in sequence
    Promise.all([
        loadSidebar(),
        loadTopNavbar()
    ]).then(() => {
        // Update current page after components are loaded
        updateCurrentPage();
        
        // Bind common event listeners
        bindCommonEvents();
        
        // Show the main app
        showMainApp();
        
        console.log('Common components initialized successfully');
    }).catch(error => {
        console.error('Error initializing components:', error);
        // Show the app anyway with fallback
        showMainApp();
    });
}

/**
 * Check Authentication Status
 */
function checkAuthentication() {
    const userData = sessionStorage.getItem('violetMarellaUser') || localStorage.getItem('violetMarellaUser');
    const currentPage = window.location.pathname.split('/').pop();
    
    if (userData) {
        try {
            AppState.isLoggedIn = true;
            AppState.currentUser = JSON.parse(userData);
            console.log('User authenticated:', AppState.currentUser.name);
        } catch (e) {
            console.error('Error parsing user data:', e);
            // Clear invalid data and redirect to login
            sessionStorage.removeItem('violetMarellaUser');
            localStorage.removeItem('violetMarellaUser');
            if (currentPage !== 'login' && currentPage !== '') {
                window.location.href = 'login';
            }
            return;
        }
    } else if (currentPage !== 'login' && currentPage !== '') {
        // Redirect to login if not authenticated and not on login page
        console.log('No authentication found, redirecting to login');
        window.location.href = 'login';
        return;
    }
}

/**
 * Show Main App
 */
function showMainApp() {
    const mainApp = document.getElementById('mainApp') || document.querySelector('.main-app');
    if (mainApp) {
        mainApp.style.display = 'block';
        mainApp.classList.add('active');
        console.log('Main app shown');
    }
}

/**
 * Load Sidebar Component
 */
function loadSidebar() {
    return new Promise((resolve) => {
        if (!commonElements.sidebar) {
            console.warn('Sidebar element not found');
            resolve();
            return;
        }
        
        const sidebarHTML = `
            <div class="sidebar-header">
                <a href="dashboard" class="sidebar-brand">Violet Marella</a>
                <small class="sidebar-subtitle">Management Suite</small>
            </div>
            
            <div class="sidebar-nav">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard" data-page="dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gift-store" data-page="gift-store">
                            <i class="fas fa-gift"></i>
                            Gift Store
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="lounge" data-page="lounge">
                            <i class="fas fa-shopping-cart"></i>
                            Mini Lounge
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="music-studio" data-page="music-studio">
                            <i class="fas fa-music"></i>
                            Music Studio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="instrument-rental" data-page="instrument-rental">
                            <i class="fas fa-guitar"></i>
                            Instrument Rental
                        </a>
                    </li>
                </ul>
                
                <hr class="sidebar-divider">
                
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="reports" data-page="reports">
                            <i class="fas fa-chart-bar"></i>
                            Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings" data-page="settings">
                            <i class="fas fa-cog"></i>
                            Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users" data-page="users">
                            <i class="fas fa-users"></i>
                            User Management
                        </a>
                    </li>
                </ul>
            </div>
        `;
        
        commonElements.sidebar.innerHTML = sidebarHTML;
        console.log('Sidebar loaded');
        resolve();
    });
}

/**
 * Load Top Navigation Component
 */
function loadTopNavbar() {
    return new Promise((resolve) => {
        if (!commonElements.topNavbar) {
            console.warn('Top navbar element not found');
            resolve();
            return;
        }
        
        const user = AppState.currentUser || { name: 'Guest', role: 'User', avatar: 'G' };
        
        const topNavHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <button class="btn btn-link d-md-none p-0 me-3" onclick="VioletMarellaCommon.toggleSidebar()">
                        <i class="fas fa-bars fa-lg"></i>
                    </button>
                    <h5 class="mb-0" id="currentPageTitle">${getPageTitle()}</h5>
                    <small class="text-muted" id="currentPageSubtitle">${getPageSubtitle()}</small>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="dropdown me-3">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell me-1"></i>
                            Notifications
                            <span class="badge bg-danger ms-1">3</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Low stock alert - Gift items</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-clock text-info me-2"></i>Studio booking reminder</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-chart-bar text-success me-2"></i>Monthly report ready</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center" href="notifications">View All Notifications</a></li>
                        </ul>
                    </div>
                    
                    <div class="user-profile dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                            <div class="user-avatar">${user.avatar}</div>
                            <div class="d-none d-md-block">
                                <div class="fw-semibold">${user.name}</div>
                                <small class="text-muted">${user.role}</small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="settings"><i class="fas fa-cog me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" onclick="VioletMarellaCommon.logout()"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        `;
        
        commonElements.topNavbar.innerHTML = topNavHTML;
        console.log('Top navbar loaded');
        resolve();
    });
}

/**
 * Update Current Page Active State
 */
function updateCurrentPage() {
    const currentPage = getCurrentPageName();
    AppState.currentPage = currentPage;
    
    // Update sidebar active state
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('data-page') === currentPage) {
            link.classList.add('active');
        }
    });
    
    // Update page title and subtitle
    updatePageHeader(currentPage);
    console.log('Current page updated:', currentPage);
}

/**
 * Update Page Header
 */
function updatePageHeader(currentPage) {
    const titleElement = document.getElementById('currentPageTitle');
    const subtitleElement = document.getElementById('currentPageSubtitle');
    
    const pageInfo = getPageInfo(currentPage);
    
    if (titleElement) titleElement.textContent = pageInfo.title;
    if (subtitleElement) subtitleElement.textContent = pageInfo.subtitle;
}

/**
 * Get Current Page Name
 */
function getCurrentPageName() {
    const pathname = window.location.pathname;
    const filename = pathname.split('/').pop();
    return filename.replace('', '') || 'dashboard';
}

/**
 * Get Page Title
 */
function getPageTitle() {
    const pageTitles = {
        'dashboard': 'Dashboard',
        'gift-store': 'Gift Store',
        'lounge': 'Mini Lounge',
        'music-studio': 'Music Studio',
        'instrument-rental': 'Instrument Rental',
        'reports': 'Reports',
        'settings': 'Settings',
        'users': 'User Management'
    };
    
    return pageTitles[getCurrentPageName()] || 'Dashboard';
}

/**
 * Get Page Subtitle
 */
function getPageSubtitle() {
    const pageSubtitles = {
        'dashboard': 'Welcome to Violet Marella Management Suite',
        'gift-store': 'Manage inventory, sales, and customer orders',
        'lounge': 'POS system and inventory management',
        'music-studio': 'Studio sessions and time-based billing',
        'instrument-rental': 'Manage instrument bookings and availability',
        'reports': 'Business analytics and insights',
        'settings': 'System configuration and preferences',
        'users': 'Manage user accounts and permissions'
    };
    
    return pageSubtitles[getCurrentPageName()] || 'Business management platform';
}

/**
 * Get Page Information
 */
function getPageInfo(pageName) {
    return {
        title: getPageTitle(),
        subtitle: getPageSubtitle()
    };
}

/**
 * Bind Common Event Listeners
 */
function bindCommonEvents() {
    // Handle window resize
    window.addEventListener('resize', handleResize);
    
    // Handle navigation clicks
    document.addEventListener('click', handleNavigation);
    
    // Handle form submissions
    document.addEventListener('submit', handleFormSubmission);
}

/**
 * Handle Window Resize
 */
function handleResize() {
    // Auto-close sidebar on mobile when resizing to larger screen
    if (window.innerWidth >= 768) {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            sidebar.classList.remove('mobile-open');
        }
    }
}

/**
 * Handle Navigation Clicks
 */
function handleNavigation(event) {
    // Handle quick action buttons
    if (event.target.classList.contains('quick-action') || event.target.closest('.quick-action')) {
        const target = event.target.classList.contains('quick-action') ? event.target : event.target.closest('.quick-action');
        const href = target.getAttribute('href');
        if (href && href !== '#') {
            event.preventDefault();
            window.location.href = href;
        }
    }
}

/**
 * Handle Form Submissions
 */
function handleFormSubmission(event) {
    // Add loading state to submit buttons
    const submitBtn = event.target.querySelector('button[type="submit"]');
    if (submitBtn && !submitBtn.disabled) {
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        submitBtn.disabled = true;
        
        // Reset after 3 seconds (in real app, this would be after API response)
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 3000);
    }
}

/**
 * Toggle Sidebar (Mobile)
 */
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('mobile-open');
    }
}

/**
 * Logout Function
 */
function logout() {
    // Clear session data
    sessionStorage.removeItem('violetMarellaUser');
    localStorage.removeItem('violetMarellaUser');
    AppState.isLoggedIn = false;
    AppState.currentUser = null;
    
    // Redirect to login
    window.location.href = 'login';
}

/**
 * Show Notification
 */
function showNotification(message, type = 'info', duration = 5000) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${getBootstrapAlertClass(type)} alert-dismissible fade show position-fixed`;
    notification.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        max-width: 400px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border: none;
        border-radius: 12px;
    `;
    
    notification.innerHTML = `
        <i class="fas ${getNotificationIcon(type)} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove notification
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, duration);
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
 * Format Currency
 */
function formatCurrency(amount, currency = '₦') {
    if (typeof amount !== 'number') {
        amount = parseFloat(amount) || 0;
    }
    return `${currency}${amount.toLocaleString()}`;
}

/**
 * Format Date
 */
function formatDate(date, format = 'short') {
    if (!date) return 'N/A';
    
    const dateObj = new Date(date);
    if (isNaN(dateObj.getTime())) return 'Invalid Date';
    
    const options = {
        'short': { year: 'numeric', month: 'short', day: 'numeric' },
        'long': { year: 'numeric', month: 'long', day: 'numeric' },
        'time': { hour: '2-digit', minute: '2-digit' },
        'datetime': { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }
    };
    
    return new Intl.DateTimeFormat('en-US', options[format] || options.short).format(dateObj);
}

/**
 * Validate Form
 */
function validateForm(formElement) {
    if (!formElement) return false;
    
    const requiredFields = formElement.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

/**
 * Show Loading State
 */
function showLoading(element, text = 'Loading...') {
    if (element) {
        const originalText = element.innerHTML;
        element.setAttribute('data-original-text', originalText);
        element.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${text}`;
        element.disabled = true;
    }
}

/**
 * Hide Loading State
 */
function hideLoading(element) {
    if (element) {
        const originalText = element.getAttribute('data-original-text');
        if (originalText) {
            element.innerHTML = originalText;
            element.removeAttribute('data-original-text');
        }
        element.disabled = false;
    }
}

/**
 * Generate Random ID
 */
function generateId(prefix = '') {
    return prefix + Math.random().toString(36).substr(2, 9);
}

/**
 * Debounce Function
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Export Data to CSV
 */
function exportToCSV(data, filename) {
    if (!data || !data.length) {
        showNotification('No data to export', 'warning');
        return;
    }
    
    const csv = convertToCSV(data);
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    window.URL.revokeObjectURL(url);
}

/**
 * Convert Array to CSV
 */
function convertToCSV(array) {
    if (!array.length) return '';
    
    const headers = Object.keys(array[0]);
    const csvContent = [
        headers.join(','),
        ...array.map(row => headers.map(header => {
            const value = row[header] || '';
            // Escape quotes and wrap in quotes if contains comma or quote
            const escaped = String(value).replace(/"/g, '""');
            return escaped.includes(',') || escaped.includes('"') ? `"${escaped}"` : escaped;
        }).join(','))
    ];
    
    return csvContent.join('\n');
}

/**
 * Initialize on DOM Content Loaded
 */
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize common components if not on login page
    const currentPage = getCurrentPageName();
    if (currentPage !== 'login') {
        // Small delay to ensure DOM is fully ready
        setTimeout(initializeCommon, 50);
    }
});

// Export common functions for global access
window.VioletMarellaCommon = {
    showNotification,
    formatCurrency,
    formatDate,
    validateForm,
    showLoading,
    hideLoading,
    generateId,
    debounce,
    exportToCSV,
    toggleSidebar,
    logout,
    checkAuthentication,
    initializeCommon
};