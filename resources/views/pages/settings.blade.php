@extends('layouts.app')
@section('title', 'Settings')
@push('styles')
<link href="{{ asset('assets/css/settings.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Settings</h1>
        <p class="page-subtitle">System configuration and preferences</p>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <div class="settings-nav">
                <div class="nav flex-column nav-pills" id="settings-tab" role="tablist">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="pill" data-bs-target="#general" type="button">
                        <i class="fas fa-cog me-2"></i>General
                    </button>
                    <button class="nav-link" id="business-tab" data-bs-toggle="pill" data-bs-target="#business" type="button">
                        <i class="fas fa-building me-2"></i>Business Info
                    </button>
                    <button class="nav-link" id="payment-tab" data-bs-toggle="pill" data-bs-target="#payment" type="button">
                        <i class="fas fa-credit-card me-2"></i>Payment Methods
                    </button>
                    <button class="nav-link" id="notifications-tab" data-bs-toggle="pill" data-bs-target="#notifications" type="button">
                        <i class="fas fa-bell me-2"></i>Notifications
                    </button>
                    <button class="nav-link" id="security-tab" data-bs-toggle="pill" data-bs-target="#security" type="button">
                        <i class="fas fa-shield-alt me-2"></i>Security
                    </button>
                    <button class="nav-link" id="backup-tab" data-bs-toggle="pill" data-bs-target="#backup" type="button">
                        <i class="fas fa-database me-2"></i>Backup & Export
                    </button>
                    <button class="nav-link" id="integrations-tab" data-bs-toggle="pill" data-bs-target="#integrations" type="button">
                        <i class="fas fa-plug me-2"></i>Integrations
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="tab-content" id="settings-tabContent">
                <!-- General Settings -->
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    ...existing code from general settings section...
                </div>
                <!-- Business Information -->
                <div class="tab-pane fade" id="business" role="tabpanel">
                    ...existing code from business info section...
                </div>
                <!-- Payment Methods -->
                <div class="tab-pane fade" id="payment" role="tabpanel">
                    ...existing code from payment methods section...
                </div>
                <!-- Notifications -->
                <div class="tab-pane fade" id="notifications" role="tabpanel">
                    ...existing code from notifications section...
                </div>
                <!-- Security -->
                <div class="tab-pane fade" id="security" role="tabpanel">
                    ...existing code from security section...
                </div>
                <!-- Backup & Export -->
                <div class="tab-pane fade" id="backup" role="tabpanel">
                    ...existing code from backup & export section...
                </div>
                <!-- Integrations -->
                <div class="tab-pane fade" id="integrations" role="tabpanel">
                    ...existing code from integrations section...
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('assets/js/settings.js') }}"></script>
@endpush
