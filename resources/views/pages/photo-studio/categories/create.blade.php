@extends('layouts.app')
@section('title', 'Create Studio Category')
@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <div class="page-header mb-4">
        <h1 class="page-title">Create Studio Category</h1>
        <p class="text-muted">Set up a new room category with pricing and capacity</p>
    </div>

    <div class="card">
        <form action="{{ route('photo-studio.categories.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="e.g., Classic, Deluxe" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Color Code</label>
                        <input type="color" class="form-control form-control-color" name="color" value="{{ old('color', '#6f42c1') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="2">{{ old('description') }}</textarea>
                </div>

                <hr>
                <h6>Pricing Configuration</h6>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Base Time (minutes) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="base_time" value="{{ old('base_time', 30) }}" min="10" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Base Price (₦) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="base_price" value="{{ old('base_price', 30000) }}" min="0" required>
                    </div>
                </div>

                <hr>
                <h6>Capacity Settings</h6>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Max Occupants <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="max_occupants" value="{{ old('max_occupants', 4) }}" min="1" required>
                        <small class="text-muted">Maximum people allowed per session</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Max Concurrent Sessions <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="max_concurrent_sessions" value="{{ old('max_concurrent_sessions', 3) }}" min="1" required>
                        <small class="text-muted">How many sessions can run at once</small>
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active (available for booking)</label>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Create Category
                </button>
                <a href="{{ route('photo-studio.categories.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
