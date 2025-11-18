@extends('layouts.app')
@section('title', 'Daily Report')
@push('styles')
<link href="{{ asset('assets/css/photo-studio-light.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area" style="background-color: #f9fafb;">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title mb-1" style="color: #1f2937;">Daily Report</h1>
                <p class="text-muted mb-0">Daily studio sessions and revenue</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('photo-studio.reports.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Reports
                </a>
            </div>
        </div>
    </div>

    <!-- Date Selector -->
    <div class="card mb-4" style="background: white;">
        <div class="card-body" style="background: white;">
            <form method="GET" action="{{ route('photo-studio.reports.daily') }}">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Select Date</label>
                        <input type="date" class="form-control" name="date" value="{{ $date ?? date('Y-m-d') }}">
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

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card" style="background: white;">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Sessions</div>
                    <div class="stat-value">{{ $stats['total_sessions'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card" style="background: white;">
                <div class="stat-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Completed</div>
                    <div class="stat-value">{{ $stats['completed'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card" style="background: white;">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value">₦{{ number_format($stats['total_revenue'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card" style="background: white;">
                <div class="stat-icon bg-info">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Minutes</div>
                    <div class="stat-value">{{ $stats['total_minutes'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sessions List -->
    <div class="card" style="background: white;">
        <div class="card-header" style="background: white; border-bottom: 1px solid #e5e7eb;">
            <h5 class="mb-0" style="color: #1f2937;">Sessions for {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</h5>
        </div>
        <div class="card-body" style="background: white;">
            @if($sessions->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No sessions found for this date</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table" style="background: white;">
                        <thead style="background-color: #f9fafb;">
                            <tr>
                                <th style="color: #1f2937;">Time</th>
                                <th style="color: #1f2937;">Customer</th>
                                <th style="color: #1f2937;">Studio</th>
                                <th style="color: #1f2937;">Duration</th>
                                <th style="color: #1f2937;">Amount</th>
                                <th style="color: #1f2937;">Payment</th>
                                <th style="color: #1f2937;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $session)
                            <tr style="background: white;">
                                <td style="color: #1f2937;">{{ $session->check_in_time->format('g:i A') }}</td>
                                <td style="color: #1f2937;">{{ $session->customer->name }}</td>
                                <td style="color: #1f2937;">{{ $session->studio->name }}</td>
                                <td style="color: #1f2937;">{{ $session->actual_duration ?? 'N/A' }} min</td>
                                <td style="color: #1f2937;">₦{{ number_format($session->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $session->payment_status === 'paid' ? 'success' : 'warning' }}">
                                        {{ ucfirst($session->payment_status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $session->status === 'completed' ? 'primary' : 'success' }}">
                                        {{ ucfirst($session->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection