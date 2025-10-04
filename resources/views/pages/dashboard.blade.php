@extends('layouts.app')
@section('title', 'Dashboard - Violet Marella Limited')
@push('styles')
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="main-content">
    <nav class="top-navbar" id="topNavbar"></nav>
    <div class="content-area">
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
        <!-- Add dashboard widgets/content here -->
    </div>
</div>
@endsection
