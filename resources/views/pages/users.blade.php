@extends('layouts.app')
@section('title', 'User Management')
@push('styles')
<link href="{{ asset('assets/css/users.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">User Management</h1>
                <p class="page-subtitle">Manage user accounts, roles, and permissions</p>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary">
                        <i class="fas fa-download me-2"></i>Export
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fas fa-user-plus me-2"></i>Add User
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- User Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">12</div>
                    <div class="stat-label">Total Users</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">10</div>
                    <div class="stat-label">Active Users</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">2</div>
                    <div class="stat-label">Pending Users</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">3</div>
                    <div class="stat-label">Administrators</div>
                </div>
            </div>
        </div>
    </div>
    <!-- User Management Tabs -->
    <ul class="nav nav-tabs mb-4" id="userTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button">
                <i class="fas fa-users me-2"></i>All Users
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles" type="button">
                <i class="fas fa-user-tag me-2"></i>Roles & Permissions
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button">
                <i class="fas fa-history me-2"></i>User Activity
            </button>
        </li>
    </ul>
    <!-- Tab Content -->
    <div class="tab-content" id="userTabContent">
        <!-- Users Tab -->
        <div class="tab-pane fade show active" id="users" role="tabpanel">
            ...existing code from users table section...
        </div>
        <!-- Roles & Permissions Tab -->
        <div class="tab-pane fade" id="roles" role="tabpanel">
            ...existing code from roles and permissions section...
        </div>
        <!-- User Activity Tab -->
        <div class="tab-pane fade" id="activity" role="tabpanel">
            ...existing code from user activity section...
        </div>
    </div>
    <!-- Add User Modal -->
    ...existing code from add user modal...
    <!-- Edit User Modal -->
    ...existing code from edit user modal...
    <!-- Add Role Modal -->
    ...existing code from add role modal...
</div>
@push('scripts')
<script src="{{ asset('assets/js/users.js') }}"></script>
@endpush
@endsection
