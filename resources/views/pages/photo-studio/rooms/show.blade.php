@extends('layouts.app')
@section('title', $room->name . ' - Room Details')

@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('photo-studio.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('photo-studio.rooms.index') }}">Rooms</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $room->name }}</li>
        </ol>
    </nav>

    <div class="page-header mb-4">
        <div class="row align-items-center gy-3">
            <div class="col-md">
                <h1 class="page-title mb-1">{{ $room->name }}</h1>
                <p class="text-muted mb-0">
                    <span class="badge bg-secondary">{{ $room->code }}</span>
                    <span class="ms-2">Category: {{ $room->category?->name ?? 'N/A' }}</span>
                </p>
            </div>
            <div class="col-md-auto d-flex gap-2 flex-wrap">
                <a href="{{ route('photo-studio.rooms.edit', $room->id) }}" class="btn btn-outline-primary"><i class="fas fa-edit me-2"></i>Edit</a>
                <a href="{{ route('photo-studio.rooms.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-primary"><i class="fas fa-door-open"></i></div>
                <div>
                    <small class="text-muted">Status</small>
                    <div class="fw-bold">{{ $room->status_label }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-success"><i class="fas fa-ruler-combined"></i></div>
                <div>
                    <small class="text-muted">Size</small>
                    <div class="fw-bold">{{ $room->formatted_size ?: 'N/A' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-warning"><i class="fas fa-tools"></i></div>
                <div>
                    <small class="text-muted">Equipment</small>
                    <div class="fw-bold">{{ count($room->getEquipmentList()) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-info"><i class="fas fa-toggle-on"></i></div>
                <div>
                    <small class="text-muted">Active</small>
                    <div class="fw-bold">{{ $room->is_active ? 'Yes' : 'No' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Room Information</h6></div>
                <div class="card-body">
                    <div class="mb-2"><strong>Category:</strong> {{ $room->category?->name ?? 'N/A' }}</div>
                    <div class="mb-2"><strong>Floor:</strong> {{ $room->floor_display ?: 'N/A' }}</div>
                    <div class="mb-2"><strong>Location:</strong> {{ $room->location ?: 'N/A' }}</div>
                    <div class="mb-2"><strong>Description:</strong> {{ $room->description ?: 'N/A' }}</div>
                    <div class="mb-0"><strong>Maintenance Notes:</strong> {{ $room->maintenance_notes ?: 'N/A' }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Status Controls</h6></div>
                <div class="card-body d-flex flex-wrap gap-2">
                    <button class="btn btn-success" onclick="markAvailable()"><i class="fas fa-check me-2"></i>Mark Available</button>
                    <button class="btn btn-warning" onclick="markMaintenance()"><i class="fas fa-tools me-2"></i>Maintenance</button>
                    <button class="btn btn-danger" onclick="markOutOfService()"><i class="fas fa-ban me-2"></i>Out of Service</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Equipment</h6></div>
                <div class="card-body">
                    @if(empty($room->getEquipmentList()))
                    <p class="text-muted mb-0">No equipment listed.</p>
                    @else
                    <ul class="mb-0">
                        @foreach($room->getEquipmentList() as $item)
                        <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Features</h6></div>
                <div class="card-body">
                    @if(empty($room->getFeaturesList()))
                    <p class="text-muted mb-0">No features listed.</p>
                    @else
                    <ul class="mb-0">
                        @foreach($room->getFeaturesList() as $item)
                        <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const markAvailableUrl = @json(route('photo-studio.rooms.mark-available', ['id' => $room->id]));
const maintenanceUrl = @json(route('photo-studio.rooms.maintenance', ['id' => $room->id]));
const outOfServiceUrl = @json(route('photo-studio.rooms.out-of-service', ['id' => $room->id]));

async function markAvailable() {
    const result = await apiRequest(markAvailableUrl, 'POST');
    if (result?.success) {
        showAppToast(result.message, 'success');
        window.location.reload();
    }
}

async function markMaintenance() {
    const notes = prompt('Maintenance notes:');
    if (!notes) return;

    const result = await apiRequest(maintenanceUrl, 'POST', { notes });
    if (result?.success) {
        showAppToast(result.message, 'success');
        window.location.reload();
    }
}

async function markOutOfService() {
    const reason = prompt('Reason for out-of-service status:');
    if (!reason) return;

    const result = await apiRequest(outOfServiceUrl, 'POST', { reason });
    if (result?.success) {
        showAppToast(result.message, 'success');
        window.location.reload();
    }
}

async function apiRequest(url, method, payload = null) {
    try {
        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: payload ? JSON.stringify(payload) : null
        });

        const result = await response.json();
        if (!response.ok || !result.success) {
            showAppToast(result.message || 'Request failed', 'error');
            return null;
        }

        return result;
    } catch (error) {
        showAppToast('Request failed', 'error');
        return null;
    }
}

function showAppToast(message, type = 'info') {
    if (typeof showToast === 'function') {
        showToast(message, type);
    } else {
        alert(message);
    }
}
</script>
@endpush
