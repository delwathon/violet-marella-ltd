@extends('layouts.app')
@section('title', 'Category Performance Report')

@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush

@section('content')
@php
    $start = \Illuminate\Support\Carbon::parse($startDate);
    $end = \Illuminate\Support\Carbon::parse($endDate);
    $totalRevenue = $performanceData->sum('revenue');
    $totalSessions = $performanceData->sum('total_sessions');
    $completedSessions = $performanceData->sum('completed_sessions');
    $avgSessionValue = $completedSessions > 0 ? ($totalRevenue / $completedSessions) : 0;
@endphp

<div class="content-area">
    <div class="page-header mb-4">
        <div class="row align-items-center gy-3">
            <div class="col-md">
                <h1 class="page-title mb-1">Category Performance</h1>
                <p class="text-muted mb-0">Comparative performance by category ({{ $start->format('d M Y') }} - {{ $end->format('d M Y') }}).</p>
            </div>
            <div class="col-md-auto d-flex gap-2 flex-wrap">
                <a href="{{ route('photo-studio.reports.export', ['type' => 'category-performance', 'date' => now()->format('Y-m-d')]) }}" class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i>Export CSV</a>
                <a href="{{ route('photo-studio.reports.index') }}" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-2"></i>Back</a>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('photo-studio.reports.category-performance') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" name="start_date" value="{{ $start->format('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" name="end_date" value="{{ $end->format('Y-m-d') }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i>Apply</button>
                    <a href="{{ route('photo-studio.reports.category-performance', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" class="btn btn-outline-secondary">This Month</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3"><div class="stat-card-simple"><div class="stat-icon bg-primary"><i class="fas fa-layer-group"></i></div><div><small class="text-muted">Categories</small><div class="fw-bold">{{ $performanceData->count() }}</div></div></div></div>
        <div class="col-md-3 mb-3"><div class="stat-card-simple"><div class="stat-icon bg-info"><i class="fas fa-camera"></i></div><div><small class="text-muted">Total Sessions</small><div class="fw-bold">{{ number_format($totalSessions) }}</div></div></div></div>
        <div class="col-md-3 mb-3"><div class="stat-card-simple"><div class="stat-icon bg-success"><i class="fas fa-naira-sign"></i></div><div><small class="text-muted">Total Revenue</small><div class="fw-bold text-success">₦{{ number_format($totalRevenue, 2) }}</div></div></div></div>
        <div class="col-md-3 mb-3"><div class="stat-card-simple"><div class="stat-icon bg-warning"><i class="fas fa-calculator"></i></div><div><small class="text-muted">Avg Session Value</small><div class="fw-bold">₦{{ number_format($avgSessionValue, 2) }}</div></div></div></div>
    </div>

    <div class="card">
        <div class="card-header"><h6 class="mb-0">Category Metrics</h6></div>
        <div class="card-body p-0">
            @if($performanceData->isEmpty())
            <div class="text-center py-4 text-muted">No performance data for selected range.</div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Total Sessions</th>
                            <th>Completed</th>
                            <th>Cancelled</th>
                            <th>No Show</th>
                            <th>Total People</th>
                            <th>Avg Duration</th>
                            <th>Revenue</th>
                            <th>Avg Value</th>
                            <th>Utilization</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($performanceData as $row)
                        <tr>
                            <td class="fw-semibold">{{ $row['category'] }}</td>
                            <td>{{ number_format($row['total_sessions']) }}</td>
                            <td>{{ number_format($row['completed_sessions']) }}</td>
                            <td>{{ number_format($row['cancelled_sessions']) }}</td>
                            <td>{{ number_format($row['no_show']) }}</td>
                            <td>{{ number_format($row['total_people']) }}</td>
                            <td>{{ number_format((float)$row['average_duration'], 2) }} min</td>
                            <td class="text-success fw-semibold">₦{{ number_format($row['revenue'], 2) }}</td>
                            <td>₦{{ number_format((float)$row['average_session_value'], 2) }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 8px; min-width: 120px;">
                                        <div class="progress-bar {{ $row['utilization'] > 75 ? 'bg-success' : ($row['utilization'] > 40 ? 'bg-warning' : 'bg-secondary') }}" style="width: {{ min(100, $row['utilization']) }}%"></div>
                                    </div>
                                    <strong>{{ number_format($row['utilization'], 2) }}%</strong>
                                </div>
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
