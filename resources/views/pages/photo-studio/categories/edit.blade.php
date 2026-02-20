@extends('layouts.app')
@section('title', 'Edit Studio Category')

@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-area">
    <div class="page-header mb-4">
        <h1 class="page-title mb-1">Edit Category</h1>
        <p class="text-muted mb-0">Update pricing and capacity for {{ $category->name }}.</p>
    </div>

    <div class="card">
        <form action="{{ route('photo-studio.categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $category->name) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="slug" value="{{ old('slug', $category->slug) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Color</label>
                        <input type="color" class="form-control form-control-color" name="color" value="{{ old('color', $category->color) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="2">{{ old('description', $category->description) }}</textarea>
                </div>

                <hr>
                <h6>Pricing Configuration</h6>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Base Time (minutes) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="base_time" value="{{ old('base_time', $category->base_time) }}" min="10" max="240" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Base Price (₦) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="base_price" value="{{ old('base_price', $category->base_price) }}" min="0" step="0.01" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0">
                    </div>
                </div>

                <hr>
                <h6>Capacity Settings</h6>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Max Occupants <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="max_occupants" value="{{ old('max_occupants', $category->max_occupants) }}" min="1" max="50" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Max Concurrent Sessions <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="max_concurrent_sessions" value="{{ old('max_concurrent_sessions', $category->max_concurrent_sessions) }}" min="1" max="100" required>
                    </div>
                </div>

                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active (available for booking)</label>
                </div>
            </div>
            <div class="card-footer d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Category
                </button>
                <a href="{{ route('photo-studio.categories.show', $category->id) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
