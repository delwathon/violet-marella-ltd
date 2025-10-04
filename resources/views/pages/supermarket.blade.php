@extends('layouts.app')
@section('title', 'Mini Supermarket - Violet Marella Limited')
@push('styles')
<link href="{{ asset('assets/css/supermarket.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">Mini Supermarket POS</h1>
            <p class="page-subtitle">Point of Sale system with inventory management</p>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#newSaleModal">
                    <i class="fas fa-plus me-2"></i>New Sale
                </button>
                <button class="btn btn-outline-primary">
                    <i class="fas fa-cash-register me-2"></i>Cash Drawer
                </button>
                <button class="btn btn-outline-secondary">
                    <i class="fas fa-print me-2"></i>X-Report
                </button>
            </div>
        </div>
    </div>
</div>
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-success"><i class="fas fa-cash-register"></i></div>
            <div class="stat-info">
                <div class="stat-value">₦0</div>
                <div class="stat-label">Today's Sales</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary"><i class="fas fa-receipt"></i></div>
            <div class="stat-info">
                <div class="stat-value">0</div>
                <div class="stat-label">Transactions</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning"><i class="fas fa-boxes"></i></div>
            <div class="stat-info">
                <div class="stat-value">0</div>
                <div class="stat-label">Items in Stock</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-info"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <div class="stat-value">0</div>
                <div class="stat-label">Customers Served</div>
            </div>
        </div>
    </div>
</div>
<!-- POS Interface, Cart, and Modals would go here, matching the vb/supermarket.html structure -->
@endsection
