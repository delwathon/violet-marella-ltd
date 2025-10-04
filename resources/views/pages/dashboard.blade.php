@extends('layouts.app')
@section('title', 'Dashboard - Violet Marella Limited')
@push('styles')
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Welcome to Violet Marella Management Suite</p>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">
                <i class="fas fa-chart-line me-2"></i>Generate Report
            </button>
        </div>
    </div>
</div>
<div class="dashboard-section">
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            @component('components.stat-card', [
                'value' => '₦2.4M',
                'label' => 'Total Revenue',
                'changeClass' => 'text-success',
                'icon' => 'fa-arrow-up',
                'changeText' => '+12% from last month'
            ])@endcomponent
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            @component('components.stat-card', [
                'value' => '847',
                'label' => 'Total Products',
                'changeClass' => 'text-warning',
                'icon' => 'fa-exclamation-triangle',
                'changeText' => '23 low stock'
            ])@endcomponent
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            @component('components.stat-card', [
                'value' => '156',
                'label' => 'Studio Sessions',
                'changeClass' => 'text-success',
                'icon' => 'fa-arrow-up',
                'changeText' => '+8% this week'
            ])@endcomponent
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            @component('components.stat-card', [
                'value' => '42',
                'label' => 'Active Rentals',
                'changeClass' => 'text-info',
                'icon' => 'fa-clock',
                'changeText' => '12 due today'
            ])@endcomponent
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-lg-4 mb-4">
            @component('components.module-card', [
                'type' => 'gift-store',
                'icon' => 'fa-gift',
                'title' => 'Gift Store',
                'description' => 'Manage inventory, track sales, and monitor stock levels for your gift store business.'
            ])
                <a href="{{ route('gift-store') }}" class="quick-action">
                    <i class="fas fa-boxes"></i> View Inventory
                </a>
                <a href="{{ route('gift-store') }}?action=add" class="quick-action">
                    <i class="fas fa-plus"></i> Add New Product
                </a>
                <a href="{{ route('reports') }}?type=gift-store" class="quick-action">
                    <i class="fas fa-chart-line"></i> Sales Report
                </a>
            @endcomponent
        </div>
        <div class="col-lg-4 mb-4">
            @component('components.module-card', [
                'type' => 'supermarket',
                'icon' => 'fa-shopping-cart',
                'title' => 'Mini Supermarket',
                'description' => 'Complete POS system with inventory management for your supermarket operations.'
            ])
                <a href="{{ route('supermarket') }}" class="quick-action">
                    <i class="fas fa-cash-register"></i> Open POS
                </a>
                <a href="{{ route('supermarket') }}?tab=inventory" class="quick-action">
                    <i class="fas fa-warehouse"></i> Manage Stock
                </a>
                <a href="{{ route('reports') }}?type=supermarket" class="quick-action">
                    <i class="fas fa-receipt"></i> Daily Sales
                </a>
            @endcomponent
        </div>
        <div class="col-lg-4 mb-4">
            @component('components.module-card', [
                'type' => 'music-studio',
                'icon' => 'fa-music',
                'title' => 'Music Studio',
                'description' => 'Time-based billing system with QR code generation for studio session management.'
            ])
                <a href="{{ route('music-studio') }}" class="quick-action">
                    <i class="fas fa-user-plus"></i> Check-in Customer
                </a>
                <a href="{{ route('music-studio') }}?tab=qr" class="quick-action">
                    <i class="fas fa-qrcode"></i> Generate QR Code
                </a>
                <a href="{{ route('music-studio') }}?tab=billing" class="quick-action">
                    <i class="fas fa-calculator"></i> Calculate Billing
                </a>
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Activities</h5>
                    <button class="btn btn-sm btn-outline-primary">View All</button>
                </div>
                <div class="card-body">
                    @component('components.activity-item', [
                        'iconBg' => 'bg-success',
                        'icon' => 'fa-shopping-cart',
                        'title' => 'New sale recorded',
                        'description' => "Valentine's Gift Box sold for ₦5,500",
                        'time' => '2 minutes ago'
                    ])@endcomponent
                    @component('components.activity-item', [
                        'iconBg' => 'bg-warning',
                        'icon' => 'fa-exclamation-triangle',
                        'title' => 'Low stock alert',
                        'description' => 'Birthday Card Set - Only 5 items remaining',
                        'time' => '15 minutes ago'
                    ])@endcomponent
                    @component('components.activity-item', [
                        'iconBg' => 'bg-info',
                        'icon' => 'fa-music',
                        'title' => 'Studio session completed',
                        'description' => 'John Smith - 2 hours, ₦4,000 billed',
                        'time' => '1 hour ago'
                    ])@endcomponent
                    @component('components.activity-item', [
                        'iconBg' => 'bg-primary',
                        'icon' => 'fa-guitar',
                        'title' => 'Instrument rental',
                        'description' => 'Guitar rented to Sarah Johnson for 3 days',
                        'time' => '2 hours ago'
                    ])@endcomponent
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('music-studio') }}" class="btn btn-outline-primary">
                            <i class="fas fa-user-plus me-2"></i>New Studio Check-in
                        </a>
                        <a href="{{ route('supermarket') }}" class="btn btn-outline-success">
                            <i class="fas fa-cash-register me-2"></i>Open POS
                        </a>
                        <a href="{{ route('gift-store') }}?action=add" class="btn btn-outline-warning">
                            <i class="fas fa-plus me-2"></i>Add Product
                        </a>
                        <a href="{{ route('reports') }}" class="btn btn-outline-info">
                            <i class="fas fa-chart-bar me-2"></i>Generate Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
