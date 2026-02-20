@extends('layouts.app')

@section('title', 'Add Prop')

@section('content')
<div class="content-area">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Add New Prop</h1>
                <p class="page-subtitle">Register a prop and make it available for rental</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('prop-rental.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Props
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-guitar me-2"></i>Prop Details
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('prop-rental.props.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category') is-invalid @enderror" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="guitars" {{ old('category') === 'guitars' ? 'selected' : '' }}>Guitars</option>
                                        <option value="keyboards" {{ old('category') === 'keyboards' ? 'selected' : '' }}>Keyboards</option>
                                        <option value="drums" {{ old('category') === 'drums' ? 'selected' : '' }}>Drums</option>
                                        <option value="brass" {{ old('category') === 'brass' ? 'selected' : '' }}>Brass</option>
                                        <option value="strings" {{ old('category') === 'strings' ? 'selected' : '' }}>Strings</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Type <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('type') is-invalid @enderror"
                                           name="type" value="{{ old('type') }}" placeholder="e.g., Acoustic Guitar" required>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Brand <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('brand') is-invalid @enderror"
                                           name="brand" value="{{ old('brand') }}" placeholder="e.g., Yamaha" required>
                                    @error('brand')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Model <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('model') is-invalid @enderror"
                                           name="model" value="{{ old('model') }}" placeholder="e.g., FG830" required>
                                    @error('model')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Serial Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('serial_number') is-invalid @enderror"
                                           name="serial_number" value="{{ old('serial_number') }}" required>
                                    @error('serial_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Purchase Date</label>
                                    <input type="date" class="form-control @error('purchase_date') is-invalid @enderror"
                                           name="purchase_date" value="{{ old('purchase_date') }}">
                                    @error('purchase_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Daily Rate (₦) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('daily_rate') is-invalid @enderror"
                                           name="daily_rate" value="{{ old('daily_rate') }}" required min="0" step="0.01">
                                    @error('daily_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Condition <span class="text-danger">*</span></label>
                                    <select class="form-select @error('condition') is-invalid @enderror" name="condition" required>
                                        <option value="">Select Condition</option>
                                        <option value="excellent" {{ old('condition') === 'excellent' ? 'selected' : '' }}>Excellent</option>
                                        <option value="good" {{ old('condition') === 'good' ? 'selected' : '' }}>Good</option>
                                        <option value="fair" {{ old('condition') === 'fair' ? 'selected' : '' }}>Fair</option>
                                        <option value="poor" {{ old('condition') === 'poor' ? 'selected' : '' }}>Poor</option>
                                    </select>
                                    @error('condition')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            New props are created with <strong>Available</strong> status by default.
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Prop
                            </button>
                            <a href="{{ route('prop-rental.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
