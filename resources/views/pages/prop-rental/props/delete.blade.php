@extends('layouts.app')

@section('title', 'Delete Prop')

@section('content')
<div class="content-area">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Delete Prop</h1>
                <p class="page-subtitle">{{ $prop->name }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('prop-rental.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Props
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <strong>Warning:</strong> This action will remove the prop from active listings.
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Name:</strong> {{ $prop->name }}</p>
                                    <p class="mb-2"><strong>Category:</strong> {{ ucfirst($prop->category) }}</p>
                                    <p class="mb-2"><strong>Brand/Model:</strong> {{ $prop->brand }} {{ $prop->model }}</p>
                                    <p class="mb-0"><strong>Serial Number:</strong> {{ $prop->serial_number }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Daily Rate:</strong> {{ $prop->formatted_daily_rate }}</p>
                                    <p class="mb-2"><strong>Condition:</strong> {{ ucfirst($prop->condition) }}</p>
                                    <p class="mb-2"><strong>Status:</strong> {{ ucfirst($prop->status) }}</p>
                                    <p class="mb-0"><strong>Total Rentals:</strong> {{ $prop->rentals()->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('prop-rental.props.destroy', $prop->id) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="confirmDelete" required>
                            <label class="form-check-label" for="confirmDelete">
                                I understand this action cannot be undone
                            </label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-2"></i>Delete Prop
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
