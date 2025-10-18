/**
 * VIOLET MARELLA LIMITED - DASHBOARD FUNCTIONALITY
 * Dashboard-specific JavaScript functionality
 */

// Dashboard state and data
const DashboardState = {
    stats: {
        totalRevenue: 2400000,
        totalProducts: 847,
        studioSessions: 156,
        activeRentals: 42
    },
    activities: [],
    chartData: {},
    refreshInterval: null
};

// DOM Elements
const dashboardElements = {
    statCards: document.querySelectorAll('.stat-card'),
    activityFeed: document.querySelector('.activity-feed'),
    quickActionBtns: document.querySelectorAll('.quick-action'),
    moduleCards: document.querySelectorAll('.module-card')
};

/**
 * Initialize Dashboard
 */
function initializeDashboard() {
    console.log('Initializing dashboard...');
    
    // Load dashboard data
    loadDashboardData();
    
    // Animate statistics cards
    animateStatCards();
    
    // Load recent activities
    loadRecentActivities();
    
    // Bind dashboard events
    bindDashboardEvents();
    
    // Start real-time updates
    startRealTimeUpdates();
    
    console.log('Dashboard initialized successfully');
}

/**
 * Load Dashboard Data
 */
function loadDashboardData() {
    // Simulate loading dashboard statistics
    const stats = getDashboardStats();
    updateStatCards(stats);
    
    // Load business metrics
    loadBusinessMetrics();
    
    // Update module status
    updateModuleStatus();
}

/**
 * Get Dashboard Statistics
 */
function getDashboardStats() {
    // In production, this would fetch from an API
    return {
        totalRevenue: {
            value: 2400000,
            change: 12,
            trend: 'up'
        },
        totalProducts: {
            value: 847,
            lowStock: 23,
            trend: 'warning'
        },
        studioSessions: {
            value: 156,
            change: 8,
            trend: 'up'
        },
        activeRentals: {
            value: 42,
            dueToday: 12,
            trend: 'stable'
        }
    };
}

/**
 * Update Statistics Cards
 */
function updateStatCards(stats) {
    const cards = [
        {
            selector: '.stat-card:nth-child(1)',
            value: formatCurrency(stats.totalRevenue.value),
            change: `+${stats.totalRevenue.change}% from last month`,
            trend: 'success'
        },
        {
            selector: '.stat-card:nth-child(2)',
            value: stats.totalProducts.value.toLocaleString(),
            change: `${stats.totalProducts.lowStock} low stock`,
            trend: 'warning'
        },
        {
            selector: '.stat-card:nth-child(3)',
            value: stats.studioSessions.value.toLocaleString(),
            change: `+${stats.studioSessions.change}% this week`,
            trend: 'success'
        },
        {
            selector: '.stat-card:nth-child(4)',
            value: stats.activeRentals.value.toLocaleString(),
            change: `${stats.activeRentals.dueToday} due today`,
            trend: 'info'
        }
    ];

    cards.forEach((card, index) => {
        const cardElement = document.querySelector(card.selector);
        if (cardElement) {
            const valueElement = cardElement.querySelector('.stat-value');
            const changeElement = cardElement.querySelector('.stat-change');
            
            if (valueElement) {
                valueElement.textContent = card.value;
            }
            
            if (changeElement) {
                changeElement.innerHTML = getChangeIcon(card.trend) + ' ' + card.change;
                changeElement.className = `stat-change text-${card.trend}`;
            }
        }
    });
}

/**
 * Get Change Icon
 */
function getChangeIcon(trend) {
    const icons = {
        'success': '<i class="fas fa-arrow-up"></i>',
        'warning': '<i class="fas fa-exclamation-triangle"></i>',
        'info': '<i class="fas fa-clock"></i>',
        'danger': '<i class="fas fa-arrow-down"></i>'
    };
    return icons[trend] || '<i class="fas fa-minus"></i>';
}

/**
 * Animate Statistics Cards
 */
function animateStatCards() {
    const cards = document.querySelectorAll('.stat-card');
    
    cards.forEach((card, index) => {
        // Initial state
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease';
        
        // Animate with delay
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 150);
    });
}

/**
 * Load Recent Activities
 */
function loadRecentActivities() {
    const activities = getRecentActivities();
    updateActivityFeed(activities);
}

/**
 * Get Recent Activities
 */
