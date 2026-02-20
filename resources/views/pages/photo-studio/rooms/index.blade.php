@extends('layouts.app')
@section('title', 'Physical Rooms')

@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-area">
    <div class="page-header mb-4">
        <div class="row align-items-center gy-3">
            <div class="col-md">
                <h1 class="page-title mb-1">Physical Rooms</h1>
                <p class="text-muted mb-0">Track room inventory, status, and equipment per category.</p>
            </div>
            <div class="col-md-auto d-flex gap-2 flex-wrap">
                <a href="{{ route('photo-studio.rooms.export') }}" class="btn btn-outline-secondary"><i class="fas fa-file-csv me-2"></i>Export CSV</a>
                <a href="{{ route('photo-studio.rooms.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Room</a>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('photo-studio.rooms.index') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Category</label>
                    <select class="form-select" name="category_id">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ (string)$categoryId === (string)$category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All Statuses</option>
                        <option value="available" {{ $status === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="maintenance" {{ $status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="out_of_service" {{ $status === 'out_of_service' ? 'selected' : '' }}>Out of Service</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-filter me-2"></i>Apply</button>
                    <a href="{{ route('photo-studio.rooms.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($rooms->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-door-open fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No rooms found</h5>
                <p class="text-muted">Add your first room to start tracking studio spaces.</p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Category</th>
                            <th>Location</th>
                            <th>Size</th>
                            <th>Equipment</th>
                            <th>Status</th>
                            <th>Active</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rooms as $room)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $room->name }}</div>
                                <small class="text-muted">{{ $room->code }}</small>
                            </td>
                            <td>{{ $room->category?->name ?? 'N/A' }}</td>
                            <td>
                                <div>{{ $room->floor_display ?: 'N/A' }}</div>
                                <small class="text-muted">{{ $room->location ?: 'No location' }}</small>
                            </td>
                            <td>{{ $room->formatted_size ?: 'N/A' }}</td>
                            <td>{{ count($room->getEquipmentList()) }}</td>
                            <td><span class="badge bg-{{ $room->status === 'available' ? 'success' : ($room->status === 'maintenance' ? 'warning' : 'danger') }}">{{ $room->status_label }}</span></td>
                            <td>{!! $room->is_active ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('photo-studio.rooms.show', $room->id) }}" class="btn btn-outline-primary"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('photo-studio.rooms.edit', $room->id) }}" class="btn btn-outline-secondary"><i class="fas fa-edit"></i></a>
                                    <button class="btn btn-outline-success" onclick="markRoomAvailable({{ $room->id }})" title="Mark Available"><i class="fas fa-check"></i></button>
                                    <button class="btn btn-outline-warning" onclick="markRoomMaintenance({{ $room->id }})" title="Maintenance"><i class="fas fa-tools"></i></button>
                                    <button class="btn btn-outline-danger" onclick="markRoomOutOfService({{ $room->id }})" title="Out of Service"><i class="fas fa-ban"></i></button>
                                    <button class="btn btn-outline-dark" onclick="deleteRoom({{ $room->id }})" title="Delete"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const markAvailableTemplate = @json(route('photo-studio.rooms.mark-available', ['id' => '__ID__']));
const markMaintenanceTemplate = @json(route('photo-studio.rooms.maintenance', ['id' => '__ID__']));
const markOutOfServiceTemplate = @json(route('photo-studio.rooms.out-of-service', ['id' => '__ID__']));
const deleteRoomTemplate = @json(route('photo-studio.rooms.destroy', ['id' => '__ID__']));

function pathFromTemplate(template, id) {
    return template.replace('__ID__', String(id));
}

async function markRoomAvailable(id) {
    const result = await apiRequest(pathFromTemplate(markAvailableTemplate, id), 'POST');
    if (result?.success) {
        showAppToast(result.message, 'success');
        window.location.reload();
    }
}

async function markRoomMaintenance(id) {
    const notes = prompt('Maintenance notes:');
    if (!notes) return;

    const result = await apiRequest(pathFromTemplate(markMaintenanceTemplate, id), 'POST', { notes });
    if (result?.success) {
        showAppToast(result.message, 'success');
        window.location.reload();
    }
}

async function markRoomOutOfService(id) {
    const reason = prompt('Reason for marking this room out of service:');
    if (!reason) return;

    const result = await apiRequest(pathFromTemplate(markOutOfServiceTemplate, id), 'POST', { reason });
    if (result?.success) {
        showAppToast(result.message, 'success');
        window.location.reload();
    }
}

async function deleteRoom(id) {
    if (!confirm('Delete this room?')) return;

    const result = await apiRequest(pathFromTemplate(deleteRoomTemplate, id), 'DELETE');
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
