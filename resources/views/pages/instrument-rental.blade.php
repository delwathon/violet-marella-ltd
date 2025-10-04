@extends('layouts.app')
@section('title', 'Instrument Rental - Violet Marella Limited')
@push('styles')
<link href="{{ asset('assets/css/instrument-rental.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="main-content">
    <nav class="top-navbar" id="topNavbar"></nav>
    <div class="content-area">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="page-title">Instrument Rental</h1>
                    <p class="page-subtitle">Manage musical instrument bookings and availability</p>
                </div>
                <div class="col-auto">
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newRentalModal">
                            <i class="fas fa-plus me-2"></i>New Rental
                        </button>
                        <button class="btn btn-outline-success">
                            <i class="fas fa-guitar me-2"></i>Add Instrument
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add instrument rental content here -->
    </div>
</div>
@endsection