function getRecentActivities() {
    // Sample activity data - in production, this would come from an API
    return [
        {
            id: 1,
            type: 'sale',
            icon: 'fas fa-shopping-cart',
            iconColor: 'success',
            title: 'New sale recorded',
            description: "Valentine's Gift Box sold for ₦5,500",
            timestamp: new Date(Date.now() - 2 * 60 * 1000) // 2 minutes ago
        },
        {
            id: 2,
            type: 'alert',
            icon: 'fas fa-exclamation-triangle',
            iconColor: 'warning',
            title: 'Low stock alert',
            description: 'Birthday Card Set - Only 5 items remaining',
            timestamp: new Date(Date.now() - 15 * 60 * 1000) // 15 minutes ago
        },
        {
            id: 3,
            type: 'studio',
            icon: 'fas fa-music',
            iconColor: 'info',
            title: 'Studio session completed',
            description: 'John Smith - 2 hours, ₦4,000 billed',
            timestamp: new Date(Date.now() - 60 * 60 * 1000) // 1 hour ago
        },
        {
            id: 4,
            type: 'rental',
            icon: 'fas fa-guitar',
            iconColor: 'primary',
            title: 'Instrument rental',
            description: 'Guitar rented to Sarah Johnson for 3 days',
            timestamp: new Date(Date.now() - 2 * 60 * 60 * 1000) // 2 hours ago
        },
        {
            id: 5,
            type: 'user',
            icon: 'fas fa-user-plus',
            iconColor: 'success',
            title: 'New customer registered',
            description: 'Mike Wilson signed up for studio membership',
            timestamp: new Date(Date.now() - 4 * 60 * 60 * 1000) // 4 hours ago
        }
    ];
}

/**
 * Update Activity Feed
 */
function updateActivityFeed(activities) {
    const container = document.querySelector('.activity-feed, .card-body');
    if (!container || !activities.length) return;
    
    const activitiesHTML = activities.map(activity => `
        <div class="activity-item" data-activity-id="${activity.id}">
            <div class="activity-icon bg-${activity.iconColor}">
                <i class="${activity.icon}"></i>
            </div>
            <div class="activity-content">
                <h6>${activity.title}</h6>
                <p class="text-muted mb-0">${activity.description}</p>
                <small class="text-muted">${getTimeAgo(activity.timestamp)}</small>
            </div>
        </div>
    `).join('');
    
    // Find the activities container (could be in different places)
    let activitiesContainer = container.querySelector('.activities-list');
    if (!activitiesContainer) {
        // Create activities container if it doesn't exist
        activitiesContainer = document.createElement('div');
        activitiesContainer.className = 'activities-list';
        container.appendChild(activitiesContainer);
    }
    
    activitiesContainer.innerHTML = activitiesHTML;
}

/**
 * Get Time Ago String
 */
function getTimeAgo(timestamp) {
    const now = new Date();
    const diff = now - timestamp;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);
    
    if (minutes < 1) return 'Just now';
    if (minutes < 60) return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    if (hours < 24) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    return `${days} day${days > 1 ? 's' : ''} ago`;
}

/**
 * Load Business Metrics
 */
function loadBusinessMetrics() {
    const metrics = {
        giftStore: {
            revenue: 450000,
            orders: 234,
            avgOrderValue: 1923
        },
        lounge: {
            revenue: 1200000,
            transactions: 1847,
            avgTransaction: 650
        },
        musicStudio: {
            revenue: 750000,
            sessions: 156,
            avgSession: 4808
        }
    };
    
    // Update module cards with metrics
    updateModuleMetrics(metrics);
}

/**
 * Update Module Metrics
 */
function updateModuleMetrics(metrics) {
    // This would update the module cards with current metrics
    console.log('Updated module metrics:', metrics);
}

/**
 * Update Module Status
 */
function updateModuleStatus() {
    const modules = document.querySelectorAll('.module-card');
    
    modules.forEach(module => {
        // Add loading animation initially
        module.style.opacity = '0';
        module.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            module.style.transition = 'all 0.6s ease';
            module.style.opacity = '1';
            module.style.transform = 'translateY(0)';
        }, Math.random() * 500);
    });
}

/**
 * Bind Dashboard Events
 */
function bindDashboardEvents() {
    // Module card hover effects
    const moduleCards = document.querySelectorAll('.module-card');
    moduleCards.forEach(card => {
        card.addEventListener('mouseenter', handleModuleCardHover);
        card.addEventListener('mouseleave', handleModuleCardLeave);
    });
    
    // Quick action buttons
    const quickActions = document.querySelectorAll('.quick-action');
    quickActions.forEach(btn => {
        btn.addEventListener('click', handleQuickAction);
    });
    
    // Stat cards click events
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.addEventListener('click', handleStatCardClick);
    });
    
    // Activity refresh
    const refreshBtn = document.querySelector('[onclick="refreshActivities()"]');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', refreshActivities);
    }
}

/**
 * Handle Module Card Hover
 */
function handleModuleCardHover(event) {
    const card = event.currentTarget;
    card.style.transform = 'translateY(-8px) scale(1.02)';
    card.style.boxShadow = '0 20px 60px rgba(0, 0, 0, 0.2)';
}

