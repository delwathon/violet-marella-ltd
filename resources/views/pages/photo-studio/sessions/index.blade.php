@extends('layouts.app')
@section('title', 'Session History')
@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area" style="background-color: #f9fafb;">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title mb-1" style="color: #1f2937;">Session History</h1>
                <p class="text-muted mb-0">View and manage all studio sessions</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-secondary" onclick="window.location.href='{{ route('photo-studio.sessions.export', request()->all()) }}'">
                    <i class="fas fa-download me-2"></i>Export CSV
                </button>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4" style="background: white;">
        <div class="card-body" style="background: white;">
            <form method="GET" action="{{ route('photo-studio.sessions.history') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">All Statuses</option>
                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Studio</label>
                        <select class="form-select" name="studio_id">
                            <option value="">All Studios</option>
                            @foreach($studios as $studio)
                                <option value="{{ $studio->id }}" {{ $studioId == $studio->id ? 'selected' : '' }}>
                                    {{ $studio->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">From Date</label>
                        <input type="date" class="form-control" name="date_from" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To Date</label>
                        <input type="date" class="form-control" name="date_to" value="{{ $dateTo }}">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Apply Filters
                        </button>
                        <a href="{{ route('photo-studio.sessions.history') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sessions Table -->
    <div class="card" style="background: white;">
        <div class="card-header" style="background: white; border-bottom: 1px solid #e5e7eb;">
            <h5 class="mb-0" style="color: #1f2937;">All Sessions ({{ $sessions->total() }})</h5>
        </div>
        <div class="card-body" style="background: white;">
            @if($sessions->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-history fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No Sessions Found</h5>
                    <p class="text-muted">Try adjusting your filters</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover" style="background: white;">
                        <thead style="background-color: #f9fafb;">
                            <tr>
                                <th style="color: #1f2937;">Session Code</th>
                                <th style="color: #1f2937;">Customer</th>
                                <th style="color: #1f2937;">Studio</th>
                                <th style="color: #1f2937;">Check-in</th>
                                <th style="color: #1f2937;">Duration</th>
                                <th style="color: #1f2937;">Amount</th>
                                <th style="color: #1f2937;">Payment</th>
                                <th style="color: #1f2937;">Status</th>
                                <th style="color: #1f2937;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $session)
                            <tr style="background: white;">
                                <td style="color: #1f2937;">
                                    <strong>{{ $session->session_code }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="customer-avatar-sm me-2">{{ $session->customer->initials }}</div>
                                        <div>
                                            <div class="fw-semibold" style="color: #1f2937;">{{ $session->customer->name }}</div>
                                            <small class="text-muted">{{ $session->customer->phone }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td style="color: #1f2937;">{{ $session->studio->name }}</td>
                                <td style="color: #1f2937;">
                                    {{ $session->check_in_time->format('M d, Y') }}<br>
                                    <small class="text-muted">{{ $session->check_in_time->format('g:i A') }}</small>
                                </td>
                                <td style="color: #1f2937;">
                                    @if($session->actual_duration)
                                        {{ $session->actual_duration }} min
                                    @else
                                        <span class="text-muted">In progress</span>
                                    @endif
                                </td>
                                <td style="color: #1f2937;">
                                    <strong class="text-success">₦{{ number_format($session->total_amount, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $session->payment_status === 'paid' ? 'success' : ($session->payment_status === 'pending' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($session->payment_status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $session->status === 'completed' ? 'primary' : ($session->status === 'active' ? 'success' : 'secondary') }}">
                                        {{ ucfirst($session->status) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewSessionDetails({{ $session->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $sessions->firstItem() }} to {{ $sessions->lastItem() }} of {{ $sessions->total() }} sessions
                    </div>
                    <div>
                        {{ $sessions->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@include('pages.photo-studio.modals.session-details')

@push('scripts')
<script src="{{ asset('assets/js/photo-studio.js') }}"></script>
@endpush
@endsection