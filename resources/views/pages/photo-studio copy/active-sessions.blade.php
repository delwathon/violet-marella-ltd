@extends('layouts.app')
@section('title', 'Active Sessions')
@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title mb-1">Active Studio Sessions</h1>
                <p class="text-muted mb-0">Monitor and manage ongoing sessions</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkInModal">
                    <i class="fas fa-user-plus me-2"></i>New Check-in
                </button>
                <button class="btn btn-outline-secondary" onclick="refreshActiveSessions()">
                    <i class="fas fa-sync-alt me-2"></i>Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $sessions->count() }}</div>
                    <div class="stat-label">Active Sessions</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-success">
                    <i class="fas fa-door-open"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $studios->where('status', 'available')->count() }}</div>
                    <div class="stat-label">Available Studios</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    @php
                        $overtimeSessions = $sessions->filter(function($session) {
                            return $session->getCurrentDuration() > $session->expected_duration + 30;
                        })->count();
                    @endphp
                    <div class="stat-value">{{ $overtimeSessions }}</div>
                    <div class="stat-label">Overtime Sessions</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-info">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    @php
                        $totalMinutes = $sessions->sum(function($session) {
                            return $session->getCurrentDuration();
                        });
                        $hours = floor($totalMinutes / 60);
                        $mins = $totalMinutes % 60;
                    @endphp
                    <div class="stat-value">{{ $hours }}h {{ $mins }}m</div>
                    <div class="stat-label">Total Duration</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Sessions List -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Active Sessions</h5>
                <div class="text-muted small">
                    <i class="fas fa-circle text-success me-1"></i> Auto-refreshing every 30 seconds
                </div>
            </div>
        </div>
        <div class="card-body" id="activeSessionsContainer">
            @if($sessions->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-camera fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No Active Sessions</h5>
                    <p class="text-muted">Check in customers to see active sessions here</p>
                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#checkInModal">
                        <i class="fas fa-user-plus me-2"></i>Check-in First Customer
                    </button>
                </div>
            @else
                @foreach($sessions as $session)
                    @php
                        $duration = $session->getCurrentDuration();
                        $hours = floor($duration / 60);
                        $minutes = $duration % 60;
                        $durationText = $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
                        $isOvertime = $duration > $session->expected_duration + 30;
                        $isWarning = $duration > $session->expected_duration && !$isOvertime;
                        $durationClass = $isOvertime ? 'overtime' : ($isWarning ? 'warning' : 'active');
                    @endphp
                    <div class="session-item" data-session-id="{{ $session->id }}">
                        <div class="row align-items-center">
                            <div class="col-lg-3 col-md-4 mb-2 mb-md-0">
                                <div class="customer-info">
                                    <div class="customer-avatar">{{ $session->customer->initials }}</div>
                                    <div>
                                        <div class="fw-semibold">{{ $session->customer->name }}</div>
                                        <small class="text-muted">{{ $session->customer->phone }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 mb-2 mb-md-0">
                                <div class="studio-badge">{{ $session->studio->name }}</div>
                            </div>
                            <div class="col-lg-2 col-md-2 mb-2 mb-md-0">
                                <div class="time-info">
                                    <div class="check-in-time">{{ $session->check_in_time->format('g:i A') }}</div>
                                    <small class="text-muted">Check-in</small>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 mb-2 mb-md-0">
                                <div class="duration-badge {{ $durationClass }}">
                                    {{ $durationText }}
                                    @if($isOvertime)
                                        <i class="fas fa-exclamation-triangle ms-1"></i>
                                    @endif
                                </div>
                                <small class="text-muted d-block mt-1">Expected: {{ $session->expected_duration }}min</small>
                            </div>
                            <div class="col-lg-3 col-md-2">
                                <div class="session-actions">
                                    <button class="btn btn-sm btn-outline-info" onclick="showQRCodeModal({{ $session->id }})" title="Show QR Code" data-bs-toggle="tooltip">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" onclick="showExtendModal({{ $session->id }})" title="Extend Session" data-bs-toggle="tooltip">
                                        <i class="fas fa-clock"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="viewSessionDetails({{ $session->id }})" title="View Details" data-bs-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-success" onclick="showCheckoutModal({{ $session->id }})" title="Checkout" data-bs-toggle="tooltip">
                                        <i class="fas fa-sign-out-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<!-- Include Modals -->
@include('pages.photo-studio.modals.check-in')
@include('pages.photo-studio.modals.checkout')
@include('pages.photo-studio.modals.extend-session')
@include('pages.photo-studio.modals.qr-code')
@include('pages.photo-studio.modals.session-details')

@push('scripts')
<script src="{{ asset('assets/js/photo-studio.js') }}"></script>
@endpush
@endsection