/**
 * Handle Module Card Leave
 */
function handleModuleCardLeave(event) {
    const card = event.currentTarget;
    card.style.transform = 'translateY(0) scale(1)';
    card.style.boxShadow = '0 8px 32px rgba(0, 0, 0, 0.1)';
}

/**
 * Handle Quick Action Click
 */
function handleQuickAction(event) {
    const button = event.currentTarget;
    const href = button.getAttribute('href') || button.closest('a')?.getAttribute('href');
    
    if (href && href !== '#') {
        // Add click animation
        button.style.transform = 'scale(0.95)';
        setTimeout(() => {
            button.style.transform = 'scale(1)';
            window.location.href = href;
        }, 150);
    }
}

/**
 * Handle Stat Card Click
 */
function handleStatCardClick(event) {
    const card = event.currentTarget;
    const cardIndex = Array.from(card.parentNode.children).indexOf(card);
    
    // Navigate to relevant page based on card
    const pages = ['reports.html', 'gift-store.html', 'music-studio.html', 'instrument-rental.html'];
    if (pages[cardIndex]) {
        window.location.href = pages[cardIndex];
    }
}

/**
 * Start Real-time Updates
 */
function startRealTimeUpdates() {
    // Update dashboard every 30 seconds
    DashboardState.refreshInterval = setInterval(() => {
        updateDashboardData();
    }, 30000);
}

/**
 * Update Dashboard Data
 */
function updateDashboardData() {
    // Simulate real-time data updates
    const stats = getDashboardStats();
    
    // Add small random variations to simulate real data
    stats.totalRevenue.value += Math.floor(Math.random() * 10000);
    stats.studioSessions.value += Math.floor(Math.random() * 3);
    
    updateStatCards(stats);
    
    // Occasionally add new activities
    if (Math.random() < 0.3) {
        addNewActivity();
    }
}

/**
 * Add New Activity
 */
function addNewActivity() {
    const newActivities = [
        {
            type: 'sale',
            icon: 'fas fa-shopping-cart',
            iconColor: 'success',
            title: 'New sale recorded',
            description: 'Product sold for ₦' + (Math.floor(Math.random() * 5000) + 1000).toLocaleString(),
            timestamp: new Date()
        },
        {
            type: 'studio',
            icon: 'fas fa-music',
            iconColor: 'info',
            title: 'Studio session started',
            description: 'New customer checked into Studio ' + ['A', 'B', 'C'][Math.floor(Math.random() * 3)],
            timestamp: new Date()
        }
    ];
    
    const newActivity = newActivities[Math.floor(Math.random() * newActivities.length)];
    newActivity.id = Date.now();
    
    // Add to activities list
    DashboardState.activities.unshift(newActivity);
    if (DashboardState.activities.length > 10) {
        DashboardState.activities.pop();
    }
    
    // Update display
    updateActivityFeed(DashboardState.activities);
    
    // Show notification
    VioletMarellaCommon.showNotification(newActivity.title, 'info');
}

/**
 * Refresh Activities
 */
function refreshActivities() {
    const activities = getRecentActivities();
    DashboardState.activities = activities;
    updateActivityFeed(activities);
    VioletMarellaCommon.showNotification('Activities refreshed', 'success');
}

/**
 * Generate Report
 */
function generateReport() {
    VioletMarellaCommon.showLoading(event.target, 'Generating...');
    
    setTimeout(() => {
        VioletMarellaCommon.hideLoading(event.target, '<i class="fas fa-chart-bar me-2"></i>Generate Report');
        VioletMarellaCommon.showNotification('Report generated successfully', 'success');
        window.location.href = 'reports.html';
    }, 2000);
}

/**
 * Export Dashboard Data
 */
function exportDashboardData() {
    const data = {
        stats: DashboardState.stats,
        activities: DashboardState.activities,
        exportDate: new Date().toISOString()
    };
    
    const filename = `dashboard-export-${new Date().toISOString().split('T')[0]}.json`;
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    link.click();
    
    URL.revokeObjectURL(url);
    VioletMarellaCommon.showNotification('Dashboard data exported', 'success');
}

/**
 * Cleanup on page unload
 */
window.addEventListener('beforeunload', () => {
    if (DashboardState.refreshInterval) {
        clearInterval(DashboardState.refreshInterval);
    }
});

/**
 * Initialize on DOM Content Loaded
 */
document.addEventListener('DOMContentLoaded', function() {
    // Small delay to ensure common.js has loaded
    setTimeout(initializeDashboard, 100);
});

// Export dashboard functions for global access
window.VioletMarellaDashboard = {
    refreshActivities,
    generateReport,
    exportDashboardData,
    updateDashboardData
};