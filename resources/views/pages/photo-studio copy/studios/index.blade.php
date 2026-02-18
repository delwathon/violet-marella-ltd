@extends('layouts.app')
@section('title', 'Studio Management')
@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title mb-1">Studio Management</h1>
                <p class="text-muted mb-0">Manage photo studio rooms and settings</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudioModal">
                    <i class="fas fa-plus me-2"></i>Add Studio
                </button>
            </div>
        </div>
    </div>

    <!-- Studios Grid -->
    <div class="row">
        @foreach($studios as $studio)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card studio-management-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $studio->name }}</h5>
                    <span class="badge bg-{{ $studio->status === 'available' ? 'success' : ($studio->status === 'occupied' ? 'danger' : 'warning') }}">
                        {{ ucfirst($studio->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted small">Code</div>
                        <strong>{{ $studio->code }}</strong>
                    </div>
                    
                    @if($studio->description)
                    <div class="mb-3">
                        <div class="text-muted small">Description</div>
                        <p class="mb-0">{{ $studio->description }}</p>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="text-muted small">Rate Plan</div>
                            @if($studio->rate)
                                <strong>{{ $studio->rate->name }}</strong>
                                <br>
                                <small class="text-success">₦{{ number_format($studio->rate->hourly_rate, 2) }}/hr</small>
                            @else
                                <span class="text-muted">Default Rate</span>
                            @endif
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Capacity</div>
                            <strong>{{ $studio->capacity }} {{ Str::plural('person', $studio->capacity) }}</strong>
                        </div>
                    </div>

                    @if($studio->equipment)
                    <div class="mb-3">
                        <div class="text-muted small mb-2">Equipment</div>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($studio->equipment as $equipment)
                            <span class="badge bg-secondary">{{ $equipment }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <div class="text-muted small">Today's Stats</div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <small class="text-muted">Sessions</small>
                                <div><strong>{{ $studio->todaySessions()->count() }}</strong></div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Revenue</small>
                                <div><strong class="text-success">₦{{ number_format($studio->todayRevenue(), 2) }}</strong></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="btn-group w-100">
                        <button class="btn btn-sm btn-outline-primary" onclick="editStudio({{ $studio->id }})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i> Status
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="updateStudioStatus({{ $studio->id }}, 'available')">
                                    <i class="fas fa-check-circle text-success me-2"></i>Available
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="updateStudioStatus({{ $studio->id }}, 'maintenance')">
                                    <i class="fas fa-tools text-warning me-2"></i>Maintenance
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" onclick="toggleStudioActive({{ $studio->id }}, {{ $studio->is_active ? 'false' : 'true' }})">
                                    <i class="fas fa-{{ $studio->is_active ? 'ban' : 'check' }} me-2"></i>{{ $studio->is_active ? 'Deactivate' : 'Activate' }}
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($studios->isEmpty())
    <div class="text-center py-5">
        <i class="fas fa-door-open fa-4x text-muted mb-3"></i>
        <h5 class="text-muted">No Studios Found</h5>
        <p class="text-muted">Add your first studio to get started</p>
        <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addStudioModal">
            <i class="fas fa-plus me-2"></i>Add First Studio
        </button>
    </div>
    @endif
</div>

<!-- Add Studio Modal -->
<div class="modal fade" id="addStudioModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Add New Studio
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('photo-studio.studios.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Studio Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required placeholder="e.g., Studio A">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Studio Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="code" required placeholder="e.g., STUDIO-A">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Brief description of the studio..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Rate Plan <span class="text-danger">*</span></label>
                                <select class="form-select" name="studio_rate_id" required>
                                    <option value="">Select Rate Plan</option>
                                    @foreach(\App\Models\StudioRate::active()->get() as $rate)
                                        <option value="{{ $rate->id }}" {{ $rate->is_default ? 'selected' : '' }}>
                                            {{ $rate->name }} - ₦{{ number_format($rate->base_amount, 2) }} for {{ $rate->base_time }}min
                                            @if($rate->is_default) (Default) @endif
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Select the pricing plan for this studio</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Capacity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="capacity" required min="1" value="1">
                                <small class="text-muted">Maximum number of people</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Equipment (Optional)</label>
                        <div id="equipmentList">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="equipment[]" placeholder="e.g., DSLR Camera">
                                <button class="btn btn-outline-danger" type="button" onclick="removeEquipment(this)" disabled>
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addEquipmentField()">
                            <i class="fas fa-plus me-1"></i>Add Equipment
                        </button>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" checked>
                        <label class="form-check-label" for="isActive">
                            Studio is active
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Studio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Studio Modal -->
<div class="modal fade" id="editStudioModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit Studio
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editStudioForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" id="editStudioBody">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Studio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('scripts')
<script src="{{ asset('assets/js/photo-studio.js') }}"></script>
@endpush
@endsection