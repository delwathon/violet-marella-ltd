@extends('layouts.app')
@section('title', 'Settings - Violet Marella Limited')
@push('styles')
<link href="{{ asset('assets/css/settings.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="main-content">
    <nav class="top-navbar" id="topNavbar"></nav>
    <div class="content-area">
        <div class="page-header">
            <h1 class="page-title">Settings</h1>
            <p class="page-subtitle">System configuration and preferences</p>
        </div>
        <!-- Add settings content here -->
    </div>
</div>
@endsection
