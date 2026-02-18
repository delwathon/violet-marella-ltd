@extends('layouts.app')
@section('title', 'User Profile')

@section('content')
<div class="content-area">
    <div class="mb-4">
        <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary mb-2"><i class="fas fa-arrow-left me-1"></i>Back</a>
        <h1 class="h3 mb-1 fw-bold">{{ $targetUser->full_name }}</h1>
        <p class="text-muted mb-0">User profile and account details.</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    @if($targetUser->profile_photo)
                        <img src="{{ asset('storage/' . $targetUser->profile_photo) }}" alt="{{ $targetUser->full_name }}" class="rounded-circle mb-3" width="120" height="120">
                    @else
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex justify-content-center align-items-center mb-3" style="width:120px;height:120px;font-size:2rem;">
                            {{ strtoupper(substr($targetUser->first_name, 0, 1)) }}
                        </div>
                    @endif
                    <h5 class="mb-1">{{ $targetUser->full_name }}</h5>
                    <p class="text-muted mb-2">{{ $targetUser->email }}</p>
                    <span class="badge bg-{{ $targetUser->is_active ? 'success' : 'secondary' }}">{{ $targetUser->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3"><h5 class="mb-0">Account Details</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6"><strong>Role:</strong> {{ $targetUser->roleRecord?->name ?? $targetUser->role }}</div>
                        <div class="col-md-6"><strong>Department:</strong> {{ $targetUser->department?->name ?? '-' }}</div>
                        <div class="col-md-6"><strong>Phone:</strong> {{ $targetUser->phone ?? '-' }}</div>
                        <div class="col-md-6"><strong>Hire Date:</strong> {{ optional($targetUser->hire_date)->format('Y-m-d') ?? '-' }}</div>
                        <div class="col-md-6"><strong>Hourly Rate:</strong> {{ $targetUser->hourly_rate !== null ? number_format((float) $targetUser->hourly_rate, 2) : '-' }}</div>
                        <div class="col-md-6"><strong>Last Login:</strong> {{ optional($targetUser->last_login_at)->diffForHumans() ?? 'Never' }}</div>
                        <div class="col-12"><strong>Address:</strong> {{ $targetUser->address ?? '-' }}</div>
                        <div class="col-md-6"><strong>Emergency Contact:</strong> {{ $targetUser->emergency_contact ?? '-' }}</div>
                        <div class="col-md-6"><strong>Emergency Phone:</strong> {{ $targetUser->emergency_phone ?? '-' }}</div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <a href="{{ route('users.edit', $targetUser->id) }}" class="btn btn-primary">Edit User</a>
                        <a href="{{ route('users.permissions', $targetUser->id) }}" class="btn btn-outline-primary">Manage Permissions</a>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white py-3"><h5 class="mb-0">Custom Permissions</h5></div>
                <div class="card-body">
                    @if(!empty($targetUser->permissions))
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($targetUser->permissions as $permission)
                                <span class="badge bg-light text-dark">{{ $permission }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No custom permissions set. Role defaults are used.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
