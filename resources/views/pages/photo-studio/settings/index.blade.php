@extends('layouts.app')
@section('title', 'Photo Studio Settings')

@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush

@section('content')
@php
    $offset = $settings['offset_time']['value'] ?? 10;
    $baseTime = $settings['default_base_time']['value'] ?? 30;
    $basePrice = $settings['default_base_price']['value'] ?? 30000;
    $allowOvertime = $settings['allow_overtime']['value'] ?? true;
    $currency = $settings['currency_symbol']['value'] ?? '₦';
@endphp

<div class="content-area">
    <div class="page-header mb-4">
        <div class="row align-items-center gy-3">
            <div class="col-md">
                <h1 class="page-title mb-1">Photo Studio Settings</h1>
                <p class="text-muted mb-0">Manage global defaults for bookings, timing, billing, and overtime.</p>
            </div>
            <div class="col-md-auto d-flex gap-2 flex-wrap">
                <button class="btn btn-outline-warning" id="resetDefaultsBtn"><i class="fas fa-undo me-2"></i>Reset Defaults</button>
                <button class="btn btn-outline-secondary" id="clearCacheBtn"><i class="fas fa-broom me-2"></i>Clear Cache</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Session Timing</h6></div>
                <div class="card-body">
                    <form class="setting-form" action="{{ route('photo-studio.settings.offset-time') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Offset Time (minutes)</label>
                            <input type="number" class="form-control" name="offset_time" value="{{ $offset }}" min="0" max="60" required>
                            <small class="text-muted">Delay between check-in and timer start.</small>
                        </div>
                        <button class="btn btn-primary" type="submit"><i class="fas fa-save me-2"></i>Save Offset Time</button>
                    </form>

                    <hr>

                    <form class="setting-form" action="{{ route('photo-studio.settings.base-time') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Default Base Time (minutes)</label>
                            <input type="number" class="form-control" name="default_base_time" value="{{ $baseTime }}" min="10" max="240" required>
                            <small class="text-muted">Used as initial value when creating categories.</small>
                        </div>
                        <button class="btn btn-primary" type="submit"><i class="fas fa-save me-2"></i>Save Base Time</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Billing & Overtime</h6></div>
                <div class="card-body">
                    <form class="setting-form" action="{{ route('photo-studio.settings.base-price') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Default Base Price</label>
                            <input type="number" class="form-control" name="default_base_price" value="{{ $basePrice }}" min="0" step="0.01" required>
                            <small class="text-muted">Used as initial value when creating categories.</small>
                        </div>
                        <button class="btn btn-primary" type="submit"><i class="fas fa-save me-2"></i>Save Base Price</button>
                    </form>

                    <hr>

                    <form class="setting-form" action="{{ route('photo-studio.settings.overtime') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Allow Overtime</label>
                            <select class="form-select" name="allow_overtime" required>
                                <option value="1" {{ $allowOvertime ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ !$allowOvertime ? 'selected' : '' }}>No</option>
                            </select>
                            <small class="text-muted">If disabled, teams should check out on or before booked time.</small>
                        </div>
                        <button class="btn btn-primary" type="submit"><i class="fas fa-save me-2"></i>Save Overtime Setting</button>
                    </form>

                    <hr>

                    <form class="setting-form" action="{{ route('photo-studio.settings.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="key" value="currency_symbol">
                        <input type="hidden" name="type" value="string">
                        <div class="mb-3">
                            <label class="form-label">Currency Symbol</label>
                            <input type="text" class="form-control" name="value" value="{{ $currency }}" maxlength="4" required>
                        </div>
                        <button class="btn btn-primary" type="submit"><i class="fas fa-save me-2"></i>Save Currency</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-2">
        <div class="card-header"><h6 class="mb-0">Current Settings Snapshot</h6></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Key</th>
                            <th>Value</th>
                            <th>Type</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($settings as $key => $setting)
                        <tr>
                            <td><code>{{ $key }}</code></td>
                            <td>
                                @if(is_array($setting['value']))
                                    {{ json_encode($setting['value']) }}
                                @else
                                    {{ (string)$setting['value'] }}
                                @endif
                            </td>
                            <td>{{ $setting['type'] }}</td>
                            <td>{{ $setting['description'] ?: 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const resetDefaultsUrl = @json(route('photo-studio.settings.reset'));
const clearCacheUrl = @json(route('photo-studio.settings.clear-cache'));

const forms = document.querySelectorAll('.setting-form');
forms.forEach((form) => {
    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"]');
        const original = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';

        try {
            const payload = Object.fromEntries(new FormData(form).entries());

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();
            if (!response.ok || !result.success) {
                showAppToast(result.message || 'Failed to save setting', 'error');
                return;
            }

            showAppToast(result.message, 'success');
            setTimeout(() => window.location.reload(), 500);
        } catch (error) {
            showAppToast('Unable to save setting', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = original;
        }
    });
});

document.getElementById('resetDefaultsBtn').addEventListener('click', async () => {
    if (!confirm('Reset all photo studio settings to defaults?')) return;
    const result = await fireSimpleAction(resetDefaultsUrl);
    if (result?.success) {
        showAppToast(result.message, 'success');
        setTimeout(() => window.location.reload(), 500);
    }
});

document.getElementById('clearCacheBtn').addEventListener('click', async () => {
    const result = await fireSimpleAction(clearCacheUrl);
    if (result?.success) {
        showAppToast(result.message, 'success');
    }
});

async function fireSimpleAction(url) {
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const result = await response.json();
        if (!response.ok || !result.success) {
            showAppToast(result.message || 'Action failed', 'error');
            return null;
        }

        return result;
    } catch (error) {
        showAppToast('Action failed', 'error');
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
