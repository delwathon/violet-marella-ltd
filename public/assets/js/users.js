/**
 * VIOLET MARELLA LIMITED - USER MANAGEMENT FUNCTIONALITY
 * User accounts, roles, and permissions management
 */

// User Management state and data
const UserManagementState = {
    users: [],
    roles: [],
    permissions: [],
    activities: [],
    currentFilter: {
        role: '',
        status: '',
        search: ''
    }
};

// Sample user data
const sampleUsers = [
    {
        id: 'user-001',
        firstName: 'John',
        lastName: 'Doe',
        email: 'admin@violetmarella.com',
        phone: '+234 801 234 5678',
        role: 'administrator',
        department: 'all',
        employeeId: 'EMP-001',
        status: 'active',
        lastLogin: new Date(Date.now() - 2 * 60 * 60 * 1000),
        startDate: new Date('2023-01-15'),
        avatar: 'JD'
    },
    {
        id: 'user-002',
        firstName: 'Jane',
        lastName: 'Smith',
        email: 'manager@violetmarella.com',
        phone: '+234 802 345 6789',
        role: 'manager',
        department: 'anire-craft-store',
        employeeId: 'EMP-002',
        status: 'active',
        lastLogin: new Date(Date.now() - 5 * 60 * 60 * 1000),
        startDate: new Date('2023-02-20'),
        avatar: 'JS'
    },
    {
        id: 'user-003',
        firstName: 'Mike',
        lastName: 'Johnson',
        email: 'staff@violetmarella.com',
        phone: '+234 803 456 7890',
        role: 'staff',
        department: 'lounge',
        employeeId: 'EMP-003',
        status: 'active',
        lastLogin: new Date(Date.now() - 24 * 60 * 60 * 1000),
        startDate: new Date('2023-03-10'),
        avatar: 'MJ'
    }
];

// Sample roles data
const sampleRoles = [
    {
        id: 'role-001',
        name: 'Administrator',
        description: 'Full system access and user management',
        permissions: ['all'],
        userCount: 1
    },
    {
        id: 'role-002',
        name: 'Manager',
        description: 'Business operations and reporting access',
        permissions: ['dashboard.view', 'anire-craft-store.manage', 'lounge.manage', 'photo-studio.manage', 'reports.view'],
        userCount: 2
    },
    {
        id: 'role-003',
        name: 'Staff',
        description: 'Basic operational access',
        permissions: ['dashboard.view', 'anire-craft-store.view', 'lounge.operate'],
        userCount: 3
    }
];

// Sample activity data
const sampleActivities = [
    {
        id: 'act-001',
        userId: 'user-001',
        userName: 'John Doe',
        action: 'User Login',
        details: 'Logged in from 192.168.1.100',
        timestamp: new Date(Date.now() - 30 * 60 * 1000),
        type: 'authentication'
    },
    {
        id: 'act-002',
        userId: 'user-002',
        userName: 'Jane Smith',
        action: 'Product Updated',
        details: 'Updated inventory for Valentine Gift Box',
        timestamp: new Date(Date.now() - 2 * 60 * 60 * 1000),
        type: 'business'
    }
];

/**
 * Initialize User Management
 */
function initializeUserManagement() {
    console.log('Initializing user management...');
    
    // Load data
    loadUserManagementData();
    
    // Bind events
    bindUserManagementEvents();
    
    // Update displays
    updateUsersTable();
    updateRolesList();
    updateActivityTimeline();
    updateUserStats();
    
    console.log('User management initialized successfully');
}

/**
 * Load User Management Data
 */
function loadUserManagementData() {
    UserManagementState.users = [...sampleUsers];
    UserManagementState.roles = [...sampleRoles];
    UserManagementState.activities = [...sampleActivities];
    
    console.log('Loaded', UserManagementState.users.length, 'users');
}

/**
 * Bind User Management Events
 */
