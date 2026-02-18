@extends('layouts.app')
@section('title', 'Department Members')

@section('content')
<div class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('departments.show', $department->id) }}" class="btn btn-sm btn-outline-secondary mb-2"><i class="fas fa-arrow-left me-1"></i>Back to Department</a>
            <h1 class="h3 mb-1 fw-bold">{{ $department->name }} Members</h1>
            <p class="text-muted mb-0">Assign or remove team members.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3"><h5 class="mb-0">Add Member</h5></div>
                <div class="card-body">
                    <form action="{{ route('departments.add-member', $department->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">User</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">Select user...</option>
                                @foreach($availableUsers as $availableUser)
                                    <option value="{{ $availableUser->id }}">{{ $availableUser->full_name }} ({{ $availableUser->role }})</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-primary" type="submit">Add Member</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3"><h5 class="mb-0">Current Members</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($members as $member)
                                    <tr>
                                        <td>{{ $member->full_name }}</td>
                                        <td>{{ $member->email }}</td>
                                        <td><span class="badge bg-light text-dark">{{ $member->role }}</span></td>
                                        <td>
                                            <span class="badge bg-{{ $member->is_active ? 'success' : 'secondary' }}">{{ $member->is_active ? 'Active' : 'Inactive' }}</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <form action="{{ route('departments.remove-member', [$department->id, $member->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this member from department?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">No members assigned yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
