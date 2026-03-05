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
                $groupedPermissions = $permissionGroups ?? \App\Support\AccessControl::permissionGroups();
                $selectedPermissions = old('permissions', $targetUser->permissions ?? []);
            @endphp

            <form action="{{ route('users.permissions.update', $targetUser->id) }}" method="POST">
                @csrf
                @foreach($groupedPermissions as $groupName => $permissionOptions)
                    <div class="mb-3">
                        <h6 class="small text-uppercase text-muted mb-2">{{ $groupName }}</h6>
                        <div class="row g-2">
                            @foreach($permissionOptions as $permission)
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="perm_{{ str_replace(['.', '*'], ['_', 'wildcard'], $permission) }}" name="permissions[]" value="{{ $permission }}" {{ in_array($permission, $selectedPermissions, true) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="perm_{{ str_replace(['.', '*'], ['_', 'wildcard'], $permission) }}">{{ $permission }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                <div class="form-check border rounded p-2 mb-4">
                    <input class="form-check-input" type="checkbox" id="perm_all" name="permissions[]" value="*" {{ in_array('*', $selectedPermissions, true) ? 'checked' : '' }}>
                    <label class="form-check-label small fw-semibold" for="perm_all">* (Grant all permissions)</label>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Save Permissions</button>
                    @if($targetUser->roleRecord)
                        <span class="text-muted small align-self-center">Role defaults still apply from <strong>{{ $targetUser->roleRecord->name }}</strong>.</span>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
