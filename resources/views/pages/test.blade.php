@extends('layouts.app')
@section('title', 'Test Dashboard - Violet Marella Limited')
@push('styles')
<!-- Add custom styles for test dashboard if needed -->
@endpush
@section('content')
<div class="main-content">
    <nav class="top-navbar" id="topNavbar"></nav>
    <div class="content-area">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="page-title">Test Dashboard</h1>
                    <p class="page-subtitle">This is a test dashboard area for layout and widget demonstration.</p>
                </div>
            </div>
        </div>
        <!-- Add test dashboard widgets, cards, and layout as needed, matching vb/test.html structure -->
    </div>
</div>
@endsection
