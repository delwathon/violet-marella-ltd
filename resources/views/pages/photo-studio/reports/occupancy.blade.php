@extends('layouts.app')
@section('title', 'Occupancy Report')

@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush

@section('content')
@php
    $reportDate = \Illuminate\Support\Carbon::parse($date);
    $totalSessions = $occupancyData->sum('sessions');
    $totalMinutes = $occupancyData->sum('total_minutes');
    $avgOccupancy = $occupancyData->count() > 0 ? $occupancyData->avg('occupancy_rate') : 0;
    $totalRevenue = $occupancyData->sum('revenue');
@endphp

<div class="content-area">
    <div class="page-header mb-4">
        <div class="row align-items-center gy-3">
            <div class="col-md">
                <h1 class="page-title mb-1">Occupancy Report</h1>
                <p class="text-muted mb-0">Capacity utilization for {{ $reportDate->format('d M Y') }}.</p>
            </div>
            <div class="col-md-auto d-flex gap-2 flex-wrap">
                <a href="{{ route('photo-studio.reports.export', ['type' => 'occupancy', 'date' => $reportDate->format('Y-m-d')]) }}" class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i>Export CSV</a>
                <a href="{{ route('photo-studio.reports.index') }}" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-2"></i>Back</a>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('photo-studio.reports.occupancy') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Report Date</label>
                    <input type="date" class="form-control" name="date" value="{{ $reportDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-8 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i>Load Report</button>
                    <a href="{{ route('photo-studio.reports.occupancy', ['date' => now()->format('Y-m-d')]) }}" class="btn btn-outline-secondary">Today</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple"><div class="stat-icon bg-primary"><i class="fas fa-camera"></i></div><div><small class="text-muted">Total Sessions</small><div class="fw-bold">{{ number_format($totalSessions) }}</div></div></div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple"><div class="stat-icon bg-info"><i class="fas fa-clock"></i></div><div><small class="text-muted">Total Minutes</small><div class="fw-bold">{{ number_format($totalMinutes) }}</div></div></div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple"><div class="stat-icon bg-warning"><i class="fas fa-percentage"></i></div><div><small class="text-muted">Avg Occupancy</small><div class="fw-bold">{{ number_format($avgOccupancy, 2) }}%</div></div></div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple"><div class="stat-icon bg-success"><i class="fas fa-naira-sign"></i></div><div><small class="text-muted">Revenue</small><div class="fw-bold text-success">₦{{ number_format($totalRevenue, 2) }}</div></div></div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><h6 class="mb-0">Category Occupancy Breakdown</h6></div>
        <div class="card-body p-0">
            @if($occupancyData->isEmpty())
            <div class="text-center py-4 text-muted">No occupancy data available.</div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Sessions</th>
                            <th>Total Minutes</th>
                            <th>Max Concurrent</th>
                            <th>Average Duration</th>
                            <th>Revenue</th>
                            <th>Occupancy Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($occupancyData as $row)
                        <tr>
                            <td>{{ $row['category'] }}</td>
                            <td>{{ number_format($row['sessions']) }}</td>
                            <td>{{ number_format($row['total_minutes']) }}</td>
                            <td>{{ number_format($row['max_concurrent']) }}</td>
                            <td>{{ number_format($row['average_duration'], 2) }} min</td>
                            <td class="fw-semibold text-success">₦{{ number_format($row['revenue'], 2) }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 8px; min-width: 120px;">
                                        <div class="progress-bar {{ $row['occupancy_rate'] > 75 ? 'bg-success' : ($row['occupancy_rate'] > 40 ? 'bg-warning' : 'bg-secondary') }}" style="width: {{ min(100, $row['occupancy_rate']) }}%"></div>
                                    </div>
                                    <strong>{{ number_format($row['occupancy_rate'], 2) }}%</strong>
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
