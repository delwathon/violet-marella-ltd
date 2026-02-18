@extends('layouts.app')
@section('title', 'User Permissions')

@section('content')
<div class="content-area">
    <div class="mb-4">
        <a href="{{ route('users.show', $targetUser->id) }}" class="btn btn-sm btn-outline-secondary mb-2"><i class="fas fa-arrow-left me-1"></i>Back</a>
        <h1 class="h3 mb-1 fw-bold">Permissions: {{ $targetUser->full_name }}</h1>
        <p class="text-muted mb-0">Configure user-level overrides in addition to role permissions.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3"><h5 class="mb-0">Permission Overrides</h5></div>
        <div class="card-body">
            @php
                $permissionOptions = [
                    'users.view', 'users.manage', 'roles.manage', 'departments.manage',
                    'products.view', 'products.manage', 'inventory.view', 'inventory.manage',
                    'sales.view', 'sales.create', 'sales.refund', 'customers.view', 'customers.manage',
                    'reports.view', 'reports.export', 'settings.manage', 'security.manage'
                ];
                $selectedPermissions = old('permissions', $targetUser->permissions ?? []);
            @endphp

            <form action="{{ route('users.permissions.update', $targetUser->id) }}" method="POST">
                @csrf
                <div class="row g-2 mb-4">
                    @foreach($permissionOptions as $permission)
                        <div class="col-md-6 col-lg-4">
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
@endsection
