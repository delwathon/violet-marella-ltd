@extends('layouts.app')
@section('title', 'User Activity Log')

@section('content')
<div class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">User Activity Log</h1>
            <p class="text-muted mb-0">Track and audit user actions across the system.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('users.activity') }}" class="btn btn-outline-secondary">
                <i class="fas fa-eraser me-2"></i>Clear Filters
            </a>
            <a href="{{ route('users.activity.export', request()->query()) }}" class="btn btn-outline-primary">
                <i class="fas fa-file-export me-2"></i>Export CSV
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Total Activities</div>
                    <h3 class="mb-0 fw-bold">{{ number_format($totalActivities) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Today's Activities</div>
                    <h3 class="mb-0 fw-bold">{{ number_format($todayActivities) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Security Alerts</div>
                    <h3 class="mb-0 fw-bold">{{ number_format($securityAlerts) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Active Users Today</div>
                    <h3 class="mb-0 fw-bold">{{ number_format($activeUsersToday) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('users.activity') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">User</label>
                    <select class="form-select" name="user">
                        <option value="">All Users</option>
                        @foreach($usersForFilter as $filterUser)
                            <option value="{{ $filterUser->id }}" {{ (string) request('user') === (string) $filterUser->id ? 'selected' : '' }}>
                                {{ $filterUser->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Action</label>
                    <select class="form-select" name="action">
                        <option value="">All Actions</option>
                        @foreach(['login', 'logout', 'create', 'update', 'delete'] as $action)
                            <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Module</label>
                    <select class="form-select" name="module">
                        <option value="">All Modules</option>
                        @foreach($modules as $module)
                            <option value="{{ $module }}" {{ request('module') === $module ? 'selected' : '' }}>{{ ucfirst($module) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">From</label>
                    <input type="date" class="form-control" name="from" value="{{ request('from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">To</label>
                    <input type="date" class="form-control" name="to" value="{{ request('to') }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Recent Activity</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Timestamp</th>
                            <th>User</th>
                            <th>Module</th>
                            <th>Action</th>
                            <th>Request</th>
                            <th>URL</th>
                            <th>IP</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                            @php
                                $statusCode = (int) ($activity->status_code ?? 0);
                                $statusClass = $statusCode >= 500 ? 'danger' : ($statusCode >= 400 ? 'warning' : 'success');
                                $actionClass = match ($activity->action) {
                                    'create' => 'success',
                                    'update' => 'info',
                                    'delete' => 'danger',
                                    'login' => 'primary',
                                    'logout' => 'secondary',
                                    default => 'dark',
                                };
                            @endphp
                            <tr>
                                <td>{{ $activity->created_at?->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $activity->user?->full_name ?? 'System' }}</td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $activity->module ?: 'system' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $actionClass }}">{{ ucfirst($activity->action) }}</span>
                                </td>
                                <td><code>{{ $activity->method }}</code></td>
                                <td>
                                    <span title="{{ $activity->url }}">{{ \Illuminate\Support\Str::limit($activity->url, 45) }}</span>
                                </td>
                                <td>{{ $activity->ip_address ?: '-' }}</td>
                                <td>
                                    @if($statusCode > 0)
                                        <span class="badge bg-{{ $statusClass }}">{{ $statusCode }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No activity records found for the selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($activities->hasPages())
            <div class="card-footer bg-white">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
