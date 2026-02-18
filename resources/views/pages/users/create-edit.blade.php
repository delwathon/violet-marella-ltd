@extends('layouts.app')
@section('title', $targetUser ? 'Edit User' : 'Create User')

@section('content')
<div class="content-area">
    <div class="mb-4">
        <div class="d-flex align-items-center gap-2 mb-2">
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
            <h1 class="h3 mb-0 fw-bold">{{ $targetUser ? 'Edit User' : 'Create User' }}</h1>
        </div>
        <p class="text-muted mb-0">{{ $targetUser ? 'Update account details and access control.' : 'Add a new platform user.' }}</p>
    </div>

    <form action="{{ $targetUser ? route('users.update', $targetUser->id) : route('users.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($targetUser)
            @method('PUT')
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h5 class="mb-0">Profile</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $targetUser?->first_name) }}" required>
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $targetUser?->last_name) }}" required>
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $targetUser?->email) }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $targetUser?->phone) }}">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hire Date</label>
                                <input type="date" name="hire_date" class="form-control @error('hire_date') is-invalid @enderror" value="{{ old('hire_date', optional($targetUser?->hire_date)->format('Y-m-d')) }}" required>
                                @error('hire_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hourly Rate</label>
                                <input type="number" name="hourly_rate" step="0.01" min="0" class="form-control @error('hourly_rate') is-invalid @enderror" value="{{ old('hourly_rate', $targetUser?->hourly_rate) }}">
                                @error('hourly_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address', $targetUser?->address) }}</textarea>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Emergency Contact</label>
                                <input type="text" name="emergency_contact" class="form-control @error('emergency_contact') is-invalid @enderror" value="{{ old('emergency_contact', $targetUser?->emergency_contact) }}">
                                @error('emergency_contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Emergency Phone</label>
                                <input type="text" name="emergency_phone" class="form-control @error('emergency_phone') is-invalid @enderror" value="{{ old('emergency_phone', $targetUser?->emergency_phone) }}">
                                @error('emergency_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h5 class="mb-0">Password</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ $targetUser ? 'New Password' : 'Password' }}</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" {{ $targetUser ? '' : 'required' }}>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" {{ $targetUser ? '' : 'required' }}>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3"><h5 class="mb-0">Custom Permissions</h5></div>
                    <div class="card-body">
                        @php
                            $selectedPermissions = old('permissions', $targetUser?->permissions ?? []);
                            $permissionOptions = [
                                'users.view', 'users.manage', 'roles.manage', 'departments.manage',
                                'products.view', 'products.manage', 'inventory.view', 'inventory.manage',
                                'sales.view', 'sales.create', 'sales.refund', 'customers.view', 'customers.manage',
                                'reports.view', 'reports.export', 'settings.manage', 'security.manage'
                            ];
                        @endphp
                        <div class="row g-2">
                            @foreach($permissionOptions as $permission)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="perm_{{ $permission }}" name="permissions[]" value="{{ $permission }}" {{ in_array($permission, $selectedPermissions, true) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="perm_{{ $permission }}">{{ $permission }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h5 class="mb-0">Access</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="">Select role...</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->slug }}" {{ old('role', $targetUser?->role) === $role->slug ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <select name="department_id" class="form-select @error('department_id') is-invalid @enderror">
                                <option value="">None</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ (string) old('department_id', $targetUser?->department_id) === (string) $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status', $targetUser?->is_active ? 'active' : 'inactive') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $targetUser?->is_active ? 'active' : 'inactive') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h5 class="mb-0">Profile Photo</h5></div>
                    <div class="card-body">
                        @if($targetUser?->profile_photo)
                            <img src="{{ asset('storage/' . $targetUser->profile_photo) }}" alt="Photo" class="img-fluid rounded mb-3" style="max-height: 200px; object-fit: cover;">
                        @endif
                        <input type="file" name="profile_photo" class="form-control @error('profile_photo') is-invalid @enderror" accept="image/*">
                        @error('profile_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-primary" type="submit">{{ $targetUser ? 'Update User' : 'Create User' }}</button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
