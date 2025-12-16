@extends('layouts.app')
@section('title', 'Departments Management')

@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h3 mb-1 fw-bold">Departments</h1>
                <p class="text-muted mb-0">Organize users into departments and manage hierarchies</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
                <i class="fas fa-plus me-2"></i>Create Department
            </button>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded p-3 me-3">
                            <i class="fas fa-sitemap fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total Departments</div>
                            <h3 class="mb-0 fw-bold">{{ $totalDepartments ?? 8 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 text-success rounded p-3 me-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total Members</div>
                            <h3 class="mb-0 fw-bold">{{ $totalMembers ?? 50 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 text-info rounded p-3 me-3">
                            <i class="fas fa-user-tie fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Department Heads</div>
                            <h3 class="mb-0 fw-bold">{{ $departmentHeads ?? 8 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Departments Grid -->
    <div class="row g-4">
        @php
            $departments = $departments ?? [
                ['name' => 'Management', 'icon' => 'user-tie', 'color' => 'primary', 'head' => 'John Doe', 'members' => 3, 'description' => 'Executive leadership and strategic planning'],
                ['name' => 'Sales & Marketing', 'icon' => 'chart-line', 'color' => 'success', 'head' => 'Jane Smith', 'members' => 12, 'description' => 'Sales operations and marketing campaigns'],
                ['name' => 'Operations', 'icon' => 'cogs', 'color' => 'info', 'head' => 'Mike Johnson', 'members' => 15, 'description' => 'Day-to-day business operations'],
                ['name' => 'Finance', 'icon' => 'dollar-sign', 'color' => 'warning', 'head' => 'Sarah Williams', 'members' => 5, 'description' => 'Financial planning and accounting'],
                ['name' => 'Human Resources', 'icon' => 'users', 'color' => 'danger', 'head' => 'Emily Brown', 'members' => 4, 'description' => 'Employee relations and recruitment'],
                ['name' => 'IT & Technology', 'icon' => 'laptop-code', 'color' => 'dark', 'head' => 'David Lee', 'members' => 6, 'description' => 'Technology infrastructure and support'],
                ['name' => 'Customer Service', 'icon' => 'headset', 'color' => 'secondary', 'head' => 'Lisa Garcia', 'members' => 8, 'description' => 'Customer support and relations'],
                ['name' => 'Logistics', 'icon' => 'truck', 'color' => 'primary', 'head' => 'Tom Anderson', 'members' => 7, 'description' => 'Supply chain and delivery management']
            ];
        @endphp
        
        @foreach($departments as $dept)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-{{ $dept['color'] }} bg-opacity-10 text-{{ $dept['color'] }} rounded p-2 me-3">
                                <i class="fas fa-{{ $dept['icon'] }} fa-lg"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">{{ $dept['name'] }}</h5>
                                <p class="text-muted small mb-0">{{ $dept['members'] }} members</p>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>View Details</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>Edit Department</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-users me-2"></i>Manage Members</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i>Delete</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <p class="text-muted small mb-3">{{ $dept['description'] }}</p>
                    
                    <div class="border-top pt-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small">Department Head</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" 
                                 style="width: 32px; height: 32px;">
                                <strong class="small">{{ strtoupper(substr($dept['head'], 0, 1)) }}</strong>
                            </div>
                            <span class="small">{{ $dept['head'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top">
                    <a href="#" class="btn btn-sm btn-outline-primary w-100">
                        <i class="fas fa-users me-2"></i>View Members
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Organization Chart -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Organization Structure</h5>
                    <p class="text-muted small mb-0">Visual representation of department hierarchy</p>
                </div>
                <button class="btn btn-sm btn-outline-primary" onclick="exportOrgChart()">
                    <i class="fas fa-download me-2"></i>Export Chart
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="text-center py-5">
                <i class="fas fa-sitemap fa-3x text-muted mb-3"></i>
                <p class="text-muted">Interactive organization chart would be displayed here</p>
                <small class="text-muted">Using a library like OrgChart.js or D3.js</small>
            </div>
        </div>
    </div>
</div>

<!-- Create Department Modal -->
<div class="modal fade" id="createDepartmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('departments.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="dept_name" class="form-label">Department Name</label>
                        <input type="text" class="form-control" id="dept_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="dept_description" class="form-label">Description</label>
                        <textarea class="form-control" id="dept_description" name="description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="dept_head" class="form-label">Department Head</label>
                        <select class="form-select" id="dept_head" name="head_id">
                            <option value="">Select a user...</option>
                            <option value="1">John Doe</option>
                            <option value="2">Jane Smith</option>
                            <option value="3">Mike Johnson</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="dept_icon" class="form-label">Icon</label>
                        <select class="form-select" id="dept_icon" name="icon">
                            <option value="users">Users</option>
                            <option value="chart-line">Chart Line</option>
                            <option value="cogs">Cogs</option>
                            <option value="dollar-sign">Dollar Sign</option>
                            <option value="laptop-code">Laptop Code</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="dept_color" class="form-label">Color Theme</label>
                        <select class="form-select" id="dept_color" name="color">
                            <option value="primary">Blue</option>
                            <option value="success">Green</option>
                            <option value="info">Cyan</option>
                            <option value="warning">Yellow</option>
                            <option value="danger">Red</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Department
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function exportOrgChart() {
    alert('Exporting organization chart...');
}
</script>
@endpush
@endsection