@extends('layouts.app')
@section('title', 'Studio Categories')
@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title mb-1">Studio Categories</h1>
                <p class="text-muted mb-0">Manage your studio room categories and pricing</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('photo-studio.categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Category
                </a>
            </div>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="row">
        @forelse($categories as $category)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card studio-management-card h-100">
                <div class="card-header" style="background: linear-gradient(135deg, {{ $category->color }}15, #ffffff);">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" style="color: {{ $category->color }}">
                            <i class="fas fa-camera me-2"></i>{{ $category->name }}
                        </h5>
                        <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">{{ $category->description }}</p>
                    
                    <!-- Pricing Info -->
                    <div class="mb-3">
                        <div class="row text-center">
                            <div class="col-6">
                                <small class="text-muted d-block">Base Price</small>
                                <strong class="text-success">{{ $category->formatted_base_price }}</strong>
                                <small class="text-muted d-block">for {{ $category->base_time }}min</small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Hourly Rate</small>
                                <strong class="text-primary">{{ $category->formatted_hourly_rate }}</strong>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Capacity Info -->
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Max Occupants</small>
                                <span class="badge bg-info">{{ $category->max_occupants }} people</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Concurrent Sessions</small>
                                <span class="badge bg-warning">{{ $category->max_concurrent_sessions }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Current Status -->
                    <div class="alert alert-light mb-3">
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
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-door-open me-1"></i>{{ $category->rooms_count }} Physical Rooms
                        </small>
                    </div>
                    @endif
                </div>
                <div class="card-footer">
                    <div class="btn-group w-100">
                        <a href="{{ route('photo-studio.categories.show', $category->id) }}" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i>View
                        </a>
                        <button class="btn btn-sm btn-outline-secondary" onclick="editCategory({{ $category->id }})">
                            <i class="fas fa-edit me-1"></i>Edit
                        </button>
                        <button class="btn btn-sm btn-outline-{{ $category->is_active ? 'warning' : 'success' }}" 
                                onclick="toggleActive({{ $category->id }})">
                            <i class="fas fa-{{ $category->is_active ? 'pause' : 'play' }} me-1"></i>
                            {{ $category->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory({{ $category->id }})">
                            <i class="fas fa-trash"></i>
                        </button>
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
                    <p class="text-muted">Create your first studio category to start managing sessions</p>
                    <a href="{{ route('photo-studio.categories.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-2"></i>Create First Category
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
function editCategory(id) {
    window.location = `/app/photo-studio/categories/${id}/edit`;
}

async function toggleActive(id) {
    if (!confirm('Are you sure you want to change the status of this category?')) return;
    
    try {
        const response = await fetch(`/app/photo-studio/categories/${id}/toggle-active`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const result = await response.json();
        if (result.success) {
            location.reload();
        } else {
            alert(result.message);
        }
    } catch (error) {
        alert('An error occurred');
    }
}

async function deleteCategory(id) {
    if (!confirm('Are you sure you want to delete this category? This action cannot be undone.')) return;
    
    try {
        const response = await fetch(`/app/photo-studio/categories/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const result = await response.json();
        if (result.success) {
            location.reload();
        } else {
            alert(result.message);
        }
    } catch (error) {
        alert('An error occurred');
    }
}
</script>
@endpush
@endsection