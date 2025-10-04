@extends('layouts.app')
@section('title', 'Reports & Analytics - Violet Marella Limited')
@push('styles')
<link href="{{ asset('assets/css/reports.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">Reports & Analytics</h1>
            <p class="page-subtitle">Business insights and performance analytics</p>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary">
                    <i class="fas fa-download me-2"></i>Export
                </button>
                <button class="btn btn-primary">
                    <i class="fas fa-chart-line me-2"></i>Custom Report
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Report filters, summary cards, charts, tabs, and modals would go here, matching vb/reports.html structure -->
@endsection
