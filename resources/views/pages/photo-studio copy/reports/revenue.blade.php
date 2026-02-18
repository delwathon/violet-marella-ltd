@extends('layouts.app')
@section('title', 'Revenue Report')
@push('styles')
<link href="{{ asset('assets/css/photo-studio-light.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area" style="background-color: #f9fafb;">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title mb-1" style="color: #1f2937;">Revenue Report</h1>
                <p class="text-muted mb-0">Analyze revenue by studio and payment method</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('photo-studio.reports.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Reports
                </a>
            </div>
        </div>
    </div>

    <!-- Date Range Selector -->
    <div class="card mb-4" style="background: white;">
        <div class="card-body" style="background: white;">
            <form method="GET" action="{{ route('photo-studio.reports.revenue') }}">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date" value="{{ $startDate ?? \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date" value="{{ $endDate ?? \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>View Report
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Revenue Statistics -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stat-card" style="background: white;">
                <div class="stat-icon bg-success">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value">₦{{ number_format($stats['total_revenue'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card" style="background: white;">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Sessions</div>
                    <div class="stat-value">{{ $stats['total_sessions'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card" style="background: white;">
                <div class="stat-icon bg-info">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Average Value</div>
                    <div class="stat-value">₦{{ number_format($stats['average_session_value'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Revenue by Studio -->
        <div class="col-md-6 mb-4">
            <div class="card" style="background: white;">
                <div class="card-header" style="background: white; border-bottom: 1px solid #e5e7eb;">
                    <h5 class="mb-0" style="color: #1f2937;">Revenue by Studio</h5>
                </div>
                <div class="card-body" style="background: white;">
                    <div class="table-responsive">
                        <table class="table" style="background: white;">
                            <thead style="background-color: #f9fafb;">
                                <tr>
                                    <th style="color: #1f2937;">Studio</th>
                                    <th style="color: #1f2937;">Sessions</th>
                                    <th style="color: #1f2937;">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['by_studio'] as $studio)
                                <tr style="background: white;">
                                    <td style="color: #1f2937;">{{ $studio['studio'] }}</td>
                                    <td style="color: #1f2937;">{{ $studio['sessions'] }}</td>
                                    <td style="color: #1f2937;"><strong class="text-success">₦{{ number_format($studio['revenue'], 2) }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue by Payment Method -->
        <div class="col-md-6 mb-4">
            <div class="card" style="background: white;">
                <div class="card-header" style="background: white; border-bottom: 1px solid #e5e7eb;">
                    <h5 class="mb-0" style="color: #1f2937;">Revenue by Payment Method</h5>
                </div>
                <div class="card-body" style="background: white;">
                    <div class="table-responsive">
                        <table class="table" style="background: white;">
                            <thead style="background-color: #f9fafb;">
                                <tr>
                                    <th style="color: #1f2937;">Method</th>
                                    <th style="color: #1f2937;">Count</th>
                                    <th style="color: #1f2937;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['by_payment_method'] as $method)
                                <tr style="background: white;">
                                    <td style="color: #1f2937;">{{ ucfirst($method['method']) }}</td>
                                    <td style="color: #1f2937;">{{ $method['count'] }}</td>
                                    <td style="color: #1f2937;"><strong class="text-success">₦{{ number_format($method['amount'], 2) }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection