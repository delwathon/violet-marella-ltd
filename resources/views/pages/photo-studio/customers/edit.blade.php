@extends('layouts.app')
@section('title', 'Edit Studio Customer')

@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-area">
    <div class="page-header mb-4">
        <h1 class="page-title mb-1">Edit Customer</h1>
        <p class="text-muted mb-0">Update profile details for {{ $customer->name }}.</p>
    </div>

    <div class="card">
        <form action="{{ route('photo-studio.customers.update', $customer->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $customer->name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="phone" value="{{ old('phone', $customer->phone) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email', $customer->email) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" name="date_of_birth" value="{{ old('date_of_birth', optional($customer->date_of_birth)->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address" rows="2">{{ old('address', $customer->address) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" name="notes" rows="3">{{ old('notes', $customer->notes) }}</textarea>
                </div>

                <h6 class="mb-2">Preferences (Optional)</h6>
                @php
                    $prefs = old('preferences', $customer->preferences ?? []);
                    $prefs = is_array($prefs) ? array_values($prefs) : [];
                @endphp
                <div class="row">
                    @for($i = 0; $i < 4; $i++)
                    <div class="col-md-6 mb-2">
                        <input type="text" class="form-control" name="preferences[]" value="{{ $prefs[$i] ?? '' }}" placeholder="Preference {{ $i + 1 }}">
                    </div>
                    @endfor
                </div>

                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $customer->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Customer is active</label>
                </div>
            </div>

            <div class="card-footer d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Customer</button>
                <a href="{{ route('photo-studio.customers.show', $customer->id) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
