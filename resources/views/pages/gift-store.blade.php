@extends('layouts.app')
@section('title', 'Gift Store - Violet Marella Limited')
@push('styles')
<link href="{{ asset('assets/css/gift-store.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="main-content">
    <nav class="top-navbar" id="topNavbar"></nav>
    <div class="content-area">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="page-title">Gift Store Management</h1>
                    <p class="page-subtitle">Manage your gift store inventory, sales, and customer orders</p>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus me-2"></i>Add New Product
                    </button>
                </div>
            </div>
        </div>
        <!-- Add gift store content here -->
    </div>
</div>
@endsection
