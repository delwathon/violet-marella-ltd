@extends('layouts.app')
@section('title', 'Music Studio - Violet Marella Limited')
@push('styles')
<link href="{{ asset('assets/css/music-studio.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">Music Studio Management</h1>
            <p class="page-subtitle">Manage studio sessions, customer check-ins, and time-based billing</p>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkInModal">
                <i class="fas fa-user-plus me-2"></i>New Check-in
            </button>
        </div>
    </div>
</div>
<!-- Studio status cards, tabs, summary, rates, and quick actions would go here, matching vb/music-studio.html structure -->
@endsection
