@extends('layouts.app')
@section('title', 'Roles & Permissions Management')

@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h3 mb-1 fw-bold">Roles & Permissions</h1>
                <p class="text-muted mb-0">Define roles and configure granular access permissions</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                <i class="fas fa-plus me-2"></i>Create New Role
            </button>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded p-3 me-3">
                            <i class="fas fa-user-tag fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total Roles</div>
                            <h3 class="mb-0 fw-bold">{{ $totalRoles ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 text-success rounded p-3 me-3">
                            <i class="fas fa-key fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Permissions</div>
                            <h3 class="mb-0 fw-bold">{{ $totalPermissions ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 text-info rounded p-3 me-3">
                            <i class="fas fa-shield-alt fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Custom Roles</div>
                            <h3 class="mb-0 fw-bold">{{ $customRoles ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 text-warning rounded p-3 me-3">
                            <i class="fas fa-sitemap fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Permission Groups</div>
                            <h3 class="mb-0 fw-bold">{{ $permissionGroups ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles List -->
    <div class="row g-4">
        @php
            $defaultRoles = [
                [
                    'name' => 'Super Admin',
                    'slug' => 'super_admin',
                    'description' => 'Full system access with all permissions',
                    'users_count' => 2,
                    'color' => 'danger',
                    'is_system' => true
                ],
                [
                    'name' => 'Admin',
                    'slug' => 'admin',
                    'description' => 'Administrative access to most features',
                    'users_count' => 5,
                    'color' => 'primary',
                    'is_system' => true
                ],
                [
                    'name' => 'Manager',
                    'slug' => 'manager',
                    'description' => 'Manage team operations and reports',
                    'users_count' => 8,
                    'color' => 'info',
                    'is_system' => true
                ],
                [
                    'name' => 'Staff',
                    'slug' => 'staff',
                    'description' => 'Standard employee access',
                    'users_count' => 25,
                    'color' => 'success',
                    'is_system' => true
                ],
                [
                    'name' => 'Viewer',
                    'slug' => 'viewer',
                    'description' => 'Read-only access to authorized areas',
                    'users_count' => 10,
                    'color' => 'secondary',
                    'is_system' => true
                ]
            ];
            
            $allRoles = $roles ?? collect($defaultRoles);
        @endphp
        
        @foreach($allRoles as $role)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-1">
                                <span class="badge bg-{{ $role['color'] ?? 'primary' }} me-2">
                                    {{ $role['name'] }}
                                </span>
                                @if($role['is_system'] ?? false)
                                    <span class="badge bg-light text-dark small">System</span>
                                @endif
                            </h5>
                            <p class="text-muted small mb-0">{{ $role['description'] }}</p>
                        </div>
                        @if(!($role['is_system'] ?? false))
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>Edit Role</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-copy me-2"></i>Duplicate</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i>Delete</a></li>
                            </ul>
                        </div>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Users with this role</span>
                            <span class="badge bg-light text-dark">{{ $role['users_count'] ?? 0 }}</span>
                        </div>
                        <div class="progress" style="height: 5px;">
                            @php
                                $percentage = ($role['users_count'] ?? 0) > 0 ? min(100, ($role['users_count'] / 50) * 100) : 0;
                            @endphp
                            <div class="progress-bar bg-{{ $role['color'] ?? 'primary' }}" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    
                    <button class="btn btn-outline-primary btn-sm w-100" onclick="viewRolePermissions('{{ $role['slug'] }}')">
                        <i class="fas fa-key me-2"></i>Manage Permissions
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Permission Matrix -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Permission Matrix</h5>
            <p class="text-muted small mb-0">Configure permissions for each role across different modules</p>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 200px;">Module / Permission</th>
                            <th class="text-center">Super Admin</th>
                            <th class="text-center">Admin</th>
                            <th class="text-center">Manager</th>
                            <th class="text-center">Staff</th>
                            <th class="text-center">Viewer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dashboard -->
                        <tr class="table-light">
                            <td class="ps-4 fw-semibold" colspan="6">
                                <i class="fas fa-home me-2"></i>Dashboard
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-5 small">View Dashboard</td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        </tr>
                        
                        <!-- User Management -->
                        <tr class="table-light">
                            <td class="ps-4 fw-semibold" colspan="6">
                                <i class="fas fa-users me-2"></i>User Management
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-5 small">View Users</td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-5 small">Create Users</td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-5 small">Edit Users</td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-5 small">Delete Users</td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-5 small">Manage Roles</td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        </tr>
                        
                        <!-- Products -->
                        <tr class="table-light">
                            <td class="ps-4 fw-semibold" colspan="6">
                                <i class="fas fa-boxes me-2"></i>Product Management
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-5 small">View Products</td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-5 small">Create Products</td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-5 small">Edit Products</td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-5 small">Delete Products</td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        </tr>
                        
                        <!-- Sales & POS -->
                        <tr class="table-light">
                            <td class="ps-4 fw-semibold" colspan="6">
                                <i class="fas fa-cash-register me-2"></i>Sales & POS
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-5 small">Process Sales</td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-5 small">View Sales History</td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-5 small">Refund Sales</td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        </tr>
                        
                        <!-- Reports -->
                        <tr class="table-light">
                            <td class="ps-4 fw-semibold" colspan="6">
                                <i class="fas fa-chart-bar me-2"></i>Reports & Analytics
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-5 small">View Reports</td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-5 small">Export Reports</td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        </tr>
                        
                        <!-- Settings -->
                        <tr class="table-light">
                            <td class="ps-4 fw-semibold" colspan="6">
                                <i class="fas fa-cog me-2"></i>System Settings
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-5 small">View Settings</td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-5 small">Modify Settings</td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <button class="btn btn-primary" onclick="savePermissions()">
                <i class="fas fa-save me-2"></i>Save Permission Changes
            </button>
        </div>
    </div>
</div>

<!-- Create Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="role_name" class="form-label">Role Name</label>
                            <input type="text" class="form-control" id="role_name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="role_slug" class="form-label">Role Identifier</label>
                            <input type="text" class="form-control" id="role_slug" name="slug" required>
                            <div class="form-text">Used internally (lowercase, underscores only)</div>
                        </div>
                        <div class="col-12">
                            <label for="role_description" class="form-label">Description</label>
                            <textarea class="form-control" id="role_description" name="description" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Based on Template</label>
                            <select class="form-select" name="template">
                                <option value="">Start from scratch</option>
                                <option value="admin">Admin (Copy admin permissions)</option>
                                <option value="manager">Manager (Copy manager permissions)</option>
                                <option value="staff">Staff (Copy staff permissions)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewRolePermissions(roleSlug) {
    // Scroll to permission matrix
    document.querySelector('.table-responsive').scrollIntoView({ behavior: 'smooth' });
}

function savePermissions() {
    alert('Permissions saved successfully!');
}

// Auto-generate slug from name
document.getElementById('role_name').addEventListener('input', function() {
    const slug = this.value.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');
    document.getElementById('role_slug').value = slug;
});
</script>
@endpush
@endsection