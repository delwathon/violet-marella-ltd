@extends('layouts.app')
@section('title', 'User Management')

@section('content')
<div class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">User Management</h1>
            <p class="text-muted mb-0">Manage users, access roles, and departments.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#importUsersModal">
                <i class="fas fa-file-import me-2"></i>Import
            </button>
            <a href="{{ route('users.export') }}" class="btn btn-outline-primary">
                <i class="fas fa-file-export me-2"></i>Export
            </a>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>Add User
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Total</div><h3 class="mb-0 fw-bold">{{ $totalUsers }}</h3></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Active</div><h3 class="mb-0 fw-bold">{{ $activeUsers }}</h3></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Inactive</div><h3 class="mb-0 fw-bold">{{ $pendingUsers }}</h3></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Admins</div><h3 class="mb-0 fw-bold">{{ $adminUsers }}</h3></div></div></div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('users.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small mb-1">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name or email">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Role</label>
                    <select name="role" class="form-select">
                        <option value="">All roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->slug }}" {{ request('role') === $role->slug ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Department</label>
                    <select name="department" class="form-select">
                        <option value="">All departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ (string) request('department') === (string) $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary w-100" type="submit">Filter</button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Hire Date</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $targetUser)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($targetUser->profile_photo)
                                            <img src="{{ asset('storage/' . $targetUser->profile_photo) }}" alt="{{ $targetUser->full_name }}" class="rounded-circle" width="36" height="36">
                                        @else
                                            <span class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex justify-content-center align-items-center" style="width: 36px; height: 36px;">
                                                {{ strtoupper(substr($targetUser->first_name, 0, 1)) }}
                                            </span>
                                        @endif
                                        <span class="fw-semibold">{{ $targetUser->full_name }}</span>
                                    </div>
                                </td>
                                <td>{{ $targetUser->email }}</td>
                                <td><span class="badge bg-light text-dark">{{ $targetUser->roleRecord?->name ?? $targetUser->role }}</span></td>
                                <td>{{ $targetUser->department?->name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $targetUser->is_active ? 'success' : 'secondary' }}">
                                        {{ $targetUser->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ optional($targetUser->hire_date)->format('Y-m-d') }}</td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('users.show', $targetUser->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    <a href="{{ route('users.edit', $targetUser->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <a href="{{ route('users.permissions', $targetUser->id) }}" class="btn btn-sm btn-outline-info">Permissions</a>
                                    <form action="{{ route('users.destroy', $targetUser->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
            <div class="card-footer bg-white">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="importUsersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Users (CSV)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">CSV File</label>
                        <input type="file" name="file" class="form-control" accept=".csv,.txt" required>
                    </div>
                    <a href="{{ route('users.download-template') }}" class="small">Download CSV template</a>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
