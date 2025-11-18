/**
 * VIOLET MARELLA LIMITED - AUTHENTICATION
 * Login and authentication functionality
 */

// Demo user credentials (in production, this would be handled by a backend API)
const demoUsers = [
    {
        email: 'admin@violetmarella.com',
        password: 'admin123',
        name: 'John Doe',
        role: 'Administrator',
        avatar: 'JD',
        permissions: ['all']
    },
    {
        email: 'manager@violetmarella.com',
        password: 'manager123',
        name: 'Jane Smith',
        role: 'Manager',
        avatar: 'JS',
        permissions: ['dashboard', 'anire-craft-store', 'lounge', 'photo-studio', 'reports']
    },
    {
        email: 'staff@violetmarella.com',
        password: 'staff123',
        name: 'Mike Johnson',
        role: 'Staff',
        avatar: 'MJ',
        permissions: ['dashboard', 'anire-craft-store', 'lounge', 'photo-studio']
    }
];

// DOM Elements
const elements = {
    loginForm: document.getElementById('loginForm'),
    emailInput: document.getElementById('email'),
    passwordInput: document.getElementById('password'),
    rememberMeCheckbox: document.getElementById('rememberMe')
};

/**
 * Initialize Authentication
 */
function initializeAuth() {
    console.log('Initializing authentication...');
    
    // Check if user is already logged in
    checkExistingSession();
    
    // Bind event listeners
    bindAuthEvents();
    
    // Add demo credentials helper
    addDemoCredentialsHelper();
    
    console.log('Authentication initialized');
}

/**
 * Check Existing Session
 */
function checkExistingSession() {
    const userData = sessionStorage.getItem('violetMarellaUser') || localStorage.getItem('violetMarellaUser');
    
    if (userData) {
        // User is already logged in, redirect to dashboard
        window.location.href = 'dashboard.html';
    }
}

/**
 * Bind Authentication Events
 */
function bindAuthEvents() {
    if (elements.loginForm) {
        elements.loginForm.addEventListener('submit', handleLogin);
    }
    
    // Add enter key support for password field
    if (elements.passwordInput) {
        elements.passwordInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                handleLogin(event);
            }
        });
    }
    
    // Add demo credential buttons
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('demo-credential-btn')) {
            fillDemoCredentials(event.target.dataset.email, event.target.dataset.password);
        }
    });
}

/**
 * Handle Login Form Submission
 */
function handleLogin(event) {
    event.preventDefault();
    
    const email = elements.emailInput.value.trim();
    const password = elements.passwordInput.value.trim();
    const rememberMe = elements.rememberMeCheckbox?.checked || false;
    
    // Validate inputs
    if (!email || !password) {
        showNotification('Please enter both email and password.', 'error');
        return;
    }
    
    // Show loading state
    const submitBtn = elements.loginForm.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing In...';
    submitBtn.disabled = true;
    
    // Simulate authentication delay (in production, this would be an API call)
    setTimeout(() => {
        const user = authenticateUser(email, password);
        
        if (user) {
            // Authentication successful
            storeUserSession(user, rememberMe);
            showNotification(`Welcome back, ${user.name}!`, 'success');
            
            // Redirect to dashboard after short delay
            setTimeout(() => {
                window.location.href = 'dashboard.html';
            }, 1000);
        } else {
            // Authentication failed
            showNotification('Invalid email or password. Please try again.', 'error');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            // Add shake animation to form
            elements.loginForm.classList.add('shake');
            setTimeout(() => {
                elements.loginForm.classList.remove('shake');
            }, 500);
        }
    }, 1500);
}

/**
 * Authenticate User
 */
function authenticateUser(email, password) {
    return demoUsers.find(user => 
        user.email.toLowerCase() === email.toLowerCase() && 
        user.password === password
    );
}

/**
 * Store User Session
 */
function storeUserSession(user, rememberMe) {
    const userData = {
        email: user.email,
        name: user.name,
        role: user.role,
        avatar: user.avatar,
        permissions: user.permissions,
        loginTime: new Date().toISOString()
    };
    
    // Store in session or local storage based on remember me
    if (rememberMe) {
        localStorage.setItem('violetMarellaUser', JSON.stringify(userData));
    } else {
        sessionStorage.setItem('violetMarellaUser', JSON.stringify(userData));
    }
}

/**
 * Fill Demo Credentials
 */
function fillDemoCredentials(email, password) {
    if (elements.emailInput && elements.passwordInput) {
        elements.emailInput.value = email;
        elements.passwordInput.value = password;
        
        // Add visual feedback
        elements.emailInput.focus();
        elements.passwordInput.focus();
        elements.emailInput.focus(); // Return focus to email field
    }
}

/**
 * Add Demo Credentials Helper
 */
function addDemoCredentialsHelper() {
    // This function adds click handlers to the demo credentials in the login form
    const demoCredentialElements = document.querySelectorAll('.demo-credentials small');
    
    demoCredentialElements.forEach(element => {
        element.style.cursor = 'pointer';
        element.style.transition = 'all 0.2s ease';
        
        element.addEventListener('mouseenter', function() {
            this.style.background = 'rgba(111, 66, 193, 0.1)';
            this.style.borderRadius = '4px';
            this.style.padding = '2px 4px';
        });
        
        element.addEventListener('mouseleave', function() {
            this.style.background = 'transparent';
            this.style.padding = '0';
        });
        
        element.addEventListener('click', function() {
            const text = this.textContent;
            const emailMatch = text.match(/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/);
            const passwordMatch = text.match(/\/\s*(\w+)/);
            
            if (emailMatch && passwordMatch) {
                fillDemoCredentials(emailMatch[1], passwordMatch[1]);
                showNotification('Demo credentials filled. Click Sign In to continue.', 'info');
            }
        });
    });
}

/**
 * Show Notification (Simple version for auth page)
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
 * Logout Function
 */
function logout() {
    // Clear all stored user data
    sessionStorage.removeItem('violetMarellaUser');
    localStorage.removeItem('violetMarellaUser');
    
    // Redirect to login page
    window.location.href = 'index.html';
}

/**
 * Password Reset (Placeholder)
 */
function resetPassword(email) {
    // In a real application, this would send a reset email
    console.log('Password reset requested for:', email);
    showNotification('Password reset instructions would be sent to your email.', 'info');
}

/**
 * Add CSS for shake animation
 */
function addAuthStyles() {
    const style = document.createElement('style');
    style.textContent = `
        .shake {
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .demo-credentials {
            transition: all 0.2s ease;
        }
        
        .demo-credentials:hover {
            background: rgba(111, 66, 193, 0.05);
            border-radius: 8px;
        }
    `;
    document.head.appendChild(style);
}

/**
 * Initialize on DOM Content Loaded
 */
document.addEventListener('DOMContentLoaded', function() {
    initializeAuth();
    addAuthStyles();
});

// Export auth functions for global access
window.VioletMarellaAuth = {
    logout,
    resetPassword,
    fillDemoCredentials
};