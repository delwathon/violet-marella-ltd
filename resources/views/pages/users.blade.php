@extends('layouts.app')
@section('title', 'User Management - Violet Marella Limited')
@push('styles')
<link href="{{ asset('assets/css/users.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="main-content">
    <nav class="top-navbar" id="topNavbar"></nav>
    <div class="content-area">
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
        <!-- User statistics, tabs, tables, and modals would go here, matching vb/users.html structure -->
    </div>
</div>
@endsection
