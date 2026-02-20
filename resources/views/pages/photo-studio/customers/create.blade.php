@extends('layouts.app')
@section('title', 'Add Studio Customer')

@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-area">
    <div class="page-header mb-4">
        <h1 class="page-title mb-1">Add Customer</h1>
        <p class="text-muted mb-0">Create a customer profile for faster check-ins and tracking.</p>
    </div>

    <div class="card">
        <form action="{{ route('photo-studio.customers.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="phone" value="{{ old('phone') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" name="date_of_birth" value="{{ old('date_of_birth') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address" rows="2">{{ old('address') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" name="notes" rows="3">{{ old('notes') }}</textarea>
                </div>

                <h6 class="mb-2">Preferences (Optional)</h6>
                <div class="row">
                    @for($i = 0; $i < 4; $i++)
                    <div class="col-md-6 mb-2">
                        <input type="text" class="form-control" name="preferences[]" value="{{ old('preferences.' . $i) }}" placeholder="Preference {{ $i + 1 }}">
                    </div>
                    @endfor
                </div>
            </div>

            <div class="card-footer d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Create Customer</button>
                <a href="{{ route('photo-studio.customers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
