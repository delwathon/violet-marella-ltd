@extends('layouts.app')
@section('title', 'Role Details')

@section('content')
<div class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('roles.index') }}" class="btn btn-sm btn-outline-secondary mb-2"><i class="fas fa-arrow-left me-1"></i>Back</a>
            <h1 class="h3 mb-1 fw-bold">{{ $role->name }}</h1>
            <p class="text-muted mb-0">Role slug: <code>{{ $role->slug }}</code></p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3"><h5 class="mb-0">Role Profile</h5></div>
                <div class="card-body">
                    <form action="{{ route('roles.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $role->name) }}" required>
                        </div>
                        @if(!$role->is_system)
                            <div class="mb-3">
                                <label class="form-label">Slug</label>
                                <input type="text" name="slug" class="form-control" value="{{ old('slug', $role->slug) }}">
                            </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">Color</label>
                            <input type="text" name="color" class="form-control" value="{{ old('color', $role->color) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $role->description) }}</textarea>
                        </div>
                        <button class="btn btn-primary" type="submit">Save Role</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3"><h5 class="mb-0">Permissions</h5></div>
                <div class="card-body">
                    <form action="{{ route('roles.permissions.update', $role->id) }}" method="POST">
                        @csrf
                        @php
                            $permissionOptions = [
                                'dashboard.view', 'users.view', 'users.manage', 'roles.manage', 'departments.manage',
                                'products.view', 'products.manage', 'inventory.view', 'inventory.manage',
                                'sales.view', 'sales.create', 'sales.refund', 'customers.view', 'customers.manage',
                                'reports.view', 'reports.export', 'settings.manage', 'security.manage'
                            ];
                            $selectedPermissions = $role->permissions ?? [];
                        @endphp
                        <div class="row g-2 mb-3">
                            @foreach($permissionOptions as $permission)
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="perm_{{ $permission }}" name="permissions[]" value="{{ $permission }}" {{ in_array($permission, $selectedPermissions, true) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="perm_{{ $permission }}">{{ $permission }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button class="btn btn-primary" type="submit">Save Permissions</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white py-3"><h5 class="mb-0">Assigned Users</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $assignedUser)
                            <tr>
                                <td>{{ $assignedUser->full_name }}</td>
                                <td>{{ $assignedUser->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $assignedUser->is_active ? 'success' : 'secondary' }}">
                                        {{ $assignedUser->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">No users assigned to this role.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
