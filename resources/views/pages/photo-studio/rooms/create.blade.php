@extends('layouts.app')
@section('title', 'Add Studio Room')

@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-area">
    <div class="page-header mb-4">
        <h1 class="page-title mb-1">Add Physical Room</h1>
        <p class="text-muted mb-0">Create a room record and assign it to a category.</p>
    </div>

    <div class="card">
        <form action="{{ route('photo-studio.rooms.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Room Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Room Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="code" value="{{ old('code') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="2">{{ old('description') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Floor</label>
                        <input type="number" class="form-control" name="floor" value="{{ old('floor') }}" min="0" max="100">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" name="location" value="{{ old('location') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Size (sqm)</label>
                        <input type="number" class="form-control" name="size_sqm" value="{{ old('size_sqm') }}" min="1">
                    </div>
                </div>

                <h6 class="mb-2">Equipment</h6>
                <div class="row">
                    @for($i = 0; $i < 6; $i++)
                    <div class="col-md-6 mb-2">
                        <input type="text" class="form-control" name="equipment[]" value="{{ old('equipment.' . $i) }}" placeholder="Equipment {{ $i + 1 }}">
                    </div>
                    @endfor
                </div>

                <h6 class="mb-2 mt-3">Features</h6>
                <div class="row">
                    @for($i = 0; $i < 6; $i++)
                    <div class="col-md-6 mb-2">
                        <input type="text" class="form-control" name="features[]" value="{{ old('features.' . $i) }}" placeholder="Feature {{ $i + 1 }}">
                    </div>
                    @endfor
                </div>

                <div class="row mt-2">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="available" {{ old('status', 'available') === 'available' ? 'selected' : '' }}>Available</option>
                            <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="out_of_service" {{ old('status') === 'out_of_service' ? 'selected' : '' }}>Out of Service</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Room is active</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Create Room</button>
                <a href="{{ route('photo-studio.rooms.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
