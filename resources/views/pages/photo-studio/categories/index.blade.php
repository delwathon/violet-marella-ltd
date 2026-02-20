@extends('layouts.app')
@section('title', 'Studio Categories')

@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-area">
    <div class="page-header mb-4">
        <div class="row align-items-center gy-3">
            <div class="col-md">
                <h1 class="page-title mb-1">Studio Categories</h1>
                <p class="text-muted mb-0">Manage category pricing, occupancy, and booking availability.</p>
            </div>
            <div class="col-md-auto d-flex gap-2 flex-wrap">
                <a href="{{ route('photo-studio.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Dashboard
                </a>
                <a href="{{ route('photo-studio.categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Category
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        @forelse($categories as $category)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card studio-management-card h-100">
                <div class="card-header" style="background: linear-gradient(135deg, {{ $category->color }}18, #fff);">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" style="color: {{ $category->color }}"><i class="fas fa-camera me-2"></i>{{ $category->name }}</h5>
                        <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">{{ $category->description ?: 'No description provided.' }}</p>

                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Base Price</small>
                            <strong class="text-success">{{ $category->formatted_base_price }}</strong>
                            <small class="text-muted d-block">for {{ $category->base_time }} min</small>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Hourly Rate</small>
                            <strong class="text-primary">{{ $category->formatted_hourly_rate }}</strong>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Capacity</small>
                            <span class="badge bg-info">{{ $category->max_occupants }} people</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Concurrent</small>
                            <span class="badge bg-warning">{{ $category->max_concurrent_sessions }}</span>
                        </div>
                    </div>

                    <div class="alert alert-light mb-3 py-2">
                        <div class="row text-center">
                            <div class="col-6">
                                <small class="text-muted d-block">Active Sessions</small>
                                <strong class="text-primary">{{ $category->active_sessions_count }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Available Slots</small>
                                <strong class="text-success">{{ $category->availableSlots() }}</strong>
                            </div>
                        </div>
                    </div>

                    @if($category->rooms_count > 0)
                    <small class="text-muted"><i class="fas fa-door-open me-1"></i>{{ $category->rooms_count }} room(s) configured</small>
                    @endif
                </div>
                <div class="card-footer">
                    <div class="btn-group w-100">
                        <a href="{{ route('photo-studio.categories.show', $category->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye me-1"></i>View</a>
                        <a href="{{ route('photo-studio.categories.edit', $category->id) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit me-1"></i>Edit</a>
                        <button class="btn btn-sm btn-outline-{{ $category->is_active ? 'warning' : 'success' }}" onclick="toggleActive({{ $category->id }})">
                            <i class="fas fa-{{ $category->is_active ? 'pause' : 'play' }} me-1"></i>{{ $category->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory({{ $category->id }})"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-camera fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No Categories Yet</h5>
                    <p class="text-muted">Create a category to start accepting sessions.</p>
                    <a href="{{ route('photo-studio.categories.create') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-plus me-2"></i>Create First Category
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
const toggleCategoryTemplate = @json(route('photo-studio.categories.toggle-active', ['id' => '__ID__']));
const deleteCategoryTemplate = @json(route('photo-studio.categories.destroy', ['id' => '__ID__']));

function pathFromTemplate(template, id) {
    return template.replace('__ID__', String(id));
}

async function toggleActive(id) {
    if (!confirm('Change active status for this category?')) {
        return;
    }

    try {
        const response = await fetch(pathFromTemplate(toggleCategoryTemplate, id), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const result = await response.json();
        if (!response.ok || !result.success) {
            showAppToast(result.message || 'Unable to update category', 'error');
            return;
        }

        showAppToast(result.message, 'success');
        window.location.reload();
    } catch (error) {
        showAppToast('An error occurred while updating category', 'error');
    }
}

async function deleteCategory(id) {
    if (!confirm('Delete this category? This cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch(pathFromTemplate(deleteCategoryTemplate, id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const result = await response.json();
        if (!response.ok || !result.success) {
            showAppToast(result.message || 'Unable to delete category', 'error');
            return;
        }

        showAppToast(result.message, 'success');
        window.location.reload();
    } catch (error) {
        showAppToast('An error occurred while deleting category', 'error');
    }
}

function showAppToast(message, type = 'info') {
    if (typeof showToast === 'function') {
        showToast(message, type);
    } else {
        alert(message);
    }
}
</script>
@endpush
