@extends('layouts.app')
@section('title', 'Photo Studio Reports')

@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-area">
    <div class="page-header mb-4">
        <div class="row align-items-center gy-3">
            <div class="col-md">
                <h1 class="page-title mb-1">Photo Studio Reports</h1>
                <p class="text-muted mb-0">Analyze operational, financial, occupancy, and customer performance.</p>
            </div>
            <div class="col-md-auto d-flex gap-2 flex-wrap">
                <a href="{{ route('photo-studio.reports.export', ['type' => 'daily', 'date' => now()->format('Y-m-d')]) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-file-export me-2"></i>Export Today
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 studio-management-card">
                <div class="card-body d-flex flex-column">
                    <h5><i class="fas fa-calendar-day me-2 text-primary"></i>Daily Report</h5>
                    <p class="text-muted flex-grow-1">Session volume, completion, daily revenue, and category split.</p>
                    <a href="{{ route('photo-studio.reports.daily', ['date' => now()->format('Y-m-d')]) }}" class="btn btn-primary">Open Daily Report</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 studio-management-card">
                <div class="card-body d-flex flex-column">
                    <h5><i class="fas fa-naira-sign me-2 text-success"></i>Revenue Report</h5>
                    <p class="text-muted flex-grow-1">Track revenue trends, payment methods, discounts, and averages.</p>
                    <a href="{{ route('photo-studio.reports.revenue', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" class="btn btn-success">Open Revenue Report</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 studio-management-card">
                <div class="card-body d-flex flex-column">
                    <h5><i class="fas fa-percentage me-2 text-warning"></i>Occupancy Report</h5>
                    <p class="text-muted flex-grow-1">Monitor utilization per category against available capacity.</p>
                    <a href="{{ route('photo-studio.reports.occupancy', ['date' => now()->format('Y-m-d')]) }}" class="btn btn-warning">Open Occupancy Report</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 studio-management-card">
                <div class="card-body d-flex flex-column">
                    <h5><i class="fas fa-user-friends me-2 text-info"></i>Customer Report</h5>
                    <p class="text-muted flex-grow-1">Review customer tiers, retention profile, and spending distribution.</p>
                    <a href="{{ route('photo-studio.reports.customers') }}" class="btn btn-info text-white">Open Customer Report</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 studio-management-card">
                <div class="card-body d-flex flex-column">
                    <h5><i class="fas fa-chart-line me-2 text-dark"></i>Category Performance</h5>
                    <p class="text-muted flex-grow-1">Compare category throughput, conversion, and utilization metrics.</p>
                    <a href="{{ route('photo-studio.reports.category-performance', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" class="btn btn-dark">Open Performance Report</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 studio-management-card">
                <div class="card-body d-flex flex-column">
                    <h5><i class="fas fa-history me-2 text-secondary"></i>Session History</h5>
                    <p class="text-muted flex-grow-1">Open detailed session logs and payment reconciliation per booking.</p>
                    <a href="{{ route('photo-studio.sessions.index') }}" class="btn btn-outline-secondary">Open Sessions</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
