@extends('layouts.app')
@section('title', 'User Management - All Users')

@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h3 mb-1 fw-bold">User Management</h1>
                <p class="text-muted mb-0">Manage system users, roles, and access permissions</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#importUsersModal">
                    <i class="fas fa-file-import me-2"></i>Import Users
                </button>
                <button class="btn btn-outline-primary" onclick="exportUsers()">
                    <i class="fas fa-file-export me-2"></i>Export
                </button>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>Add New User
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 text-primary rounded p-3">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small mb-1">Total Users</div>
                            <h3 class="mb-0 fw-bold">{{ $totalUsers ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 text-success rounded p-3">
                                <i class="fas fa-user-check fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small mb-1">Active Users</div>
                            <h3 class="mb-0 fw-bold">{{ $activeUsers ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 text-warning rounded p-3">
                                <i class="fas fa-user-clock fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small mb-1">Pending</div>
                            <h3 class="mb-0 fw-bold">{{ $pendingUsers ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 text-info rounded p-3">
                                <i class="fas fa-user-shield fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small mb-1">Admins</div>
                            <h3 class="mb-0 fw-bold">{{ $adminUsers ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('users.index') }}" method="GET" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="search" class="form-label small mb-1">Search Users</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="search" name="search" 
                                   placeholder="Name, email, employee ID..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label for="roleFilter" class="form-label small mb-1">Role</label>
                        <select class="form-select" id="roleFilter" name="role">
                            <option value="">All Roles</option>
                            <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="viewer" {{ request('role') == 'viewer' ? 'selected' : '' }}>Viewer</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="departmentFilter" class="form-label small mb-1">Department</label>
                        <select class="form-select" id="departmentFilter" name="department">
                            <option value="">All Departments</option>
                            @foreach($departments ?? [] as $dept)
                                <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="statusFilter" class="form-label small mb-1">Status</label>
                        <select class="form-select" id="statusFilter" name="status">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions Bar -->
    <div class="card border-0 shadow-sm mb-3 d-none" id="bulkActionsBar">
        <div class="card-body py-2">
            <div class="d-flex align-items-center justify-content-between">
                <div class="text-muted">
                    <span id="selectedCount">0</span> user(s) selected
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="bulkActivate()">
                        <i class="fas fa-check me-1"></i>Activate
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="bulkSuspend()">
                        <i class="fas fa-pause me-1"></i>Suspend
                    </button>
                    <button class="btn btn-sm btn-outline-info" onclick="bulkAssignRole()">
                        <i class="fas fa-user-tag me-1"></i>Assign Role
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="bulkDelete()">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 50px;">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>User</th>
                            <th>Employee ID</th>
                            <th>Department</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users ?? [] as $user)
                        <tr>
                            <td class="ps-4">
                                <input type="checkbox" class="form-check-input user-checkbox" value="{{ $user->id }}">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        @if($user->profile_photo)
                                            <img src="{{ asset($user->profile_photo) }}" alt="{{ $user->first_name }}" 
                                                 class="rounded-circle" width="40" height="40">
                                        @else
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <strong>{{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="fw-semibold">{{ $user->first_name }} {{ $user->last_name }}</div>
                                        <div class="text-muted small">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $user->employee_id ?? 'N/A' }}</span>
                            </td>
                            <td>
                                @if($user->department)
                                    <span class="text-dark">{{ $user->department->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $roleColors = [
                                        'super_admin' => 'danger',
                                        'admin' => 'primary',
                                        'manager' => 'info',
                                        'staff' => 'success',
                                        'viewer' => 'secondary'
                                    ];
                                    $roleColor = $roleColors[$user->role] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $roleColor }}">{{ ucwords(str_replace('_', ' ', $user->role)) }}</span>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'active' => 'success',
                                        'inactive' => 'secondary',
                                        'pending' => 'warning',
                                        'suspended' => 'danger'
                                    ];
                                    $statusColor = $statusColors[$user->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">{{ ucfirst($user->status) }}</span>
                            </td>
                            <td class="text-muted small">
                                @if($user->last_login_at)
                                    {{ $user->last_login_at->diffForHumans() }}
                                @else
                                    Never
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('users.show', $user->id) }}">
                                                <i class="fas fa-eye me-2"></i>View Profile
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('users.edit', $user->id) }}">
                                                <i class="fas fa-edit me-2"></i>Edit User
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('users.permissions', $user->id) }}">
                                                <i class="fas fa-key me-2"></i>Manage Permissions
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        @if($user->status === 'active')
                                        <li>
                                            <a class="dropdown-item text-warning" href="#" onclick="suspendUser({{ $user->id }})">
                                                <i class="fas fa-pause me-2"></i>Suspend User
                                            </a>
                                        </li>
                                        @else
                                        <li>
                                            <a class="dropdown-item text-success" href="#" onclick="activateUser({{ $user->id }})">
                                                <i class="fas fa-check me-2"></i>Activate User
                                            </a>
                                        </li>
                                        @endif
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="resetPassword({{ $user->id }})">
                                                <i class="fas fa-key me-2"></i>Reset Password
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="#" onclick="deleteUser({{ $user->id }})">
                                                <i class="fas fa-trash me-2"></i>Delete User
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-3x mb-3 opacity-50"></i>
                                    <p class="mb-0">No users found</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if(isset($users) && $users->hasPages())
        <div class="card-footer bg-white border-top">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                </div>
                {{ $users->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Import Users Modal -->
<div class="modal fade" id="importUsersModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="import_file" class="form-label">Upload CSV/Excel File</label>
                        <input type="file" class="form-control" id="import_file" name="file" accept=".csv,.xlsx,.xls" required>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Accepted formats: CSV, Excel (.xlsx, .xls)
                        </div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <strong>Required columns:</strong> first_name, last_name, email, role
                        <br>
                        <a href="{{ route('users.download-template') }}" class="alert-link">
                            <i class="fas fa-download me-1"></i>Download template file
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Import Users
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Select all checkbox
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateBulkActionsBar();
});

