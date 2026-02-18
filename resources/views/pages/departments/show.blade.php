@extends('layouts.app')
@section('title', 'Department Details')

@section('content')
<div class="content-area">
    <div class="mb-4">
        <a href="{{ route('departments.index') }}" class="btn btn-sm btn-outline-secondary mb-2"><i class="fas fa-arrow-left me-1"></i>Back</a>
        <h1 class="h3 mb-1 fw-bold">{{ $department->name }}</h1>
        <p class="text-muted mb-0">Manage department profile and ownership.</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3"><h5 class="mb-0">Department Profile</h5></div>
                <div class="card-body">
                    <form action="{{ route('departments.update', $department->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $department->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $department->description) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Head</label>
                            <select name="head_id" class="form-select">
                                <option value="">No head selected</option>
                                @foreach(\App\Models\User::active()->orderBy('first_name')->get() as $head)
                                    <option value="{{ $head->id }}" {{ (string) old('head_id', $department->head_id) === (string) $head->id ? 'selected' : '' }}>
                                        {{ $head->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label">Icon</label>
                                <input type="text" name="icon" class="form-control" value="{{ old('icon', $department->icon) }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Color</label>
                                <input type="text" name="color" class="form-control" value="{{ old('color', $department->color) }}">
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active department</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3"><h5 class="mb-0">Summary</h5></div>
                <div class="card-body">
                    <div class="mb-3"><strong>Slug:</strong> <code>{{ $department->slug }}</code></div>
                    <div class="mb-3"><strong>Head:</strong> {{ $department->head?->full_name ?? 'Not assigned' }}</div>
                    <div class="mb-3"><strong>Total Members:</strong> {{ $department->users->count() }}</div>
                    <a href="{{ route('departments.members', $department->id) }}" class="btn btn-outline-primary">Manage Members</a>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white py-3"><h5 class="mb-0">Members</h5></div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($department->users as $member)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $member->full_name }}</span>
                                <span class="badge bg-light text-dark">{{ $member->role }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">No members assigned.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
