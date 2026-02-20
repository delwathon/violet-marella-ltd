@extends('layouts.app')

@section('title', 'Mark Prop for Maintenance')

@section('content')
<div class="content-area">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Mark Prop for Maintenance</h1>
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
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-tools me-2"></i>Maintenance Confirmation
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        This prop will become unavailable for new rentals until maintenance is completed.
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
                                    <p class="mb-2"><strong>Current Status:</strong> {{ ucfirst($prop->status) }}</p>
                                    <p class="mb-2"><strong>Condition:</strong> {{ ucfirst($prop->condition) }}</p>
                                    <p class="mb-2"><strong>Daily Rate:</strong> {{ $prop->formatted_daily_rate }}</p>
                                    <p class="mb-0">
                                        <strong>Last Maintenance:</strong>
                                        {{ $prop->last_maintenance ? $prop->last_maintenance->format('d M Y') : 'Not recorded' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('prop-rental.props.mark-maintenance', $prop->id) }}" method="POST">
                        @csrf
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-tools me-2"></i>Confirm Maintenance
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
