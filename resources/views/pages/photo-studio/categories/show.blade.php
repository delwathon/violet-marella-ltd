@extends('layouts.app')
@section('title', $category->name . ' - Category Details')
@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('photo-studio.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('photo-studio.categories.index') }}">Categories</a></li>
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title mb-1" style="color: {{ $category->color }}">
                    <i class="fas fa-camera me-2"></i>{{ $category->name }}
                </h1>
                <p class="text-muted mb-0">{{ $category->description }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('photo-studio.categories.edit', $category->id) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-2"></i>Edit Category
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-primary"><i class="fas fa-chart-line"></i></div>
                <div>
                    <small class="text-muted">Today's Sessions</small>
                    <div class="fw-bold">{{ $todayStats['total_sessions'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-success"><i class="fas fa-naira-sign"></i></div>
                <div>
                    <small class="text-muted">Today's Revenue</small>
                    <div class="fw-bold text-success">₦{{ number_format($todayStats['revenue'], 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-warning"><i class="fas fa-percentage"></i></div>
                <div>
                    <small class="text-muted">Occupancy Rate</small>
                    <div class="fw-bold">{{ number_format($occupancyRate, 1) }}%</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-info"><i class="fas fa-door-open"></i></div>
                <div>
                    <small class="text-muted">Active Now</small>
                    <div class="fw-bold">{{ $category->active_sessions_count }} / {{ $category->max_concurrent_sessions }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing & Capacity Info -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">Pricing Information</h6></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td>Base Price:</td>
                            <td class="text-end"><strong class="text-success">{{ $category->formatted_base_price }}</strong></td>
                        </tr>
                        <tr>
                            <td>Base Time:</td>
                            <td class="text-end"><strong>{{ $category->base_time }} minutes</strong></td>
                        </tr>
                        <tr>
                            <td>Per Minute Rate:</td>
                            <td class="text-end"><strong>₦{{ number_format($category->per_minute_rate, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td>Hourly Rate:</td>
                            <td class="text-end"><strong class="text-primary">{{ $category->formatted_hourly_rate }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">Capacity & Availability</h6></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td>Max Occupants:</td>
                            <td class="text-end"><span class="badge bg-info">{{ $category->max_occupants }} people</span></td>
                        </tr>
                        <tr>
                            <td>Concurrent Sessions:</td>
                            <td class="text-end"><span class="badge bg-warning">{{ $category->max_concurrent_sessions }}</span></td>
                        </tr>
                        <tr>
                            <td>Physical Rooms:</td>
                            <td class="text-end"><strong>{{ $category->rooms_count }}</strong></td>
                        </tr>
                        <tr>
                            <td>Status:</td>
                            <td class="text-end">
                                <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Sessions -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Recent Sessions</h6>
        </div>
        <div class="card-body">
            @forelse($recentSessions as $session)
            <div class="session-item">
                <!-- Session display similar to active-sessions view -->
            </div>
            @empty
            <p class="text-muted text-center py-3">No recent sessions</p>
            @endforelse
        </div>
    </div>
</div>
@endsection