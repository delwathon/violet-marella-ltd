@extends('layouts.app')
@section('title', 'Roles & Permissions')

@section('content')
<div class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">Roles & Permissions</h1>
            <p class="text-muted mb-0">Create, update, and secure role-level access.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
            <i class="fas fa-plus me-2"></i>New Role
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Total Roles</div><h3 class="mb-0 fw-bold">{{ $totalRoles }}</h3></div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Unique Permissions</div><h3 class="mb-0 fw-bold">{{ $totalPermissions }}</h3></div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Custom Roles</div><h3 class="mb-0 fw-bold">{{ $customRoles }}</h3></div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Permission Groups</div><h3 class="mb-0 fw-bold">{{ $permissionGroups }}</h3></div></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Roles</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Users</th>
                            <th>Permissions</th>
                            <th>Type</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td class="fw-semibold">{{ $role->name }}</td>
                                <td><code>{{ $role->slug }}</code></td>
                                <td>{{ $role->users_count }}</td>
                                <td>{{ count($role->permissions ?? []) }}</td>
                                <td>
                                    @if($role->is_system)
                                        <span class="badge bg-secondary">System</span>
                                    @else
                                        <span class="badge bg-info">Custom</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('roles.show', $role->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    <form action="{{ route('roles.duplicate', $role->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">Duplicate</button>
                                    </form>
                                    @if(!$role->is_system)
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this role?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No roles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug (optional)</label>
                        <input type="text" name="slug" class="form-control" placeholder="auto-generated-if-empty">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color</label>
                        <select name="color" class="form-select">
                            <option value="primary">Blue</option>
                            <option value="success">Green</option>
                            <option value="info">Cyan</option>
                            <option value="warning">Yellow</option>
                            <option value="danger">Red</option>
                            <option value="secondary">Gray</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Clone Permissions From</label>
                        <select name="template" class="form-select">
                            <option value="">No template</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->slug }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
