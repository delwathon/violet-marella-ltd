@extends('layouts.app')
@section('title', 'Daily Studio Report')

@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush

@section('content')
@php
    $reportDate = \Illuminate\Support\Carbon::parse($date);
@endphp

<div class="content-area">
    <div class="page-header mb-4">
        <div class="row align-items-center gy-3">
            <div class="col-md">
                <h1 class="page-title mb-1">Daily Report</h1>
                <p class="text-muted mb-0">Operational summary for {{ $reportDate->format('d M Y') }}.</p>
            </div>
            <div class="col-md-auto d-flex gap-2 flex-wrap">
                <a href="{{ route('photo-studio.reports.export', ['type' => 'daily', 'date' => $reportDate->format('Y-m-d')]) }}" class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i>Export CSV</a>
                <a href="{{ route('photo-studio.reports.index') }}" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-2"></i>Back</a>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('photo-studio.reports.daily') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Report Date</label>
                    <input type="date" class="form-control" name="date" value="{{ $reportDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-8 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i>Load Report</button>
                    <a href="{{ route('photo-studio.reports.daily', ['date' => now()->format('Y-m-d')]) }}" class="btn btn-outline-secondary">Today</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple"><div class="stat-icon bg-primary"><i class="fas fa-camera"></i></div><div><small class="text-muted">Total Sessions</small><div class="fw-bold">{{ number_format($stats['total_sessions']) }}</div></div></div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple"><div class="stat-icon bg-success"><i class="fas fa-check-circle"></i></div><div><small class="text-muted">Completed</small><div class="fw-bold">{{ number_format($stats['completed']) }}</div></div></div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple"><div class="stat-icon bg-warning"><i class="fas fa-clock"></i></div><div><small class="text-muted">Active</small><div class="fw-bold">{{ number_format($stats['active']) }}</div></div></div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple"><div class="stat-icon bg-info"><i class="fas fa-naira-sign"></i></div><div><small class="text-muted">Revenue</small><div class="fw-bold text-success">₦{{ number_format($stats['total_revenue'], 2) }}</div></div></div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-5 mb-3">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Summary Metrics</h6></div>
                <div class="card-body table-responsive">
                    <table class="table table-sm mb-0">
                        <tr><td>Pending Payment</td><td class="text-end">₦{{ number_format($stats['pending_payment'], 2) }}</td></tr>
                        <tr><td>Total Minutes (Completed)</td><td class="text-end">{{ number_format($stats['total_minutes']) }} min</td></tr>
                        <tr><td>Unique Customers</td><td class="text-end">{{ number_format($stats['total_customers']) }}</td></tr>
                        <tr><td>Average Party Size</td><td class="text-end">{{ number_format((float)$stats['average_party_size'], 2) }}</td></tr>
                        <tr><td>Cancelled</td><td class="text-end">{{ number_format($stats['cancelled']) }}</td></tr>
                        <tr><td>No Show</td><td class="text-end">{{ number_format($stats['no_show']) }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-7 mb-3">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Revenue by Category</h6></div>
                <div class="card-body p-0">
                    @if($byCategory->isEmpty())
                    <div class="text-center py-4 text-muted">No paid sessions for this date.</div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Sessions</th>
                                    <th>Revenue</th>
                                    <th>Avg Session Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($byCategory as $row)
                                <tr>
                                    <td>{{ $row['category'] }}</td>
                                    <td>{{ $row['sessions'] }}</td>
                                    <td class="fw-semibold text-success">₦{{ number_format($row['revenue'], 2) }}</td>
                                    <td>₦{{ number_format($row['sessions'] > 0 ? ($row['revenue'] / $row['sessions']) : 0, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h6 class="mb-0">Session Log</h6></div>
        <div class="card-body p-0">
            @if($sessions->isEmpty())
            <div class="text-center py-4 text-muted">No sessions recorded on this date.</div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Session</th>
                            <th>Customer</th>
                            <th>Category</th>
                            <th>Check In</th>
                            <th>Duration</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $session)
                        <tr>
                            <td>{{ $session->session_code }}</td>
                            <td>{{ $session->customer->name }}</td>
                            <td>{{ $session->category->name }}</td>
                            <td>{{ $session->check_in_time->format('h:i A') }}</td>
                            <td>{{ $session->actual_duration ?? $session->booked_duration }} min</td>
                            <td>₦{{ number_format($session->total_amount, 2) }}</td>
                            <td><span class="badge bg-{{ $session->status === 'completed' ? 'success' : ($session->status === 'cancelled' ? 'secondary' : 'warning') }}">{{ $session->status_label }}</span></td>
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
