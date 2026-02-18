@extends('layouts.app')
@section('title', 'Occupancy Report')
@push('styles')
<link href="{{ asset('assets/css/photo-studio-light.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area" style="background-color: #f9fafb;">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title mb-1" style="color: #1f2937;">Occupancy Report</h1>
                <p class="text-muted mb-0">Monitor studio utilization rates</p>
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
            <form method="GET" action="{{ route('photo-studio.reports.occupancy') }}">
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

    <!-- Occupancy Data -->
    <div class="card" style="background: white;">
        <div class="card-header" style="background: white; border-bottom: 1px solid #e5e7eb;">
            <h5 class="mb-0" style="color: #1f2937;">Studio Occupancy for {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</h5>
        </div>
        <div class="card-body" style="background: white;">
            @if($occupancyData->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-door-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No occupancy data available</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table" style="background: white;">
                        <thead style="background-color: #f9fafb;">
                            <tr>
                                <th style="color: #1f2937;">Studio</th>
                                <th style="color: #1f2937;">Sessions</th>
                                <th style="color: #1f2937;">Total Minutes</th>
                                <th style="color: #1f2937;">Occupancy Rate</th>
                                <th style="color: #1f2937;">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($occupancyData as $data)
                            <tr style="background: white;">
                                <td style="color: #1f2937;"><strong>{{ $data['studio'] }}</strong></td>
                                <td style="color: #1f2937;">{{ $data['sessions'] }}</td>
                                <td style="color: #1f2937;">{{ $data['total_minutes'] }} min</td>
                                <td style="color: #1f2937;">
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 20px; width: 150px;">
                                            <div class="progress-bar bg-{{ $data['occupancy_rate'] > 75 ? 'success' : ($data['occupancy_rate'] > 50 ? 'warning' : 'danger') }}" 
                                                 style="width: {{ $data['occupancy_rate'] }}%">
                                                {{ $data['occupancy_rate'] }}%
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="color: #1f2937;"><strong class="text-success">₦{{ number_format($data['revenue'], 2) }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-light mt-3">
                    <strong>Note:</strong> Occupancy rate is calculated based on a 24-hour day (1440 minutes).
                </div>
            @endif
        </div>
    </div>
</div>
@endsection