function bindUserManagementEvents() {
    // Search functionality
    const userSearch = document.getElementById('userSearch');
    if (userSearch) {
        const debouncedSearch = VioletMarellaCommon.debounce((value) => {
            UserManagementState.currentFilter.search = value;
            updateUsersTable();
        }, 300);
        
        userSearch.addEventListener('input', (e) => {
            debouncedSearch(e.target.value);
        });
    }
    
    // Role filter
    const roleFilter = document.getElementById('roleFilter');
    if (roleFilter) {
        roleFilter.addEventListener('change', (e) => {
            UserManagementState.currentFilter.role = e.target.value;
            updateUsersTable();
        });
    }
    
    // Select all users
    const selectAllUsers = document.getElementById('selectAllUsers');
    if (selectAllUsers) {
        selectAllUsers.addEventListener('change', handleSelectAllUsers);
    }
    
    // Tab switches
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', handleTabSwitch);
    });
}

/**
 * Update Users Table
 */
function updateUsersTable() {
    const usersTable = document.getElementById('usersTable');
    if (!usersTable) return;
    
    const filteredUsers = getFilteredUsers();
    
    if (filteredUsers.length === 0) {
        usersTable.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <div>No users found</div>
                    <small class="text-muted">Try adjusting your search or filters</small>
                </td>
            </tr>
        `;
        return;
    }
    
    usersTable.innerHTML = filteredUsers.map(user => `
        <tr data-user-id="${user.id}">
            <td>
                <div class="form-check">
                    <input class="form-check-input user-checkbox" type="checkbox" value="${user.id}">
                </div>
            </td>
            <td>
                <div class="user-info">
                    <div class="user-avatar">${user.avatar}</div>
                    <div class="user-details">
                        <h6 class="mb-0">${user.firstName} ${user.lastName}</h6>
                        <small class="text-muted">${user.email}</small>
                    </div>
                </div>
            </td>
            <td>
                <span class="role-badge ${user.role}">${formatRole(user.role)}</span>
            </td>
            <td>${formatDepartment(user.department)}</td>
            <td>
                <div class="last-login">
                    <div>${VioletMarellaCommon.formatDate(user.lastLogin, 'short')}</div>
                    <small class="text-muted">${VioletMarellaCommon.formatDate(user.lastLogin, 'time')}</small>
                </div>
            </td>
            <td>
                <span class="status-badge ${user.status}">${formatStatus(user.status)}</span>
            </td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="editUser('${user.id}')" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-info" onclick="viewUserActivity('${user.id}')" title="Activity">
                        <i class="fas fa-history"></i>
                    </button>
                    <button class="btn btn-outline-warning" onclick="resetUserPassword('${user.id}')" title="Reset Password">
                        <i class="fas fa-key"></i>
                    </button>
                    <button class="btn btn-outline-danger" onclick="deactivateUser('${user.id}')" title="Deactivate">
                        <i class="fas fa-user-slash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

/**
 * Get Filtered Users
 */
function getFilteredUsers() {
    let filtered = [...UserManagementState.users];
    
    // Apply search filter
    if (UserManagementState.currentFilter.search) {
        const search = UserManagementState.currentFilter.search.toLowerCase();
        filtered = filtered.filter(user => 
            user.firstName.toLowerCase().includes(search) ||
            user.lastName.toLowerCase().includes(search) ||
            user.email.toLowerCase().includes(search) ||
            user.employeeId.toLowerCase().includes(search)
        );
    }
    
    // Apply role filter
    if (UserManagementState.currentFilter.role) {
        filtered = filtered.filter(user => user.role === UserManagementState.currentFilter.role);
    }
    
    return filtered;
}

/**
 * Update Roles List
 */
function updateRolesList() {
    const rolesList = document.getElementById('rolesList');
    if (!rolesList) return;
    
    rolesList.innerHTML = UserManagementState.roles.map(role => `
        <div class="role-item" data-role-id="${role.id}">
            <div class="role-header">
                <div>
                    <div class="role-name">${role.name}</div>
                    <div class="role-description">${role.description}</div>
                </div>
                <div class="role-actions">
                    <span class="badge bg-primary">${role.userCount} users</span>
                    <button class="btn btn-sm btn-outline-primary ms-2" onclick="editRole('${role.id}')">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            </div>
            <div class="role-permissions">
                ${role.permissions.map(permission => `
                    <span class="permission-tag">${formatPermission(permission)}</span>
                `).join('')}
            </div>
        </div>
    `).join('');
}

/**
 * Update Activity Timeline
 */
function updateActivityTimeline() {
    const activityTimeline = document.getElementById('activityTimeline');
    if (!activityTimeline) return;
    
    if (UserManagementState.activities.length === 0) {
        activityTimeline.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                <div>No activity recorded</div>
                <small class="text-muted">User activities will appear here</small>
            </div>
        `;
        return;
    }
    
    activityTimeline.innerHTML = UserManagementState.activities.map(activity => `
        <div class="activity-item" data-activity-id="${activity.id}">
            <div class="activity-header">
                <div class="activity-user">${activity.userName}</div>
                <div class="activity-time">${VioletMarellaCommon.formatDate(activity.timestamp, 'datetime')}</div>
            </div>
            <div class="activity-action">${activity.action}</div>
            <div class="activity-details">${activity.details}</div>
        </div>
    `).join('');
}

/**
 * Update User Statistics
 */
function updateUserStats() {
    const totalUsers = UserManagementState.users.length;
    const activeUsers = UserManagementState.users.filter(u => u.status === 'active').length;
    const pendingUsers = UserManagementState.users.filter(u => u.status === 'pending').length;
    const administrators = UserManagementState.users.filter(u => u.role === 'administrator').length;
    
    const statCards = [
        { selector: '.stat-card:nth-child(1) .stat-value', value: totalUsers },
        { selector: '.stat-card:nth-child(2) .stat-value', value: activeUsers },
        { selector: '.stat-card:nth-child(3) .stat-value', value: pendingUsers },
        { selector: '.stat-card:nth-child(4) .stat-value', value: administrators }
    ];
    
    statCards.forEach(card => {
        const element = document.querySelector(card.selector);
        if (element) {
            element.textContent = card.value;
        }
    });
}

/**
 * Format Role
 */
function formatRole(role) {
    const roles = {
        'administrator': 'Administrator',
        'manager': 'Manager',
        'staff': 'Staff'
    };
    return roles[role] || role;
}

/**
 * Format Department
 */
function formatDepartment(department) {
    const departments = {
        'all': 'All Departments',
        'anire-craft-store': 'Gift Store',
        'lounge': 'Lounge',
        'photo-studio': 'Photo Studio'
    };
    return departments[department] || department;
}

/**
 * Format Status
 */
function formatStatus(status) {
    const statuses = {
        'active': 'Active',
        'inactive': 'Inactive',
        'pending': 'Pending',
        'suspended': 'Suspended'
    };
    return statuses[status] || status;
}

/**
 * Format Permission
 */
function formatPermission(permission) {
    if (permission === 'all') return 'All Permissions';
    
    const permissions = {
        'dashboard.view': 'View Dashboard',
        'anire-craft-store.manage': 'Manage Gift Store',
        'anire-craft-store.view': 'View Gift Store',
        'lounge.manage': 'Manage Lounge',
        'lounge.operate': 'Operate POS',
        'photo-studio.manage': 'Manage Studio',
        'reports.view': 'View Reports',
        'users.manage': 'Manage Users',
        'settings.manage': 'Manage Settings'
    };
    
    return permissions[permission] || permission;
}

/**
 * Add New User
 */
function addUser() {
    const form = document.getElementById('addUserForm');
    if (!form || !VioletMarellaCommon.validateForm(form)) return;
    
    const formData = new FormData(form);
    const newUser = {
        id: `user-${Date.now()}`,
        firstName: formData.get('firstName') || form.querySelector('input[type="text"]').value,
        lastName: formData.get('lastName') || form.querySelectorAll('input[type="text"]')[1].value,
        email: formData.get('email') || form.querySelector('input[type="email"]').value,
        phone: formData.get('phone') || form.querySelector('input[type="tel"]').value,
        role: formData.get('role') || form.querySelector('select').value,
        department: formData.get('department') || form.querySelectorAll('select')[1].value,
        employeeId: formData.get('employeeId') || form.querySelectorAll('input[type="text"]')[2].value,
        status: 'pending',
        lastLogin: null,
        startDate: new Date(formData.get('startDate') || form.querySelector('input[type="date"]').value),
        avatar: getInitials(formData.get('firstName') + ' ' + formData.get('lastName'))
    };
    
    // Add to users array
    UserManagementState.users.unshift(newUser);
    
    // Update displays
    updateUsersTable();
    updateUserStats();
    
    // Close modal and reset form
    const modal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
    modal.hide();
    form.reset();
    
    VioletMarellaCommon.showNotification(`User ${newUser.firstName} ${newUser.lastName} added successfully`, 'success');
    
    // Send welcome email (simulation)
    const sendWelcome = document.getElementById('sendWelcomeEmail')?.checked;
    if (sendWelcome) {
        setTimeout(() => {
            VioletMarellaCommon.showNotification('Welcome email sent', 'info');
        }, 1000);
    }
}

/**
 * Get Initials
 */
function getInitials(name) {
    return name.split(' ').map(word => word.charAt(0).toUpperCase()).join('').substring(0, 2);
}

/**
 * Edit User
 */
function editUser(userId) {
    const user = UserManagementState.users.find(u => u.id === userId);
    if (!user) return;
    
    VioletMarellaCommon.showNotification(`Edit functionality for ${user.firstName} ${user.lastName} would be implemented here.`, 'info');
}

/**
 * View User Activity
 */
function viewUserActivity(userId) {
    const user = UserManagementState.users.find(u => u.id === userId);
    if (!user) return;
    
    // Switch to activity tab and filter by user
    const activityTab = document.getElementById('activity-tab');
    if (activityTab) {
        activityTab.click();
        // Filter activities for this user
        setTimeout(() => {
            const userFilter = document.getElementById('activityUserFilter');
            if (userFilter) {
                userFilter.value = userId;
                // Trigger filter update
                filterActivityByUser(userId);
            }
        }, 100);
    }
}

/**
 * Reset User Password
 */
function resetUserPassword(userId) {
    const user = UserManagementState.users.find(u => u.id === userId);
    if (!user) return;
    
    if (confirm(`Reset password for ${user.firstName} ${user.lastName}?`)) {
        VioletMarellaCommon.showNotification('Password reset email sent', 'success');
        
        // Log activity
        UserManagementState.activities.unshift({
            id: `act-${Date.now()}`,
            userId: userId,
            userName: `${user.firstName} ${user.lastName}`,
            action: 'Password Reset',
            details: 'Password reset email sent',
            timestamp: new Date(),
            type: 'security'
        });
        
        updateActivityTimeline();
    }
}

/**
 * Deactivate User
 */
function deactivateUser(userId) {
    const user = UserManagementState.users.find(u => u.id === userId);
    if (!user) return;
    
    if (confirm(`Deactivate ${user.firstName} ${user.lastName}?`)) {
        user.status = 'inactive';
        updateUsersTable();
        updateUserStats();
        
        VioletMarellaCommon.showNotification(`${user.firstName} ${user.lastName} deactivated`, 'warning');
        
        // Log activity
        UserManagementState.activities.unshift({
            id: `act-${Date.now()}`,
            userId: userId,
            userName: `${user.firstName} ${user.lastName}`,
            action: 'User Deactivated',
            details: 'User account deactivated',
            timestamp: new Date(),
            type: 'administration'
        });
        
        updateActivityTimeline();
    }
}

/**
 * Generate Password
 */
function generatePassword() {
    const length = 12;
    const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    let password = '';
    
    for (let i = 0; i < length; i++) {
        password += charset.charAt(Math.floor(Math.random() * charset.length));
    }
    
    const tempPasswordInput = document.getElementById('tempPassword');
    if (tempPasswordInput) {
        tempPasswordInput.value = password;
    }
}

/**
 * Export Users
 */
function exportUsers() {
    const data = UserManagementState.users.map(user => ({
        'Employee ID': user.employeeId,
        'First Name': user.firstName,
        'Last Name': user.lastName,
        'Email': user.email,
        'Phone': user.phone,
        'Role': formatRole(user.role),
        'Department': formatDepartment(user.department),
        'Status': formatStatus(user.status),
        'Start Date': VioletMarellaCommon.formatDate(user.startDate, 'short'),
        'Last Login': user.lastLogin ? VioletMarellaCommon.formatDate(user.lastLogin, 'datetime') : 'Never'
    }));
    
    const filename = `users-export-${new Date().toISOString().split('T')[0]}.csv`;
    VioletMarellaCommon.exportToCSV(data, filename);
    VioletMarellaCommon.showNotification('Users exported successfully', 'success');
}

/**
 * Handle Select All Users
 */
function handleSelectAllUsers(event) {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = event.target.checked;
    });
    
    updateBulkActions();
}

/**
 * Update Bulk Actions
 */
function updateBulkActions() {
    const selectedUsers = document.querySelectorAll('.user-checkbox:checked');
    const bulkActions = document.querySelector('.bulk-actions');
    
    if (bulkActions) {
        if (selectedUsers.length > 0) {
            bulkActions.classList.add('show');
        } else {
            bulkActions.classList.remove('show');
        }
    }
}

/**
 * Handle Tab Switch
 */
function handleTabSwitch(event) {
    const tabId = event.target.getAttribute('data-bs-target');
    
    if (tabId === '#roles') {
        updateRolesList();
    } else if (tabId === '#activity') {
        updateActivityTimeline();
    }
}

/**
 * Filter Activity by User
 */
function filterActivityByUser(userId) {
    if (userId) {
        const filteredActivities = UserManagementState.activities.filter(a => a.userId === userId);
        // Update timeline with filtered activities
        const activityTimeline = document.getElementById('activityTimeline');
        if (activityTimeline) {
            activityTimeline.innerHTML = filteredActivities.map(activity => `
                <div class="activity-item">
                    <div class="activity-header">
                        <div class="activity-user">${activity.userName}</div>
                        <div class="activity-time">${VioletMarellaCommon.formatDate(activity.timestamp, 'datetime')}</div>
                    </div>
                    <div class="activity-action">${activity.action}</div>
                    <div class="activity-details">${activity.details}</div>
                </div>
            `).join('');
        }
    } else {
        updateActivityTimeline();
    }
}

/**
 * Add Role
 */
function addRole() {
    VioletMarellaCommon.showNotification('Add role functionality would be implemented here', 'info');
}

/**
 * Edit Role
 */
function editRole(roleId) {
    const role = UserManagementState.roles.find(r => r.id === roleId);
    if (role) {
        VioletMarellaCommon.showNotification(`Edit role: ${role.name}`, 'info');
    }
}

/**
 * Update User
 */
function updateUser() {
    VioletMarellaCommon.showNotification('Update user functionality would be implemented here', 'success');
}

/**
 * Initialize on DOM Content Loaded
 */
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initializeUserManagement, 100);
});

// Export user management functions for global access
window.VioletMarellaUserManagement = {
    addUser,
    editUser,
    viewUserActivity,
    resetUserPassword,
    deactivateUser,
    generatePassword,
    exportUsers,
    addRole,
    editRole,
    updateUser
};