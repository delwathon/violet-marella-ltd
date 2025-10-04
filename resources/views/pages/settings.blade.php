@extends('layouts.app')
@section('title', 'Settings - Violet Marella Limited')
@push('styles')
<link href="{{ asset('assets/css/settings.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="page-header">
    <h1 class="page-title">Settings</h1>
    <p class="page-subtitle">System configuration and preferences</p>
</div>
<!-- Settings navigation, tabs, and forms would go here, matching vb/settings.html structure -->
@endsection
