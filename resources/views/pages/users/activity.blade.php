@extends('layouts.app')
@section('title', 'User Activity Log')

@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h3 mb-1 fw-bold">User Activity Log</h1>
                <p class="text-muted mb-0">Track and audit all user actions across the system</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary" onclick="clearFilters()">
                    <i class="fas fa-eraser me-2"></i>Clear Filters
                </button>
                <button class="btn btn-outline-primary" onclick="exportLogs()">
                    <i class="fas fa-file-export me-2"></i>Export Logs
                </button>
            </div>
        </div>
    </div>

    <!-- Activity Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded p-3 me-3">
                            <i class="fas fa-history fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total Activities</div>
                            <h3 class="mb-0 fw-bold">1,234</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 text-success rounded p-3 me-3">
                            <i class="fas fa-calendar-day fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Today's Activities</div>
                            <h3 class="mb-0 fw-bold">87</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 text-warning rounded p-3 me-3">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Security Alerts</div>
                            <h3 class="mb-0 fw-bold">3</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 text-info rounded p-3 me-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Active Users Today</div>
                            <h3 class="mb-0 fw-bold">42</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('users.activity') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="user_filter" class="form-label small mb-1">User</label>
                        <select class="form-select" id="user_filter" name="user">
                            <option value="">All Users</option>
                            <option value="1">John Doe</option>
                            <option value="2">Jane Smith</option>
                            <option value="3">Mike Johnson</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="action_filter" class="form-label small mb-1">Action Type</label>
                        <select class="form-select" id="action_filter" name="action">
                            <option value="">All Actions</option>
                            <option value="login">Login</option>
                            <option value="logout">Logout</option>
                            <option value="create">Create</option>
                            <option value="update">Update</option>
                            <option value="delete">Delete</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="module_filter" class="form-label small mb-1">Module</label>
                        <select class="form-select" id="module_filter" name="module">
                            <option value="">All Modules</option>
                            <option value="users">Users</option>
                            <option value="products">Products</option>
                            <option value="sales">Sales</option>
                            <option value="settings">Settings</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label small mb-1">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="from">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label small mb-1">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="to">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Timeline -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Activity Timeline</h5>
        </div>
        <div class="card-body">
            <div class="timeline">
                @php
                    $activities = [
                        ['user' => 'John Doe', 'action' => 'Updated user profile', 'module' => 'Users', 'type' => 'update', 'time' => '2 minutes ago', 'color' => 'info'],
                        ['user' => 'Jane Smith', 'action' => 'Created new product "Valentine Gift Box"', 'module' => 'Products', 'type' => 'create', 'time' => '15 minutes ago', 'color' => 'success'],
                        ['user' => 'Mike Johnson', 'action' => 'Deleted inventory item #1234', 'module' => 'Inventory', 'type' => 'delete', 'time' => '32 minutes ago', 'color' => 'danger'],
                        ['user' => 'Sarah Williams', 'action' => 'Logged in to system', 'module' => 'Authentication', 'type' => 'login', 'time' => '1 hour ago', 'color' => 'primary'],
                        ['user' => 'Emily Brown', 'action' => 'Exported sales report', 'module' => 'Reports', 'type' => 'export', 'time' => '2 hours ago', 'color' => 'warning'],
                        ['user' => 'David Lee', 'action' => 'Modified system settings', 'module' => 'Settings', 'type' => 'update', 'time' => '3 hours ago', 'color' => 'info'],
                        ['user' => 'Lisa Garcia', 'action' => 'Processed sale #5678', 'module' => 'Sales', 'type' => 'create', 'time' => '4 hours ago', 'color' => 'success'],
                        ['user' => 'Tom Anderson', 'action' => 'Updated customer details', 'module' => 'Customers', 'type' => 'update', 'time' => '5 hours ago', 'color' => 'info'],
                    ];
                @endphp
                
                @foreach($activities as $activity)
                <div class="timeline-item">
                    <div class="timeline-marker bg-{{ $activity['color'] }}">
                        @if($activity['type'] == 'create')
                            <i class="fas fa-plus text-white"></i>
                        @elseif($activity['type'] == 'update')
                            <i class="fas fa-edit text-white"></i>
                        @elseif($activity['type'] == 'delete')
                            <i class="fas fa-trash text-white"></i>
                        @elseif($activity['type'] == 'login')
                            <i class="fas fa-sign-in-alt text-white"></i>
                        @else
                            <i class="fas fa-circle text-white"></i>
                        @endif
                    </div>
                    <div class="timeline-content">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="d-flex align-items-center mb-1">
                                    <strong class="me-2">{{ $activity['user'] }}</strong>
                                    <span class="badge bg-{{ $activity['color'] }} bg-opacity-10 text-{{ $activity['color'] }}">
                                        {{ $activity['module'] }}
                                    </span>
                                </div>
                                <p class="mb-0">{{ $activity['action'] }}</p>
                            </div>
                            <span class="text-muted small">{{ $activity['time'] }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="card-footer bg-white text-center">
            <button class="btn btn-link text-decoration-none" onclick="loadMoreActivities()">
                <i class="fas fa-chevron-down me-2"></i>Load More Activities
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding: 0;
    list-style: none;
}

.timeline-item {
    position: relative;
    padding-left: 50px;
    padding-bottom: 30px;
}

.timeline-item:not(:last-child):before {
    content: '';
    position: absolute;
    left: 16px;
    top: 40px;
    bottom: -10px;
    width: 2px;
    background: #e9ecef;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 0;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}
</style>
@endpush

@push('scripts')
<script>
function clearFilters() {
    window.location.href = '{{ route("users.activity") }}';
}

function exportLogs() {
    alert('Exporting activity logs...');
}

function loadMoreActivities() {
    alert('Loading more activities...');
}
</script>
@endpush
@endsection