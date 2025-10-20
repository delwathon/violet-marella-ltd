{{-- resources/views/pages/prop-rental/modals/new-prop.blade.php --}}
<div class="modal fade" id="newPropModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('prop-rental.props.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Prop</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
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
                                    <option value="guitars" {{ old('category') == 'guitars' ? 'selected' : '' }}>Guitars</option>
                                    <option value="keyboards" {{ old('category') == 'keyboards' ? 'selected' : '' }}>Keyboards</option>
                                    <option value="drums" {{ old('category') == 'drums' ? 'selected' : '' }}>Drums</option>
                                    <option value="brass" {{ old('category') == 'brass' ? 'selected' : '' }}>Brass</option>
                                    <option value="strings" {{ old('category') == 'strings' ? 'selected' : '' }}>Strings</option>
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
                                <input type="text" class="form-control @error('type') is-invalid @enderror" name="type" value="{{ old('type') }}" placeholder="e.g., Acoustic Guitar" required>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Brand <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('brand') is-invalid @enderror" name="brand" value="{{ old('brand') }}" placeholder="e.g., Yamaha" required>
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Model <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('model') is-invalid @enderror" name="model" value="{{ old('model') }}" placeholder="e.g., FG830" required>
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Daily Rate (₦) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('daily_rate') is-invalid @enderror" name="daily_rate" value="{{ old('daily_rate') }}" required min="0" step="0.01">
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
                                    <option value="excellent" {{ old('condition') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                                    <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>Good</option>
                                    <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                                    <option value="poor" {{ old('condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                                </select>
                                @error('condition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Serial Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('serial_number') is-invalid @enderror" name="serial_number" value="{{ old('serial_number') }}" required>
                                @error('serial_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Purchase Date</label>
                                <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" name="purchase_date" value="{{ old('purchase_date') }}">
                                @error('purchase_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-guitar me-2"></i>Add Prop
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>