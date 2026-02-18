@extends('layouts.app')
@section('title', 'Departments')

@section('content')
<div class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">Departments</h1>
            <p class="text-muted mb-0">Organize employees into operating units.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
            <i class="fas fa-plus me-2"></i>New Department
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Total Departments</div><h3 class="mb-0 fw-bold">{{ $totalDepartments }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Assigned Members</div><h3 class="mb-0 fw-bold">{{ $totalMembers }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Departments With Heads</div><h3 class="mb-0 fw-bold">{{ $departmentHeads }}</h3></div></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3"><h5 class="mb-0">Department List</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Head</th>
                            <th>Members</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $department)
                            <tr>
                                <td class="fw-semibold">{{ $department->name }}</td>
                                <td>{{ $department->head?->full_name ?? 'Not assigned' }}</td>
                                <td>{{ $department->users_count }}</td>
                                <td>
                                    <span class="badge bg-{{ $department->is_active ? 'success' : 'secondary' }}">
                                        {{ $department->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('departments.show', $department->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    <a href="{{ route('departments.members', $department->id) }}" class="btn btn-sm btn-outline-secondary">Members</a>
                                    <form action="{{ route('departments.destroy', $department->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this department?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No departments found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createDepartmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('departments.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department Head</label>
                        <select name="head_id" class="form-select">
                            <option value="">No head selected</option>
                            @foreach($availableHeads as $head)
                                <option value="{{ $head->id }}">{{ $head->full_name }} ({{ $head->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Icon</label>
                            <input type="text" name="icon" class="form-control" value="users">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Color</label>
                            <input type="text" name="color" class="form-control" value="primary">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
