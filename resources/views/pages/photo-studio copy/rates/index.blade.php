@extends('layouts.app')
@section('title', 'Studio Rates & Pricing')
@push('styles')
<link href="{{ asset('assets/css/photo-studio-light.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area" style="background-color: #f9fafb;">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title mb-1" style="color: #1f2937;">Studio Rates & Pricing</h1>
                <p class="text-muted mb-0">Manage studio session rates and pricing</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRateModal">
                    <i class="fas fa-plus me-2"></i>Add Rate
                </button>
            </div>
        </div>
    </div>

    <!-- Rates Grid -->
    <div class="row">
        @forelse($rates as $rate)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card {{ $rate->is_default ? 'border-primary' : '' }}" style="background: white;">
                @if($rate->is_default)
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-star me-2"></i>{{ $rate->name }}
                        <span class="badge bg-white text-primary float-end">Default</span>
                    </h5>
                </div>
                @else
                <div class="card-header" style="background: white; border-bottom: 1px solid #e5e7eb;">
                    <h5 class="mb-0" style="color: #1f2937;">{{ $rate->name }}</h5>
                </div>
                @endif
                <div class="card-body" style="background: white;">
                    <div class="text-center mb-3">
                        <h2 class="mb-0" style="color: #1f2937;">₦{{ number_format($rate->base_amount, 2) }}</h2>
                        <p class="text-muted">for {{ $rate->base_time }} minutes</p>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Per Minute Rate:</span>
                            <strong style="color: #1f2937;">₦{{ number_format($rate->per_minute_rate, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Hourly Rate:</span>
                            <strong style="color: #1f2937;">₦{{ number_format($rate->hourly_rate, 2) }}</strong>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <strong style="color: #1f2937;">Status:</strong>
                        <span class="badge bg-{{ $rate->is_active ? 'success' : 'secondary' }} float-end">
                            {{ $rate->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="alert alert-light small mb-0">
                        <strong>Example:</strong> A 2-hour session would cost 
                        <strong class="text-success">₦{{ number_format($rate->hourly_rate * 2, 2) }}</strong>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex gap-2">
                        @if(!$rate->is_default)
                        <button class="btn btn-sm btn-outline-primary" onclick="setDefaultRate({{ $rate->id }})">
                            <i class="fas fa-star me-1"></i>Set Default
                        </button>
                        @endif
                        <button class="btn btn-sm btn-outline-secondary" onclick="editRate({{ $rate->id }})">
                            <i class="fas fa-edit me-1"></i>Edit
                        </button>
                        @if(!$rate->is_default)
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteRate({{ $rate->id }})">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-tags fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No Rates Found</h5>
                <p class="text-muted">Add your first rate to get started</p>
                <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addRateModal">
                    <i class="fas fa-plus me-2"></i>Add First Rate
                </button>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Add Rate Modal -->
<div class="modal fade" id="addRateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background: white;">
            <div class="modal-header">
                <h5 class="modal-title" style="color: #1f2937;">
                    <i class="fas fa-plus me-2"></i>Add New Rate
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('photo-studio.rates.store') }}" method="POST">
                @csrf
                <div class="modal-body" style="background: white;">
                    <div class="mb-3">
                        <label class="form-label">Rate Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required placeholder="e.g., Standard Rate">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Base Time (minutes) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="base_time" required min="1" value="30">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Base Amount (₦) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="base_amount" required min="0" step="0.01" value="2000">
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_default" value="1" id="isDefault">
                        <label class="form-check-label" for="isDefault">
                            Set as default rate
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" checked>
                        <label class="form-check-label" for="isActive">
                            Rate is active
                        </label>
                    </div>
                </div>
                <div class="modal-footer" style="background: white;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Rate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Rate Modal -->
<div class="modal fade" id="editRateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background: white;">
            <div class="modal-header">
                <h5 class="modal-title" style="color: #1f2937;">
                    <i class="fas fa-edit me-2"></i>Edit Rate
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editRateForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" id="editRateBody" style="background: white;">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer" style="background: white;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Rate
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