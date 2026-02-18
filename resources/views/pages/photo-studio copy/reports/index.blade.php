@extends('layouts.app')
@section('title', 'Studio Reports')
@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area" style="background-color: #f9fafb;">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title mb-1" style="color: #1f2937;">Photo Studio Reports</h1>
                <p class="text-muted mb-0">View and analyze studio performance</p>
            </div>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="row">
        <!-- Daily Report -->
        <div class="col-lg-6 mb-4">
            <div class="card" style="background: white;">
                <div class="card-body" style="background: white;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon bg-primary me-3">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="color: #1f2937;">Daily Report</h5>
                            <p class="text-muted mb-0">View daily session and revenue reports</p>
                        </div>
                    </div>
                    <p class="text-muted">Get detailed insights into daily studio operations, sessions, and revenue.</p>
                    <a href="{{ route('photo-studio.reports.daily') }}" class="btn btn-primary">
                        <i class="fas fa-chart-line me-2"></i>View Daily Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Revenue Report -->
        <div class="col-lg-6 mb-4">
            <div class="card" style="background: white;">
                <div class="card-body" style="background: white;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon bg-success me-3">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="color: #1f2937;">Revenue Report</h5>
                            <p class="text-muted mb-0">Analyze revenue by studio and payment method</p>
                        </div>
                    </div>
                    <p class="text-muted">Track revenue trends, payment methods, and studio performance.</p>
                    <a href="{{ route('photo-studio.reports.revenue') }}" class="btn btn-success">
                        <i class="fas fa-dollar-sign me-2"></i>View Revenue Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Occupancy Report -->
        <div class="col-lg-6 mb-4">
            <div class="card" style="background: white;">
                <div class="card-body" style="background: white;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon bg-warning me-3">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="color: #1f2937;">Occupancy Report</h5>
                            <p class="text-muted mb-0">Monitor studio utilization rates</p>
                        </div>
                    </div>
                    <p class="text-muted">Analyze studio occupancy rates and optimize scheduling.</p>
                    <a href="{{ route('photo-studio.reports.occupancy') }}" class="btn btn-warning text-white">
                        <i class="fas fa-chart-bar me-2"></i>View Occupancy Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Customer Report -->
        <div class="col-lg-6 mb-4">
            <div class="card" style="background: white;">
                <div class="card-body" style="background: white;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon bg-info me-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="color: #1f2937;">Customer Report</h5>
                            <p class="text-muted mb-0">View customer statistics and behavior</p>
                        </div>
                    </div>
                    <p class="text-muted">Understand customer spending patterns and session history.</p>
                    <a href="{{ route('photo-studio.reports.customers') }}" class="btn btn-info text-white">
                        <i class="fas fa-user-chart me-2"></i>View Customer Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Export Section -->
    <div class="card" style="background: white;">
        <div class="card-header" style="background: white; border-bottom: 1px solid #e5e7eb;">
            <h5 class="mb-0" style="color: #1f2937;">Quick Export</h5>
        </div>
        <div class="card-body" style="background: white;">
            <form action="{{ route('photo-studio.reports.export') }}" method="GET" class="row align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Report Type</label>
                    <select class="form-select" name="type">
                        <option value="daily">Daily Report</option>
                        <option value="revenue">Revenue Report</option>
                        <option value="occupancy">Occupancy Report</option>
                        <option value="customers">Customer Report</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-download me-2"></i>Export to CSV
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('assets/js/photo-studio.js') }}"></script>
@endpush
@endsection