// Individual checkboxes
document.querySelectorAll('.user-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkActionsBar);
});

function updateBulkActionsBar() {
    const checked = document.querySelectorAll('.user-checkbox:checked').length;
    const bar = document.getElementById('bulkActionsBar');
    const count = document.getElementById('selectedCount');
    
    if (checked > 0) {
        bar.classList.remove('d-none');
        count.textContent = checked;
    } else {
        bar.classList.add('d-none');
    }
}

function exportUsers() {
    window.location.href = '{{ route("users.export") }}?' + new URLSearchParams(new FormData(document.getElementById('filterForm')));
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        // Implement delete functionality
        console.log('Delete user:', userId);
    }
}

function suspendUser(userId) {
    if (confirm('Are you sure you want to suspend this user?')) {
        // Implement suspend functionality
        console.log('Suspend user:', userId);
    }
}

function activateUser(userId) {
    // Implement activate functionality
    console.log('Activate user:', userId);
}

function resetPassword(userId) {
    if (confirm('Send password reset email to this user?')) {
        // Implement password reset
        console.log('Reset password for user:', userId);
    }
}

function bulkActivate() {
    const selected = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
    console.log('Bulk activate:', selected);
}

function bulkSuspend() {
    const selected = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
    if (confirm(`Suspend ${selected.length} user(s)?`)) {
        console.log('Bulk suspend:', selected);
    }
}

function bulkAssignRole() {
    const selected = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
    // Show role assignment modal
    console.log('Bulk assign role:', selected);
}

function bulkDelete() {
    const selected = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
    if (confirm(`Delete ${selected.length} user(s)? This action cannot be undone.`)) {
        console.log('Bulk delete:', selected);
    }
}
</script>
@endpush
@endsection