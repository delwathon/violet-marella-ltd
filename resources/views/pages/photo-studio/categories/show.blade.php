@extends('layouts.app')
@section('title', $category->name . ' - Category Details')

@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('photo-studio.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('photo-studio.categories.index') }}">Categories</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
        </ol>
    </nav>

    <div class="page-header mb-4">
        <div class="row align-items-center gy-3">
            <div class="col-md">
                <h1 class="page-title mb-1" style="color: {{ $category->color }}">
                    <i class="fas fa-camera me-2"></i>{{ $category->name }}
                </h1>
                <p class="text-muted mb-0">{{ $category->description ?: 'No category description provided.' }}</p>
            </div>
            <div class="col-md-auto d-flex gap-2 flex-wrap">
                <a href="{{ route('photo-studio.categories.edit', $category->id) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-2"></i>Edit Category
                </a>
                <a href="{{ route('photo-studio.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-primary"><i class="fas fa-chart-line"></i></div>
                <div>
                    <small class="text-muted">Today's Sessions</small>
                    <div class="fw-bold">{{ $todayStats['total_sessions'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-success"><i class="fas fa-naira-sign"></i></div>
                <div>
                    <small class="text-muted">Today's Revenue</small>
                    <div class="fw-bold text-success">₦{{ number_format($todayStats['revenue'], 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-warning"><i class="fas fa-percentage"></i></div>
                <div>
                    <small class="text-muted">Occupancy Rate</small>
                    <div class="fw-bold">{{ number_format($occupancyRate, 1) }}%</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-info"><i class="fas fa-stopwatch"></i></div>
                <div>
                    <small class="text-muted">Active Now</small>
                    <div class="fw-bold">{{ $category->active_sessions_count }} / {{ $category->max_concurrent_sessions }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Pricing Information</h6></div>
                <div class="card-body table-responsive">
                    <table class="table table-sm mb-0">
                        <tr><td>Base Price</td><td class="text-end fw-bold text-success">{{ $category->formatted_base_price }}</td></tr>
                        <tr><td>Base Time</td><td class="text-end fw-bold">{{ $category->base_time }} min</td></tr>
                        <tr><td>Per Minute</td><td class="text-end fw-bold">₦{{ number_format($category->per_minute_rate, 2) }}</td></tr>
                        <tr><td>Hourly Rate</td><td class="text-end fw-bold text-primary">{{ $category->formatted_hourly_rate }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Capacity & Availability</h6></div>
                <div class="card-body table-responsive">
                    <table class="table table-sm mb-0">
                        <tr><td>Max Occupants</td><td class="text-end"><span class="badge bg-info">{{ $category->max_occupants }} people</span></td></tr>
                        <tr><td>Concurrent Sessions</td><td class="text-end"><span class="badge bg-warning">{{ $category->max_concurrent_sessions }}</span></td></tr>
                        <tr><td>Physical Rooms</td><td class="text-end fw-bold">{{ $category->rooms_count }}</td></tr>
                        <tr>
                            <td>Status</td>
                            <td class="text-end">
                                <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Recent Sessions</h6>
            <a href="{{ route('photo-studio.sessions.index', ['category_id' => $category->id]) }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body">
            @if($recentSessions->isEmpty())
            <p class="text-muted text-center py-4 mb-0">No sessions recorded for this category yet.</p>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Session</th>
                            <th>Customer</th>
                            <th>Check In</th>
                            <th>Duration</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentSessions as $session)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $session->session_code }}</div>
                                <small class="text-muted">{{ $session->number_of_people }} people</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $session->customer->name }}</div>
                                <small class="text-muted">{{ $session->customer->phone }}</small>
                            </td>
                            <td>{{ $session->check_in_time->format('d M Y, h:i A') }}</td>
                            <td>{{ $session->actual_duration ?? $session->booked_duration }} min</td>
                            <td class="fw-semibold text-success">{{ $session->formatted_total_amount }}</td>
                            <td>
                                <span class="badge bg-{{ $session->status === 'completed' ? 'success' : ($session->status === 'cancelled' ? 'secondary' : 'warning') }}">
                                    {{ $session->status_label }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('photo-studio.sessions.show', $session->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
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
