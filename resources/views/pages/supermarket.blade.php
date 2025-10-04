@extends('layouts.app')
@section('title', 'Mini Supermarket - Violet Marella Limited')
@push('styles')
<link href="{{ asset('assets/css/supermarket.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="main-content">
    <nav class="top-navbar" id="topNavbar"></nav>
    <div class="content-area">
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
                    </div>
                </div>
            </div>
        </div>
        <!-- Add supermarket content here -->
    </div>
</div>
@endsection
