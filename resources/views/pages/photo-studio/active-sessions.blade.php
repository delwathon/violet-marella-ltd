@extends('layouts.app')
@section('title', 'Active Sessions')

@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-area">
    <div class="page-header mb-4">
        <div class="row align-items-center gy-3">
            <div class="col-md">
                <h1 class="page-title mb-1">Active Sessions</h1>
                <p class="text-muted mb-0">
                    <span class="badge bg-primary">{{ $sessions->count() }} Active</span>
                    <span class="ms-2">Real-time monitoring and checkout control</span>
                </p>
            </div>
            <div class="col-md-auto d-flex gap-2 flex-wrap">
                <button class="btn btn-primary" onclick="refreshSessions()">
                    <i class="fas fa-sync-alt me-2"></i>Refresh
                </button>
                <a href="{{ route('photo-studio.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Dashboard
                </a>
            </div>
        </div>
    </div>

    <div id="activeSessionsContainer">
        @forelse($sessions as $session)
        <div class="session-item mb-3" id="session-{{ $session->id }}" data-booked-duration="{{ $session->booked_duration }}">
            <div class="row align-items-center gy-3">
                <div class="col-md-3">
                    <div class="customer-info">
                        <div class="customer-avatar">{{ $session->customer->initials }}</div>
                        <div>
                            <div class="fw-semibold">{{ $session->customer->name }}</div>
                            <small class="text-muted">{{ $session->customer->phone }}</small>
                            <div class="mt-1"><span class="badge bg-secondary">{{ $session->session_code }}</span></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="studio-badge"><i class="fas fa-camera me-1"></i>{{ $session->category->name }}</div>
                    <small class="text-muted d-block mt-2"><i class="fas fa-users me-1"></i>{{ $session->number_of_people }} {{ $session->number_of_people === 1 ? 'person' : 'people' }}</small>
                </div>

                <div class="col-md-3 text-md-center">
                    @if($session->status === 'pending')
                        <div class="alert alert-warning mb-1 py-2">
                            <small class="d-block mb-1"><strong>Preparation Window</strong></small>
                            <div>Timer starts at <strong>{{ $session->scheduled_start_time->format('h:i A') }}</strong></div>
                        </div>
                        <small class="text-muted">Checked in: {{ $session->check_in_time->format('h:i A') }}</small>
                    @else
                        <div class="check-in-time">{{ $session->check_in_time->format('h:i A') }}</div>
                        <small class="text-muted d-block">Booked: {{ $session->booked_duration }} min</small>
                        @if($session->isOvertime())
                            <span class="badge bg-danger mt-1" id="remaining-{{ $session->id }}">OVERTIME: {{ $session->getOvertimeMinutes() }} min</span>
                        @else
                            <span class="badge bg-success mt-1" id="remaining-{{ $session->id }}">{{ $session->getTimeRemaining() }} min left</span>
                        @endif
                    @endif
                </div>

                <div class="col-md-2 text-md-center">
                    <div class="duration-badge {{ $session->isOvertime() ? 'overtime' : ($session->getTimeRemaining() < 10 ? 'warning' : 'active') }}">
                        <i class="fas fa-clock me-1"></i><span id="duration-{{ $session->id }}">{{ $session->formatted_duration }}</span>
                    </div>
                    <small class="text-muted d-block mt-2">Status: <strong id="status-{{ $session->id }}">{{ $session->status_label }}</strong></small>
                </div>

                <div class="col-md-2">
                    <div class="session-actions">
                        <button class="btn btn-sm btn-primary" onclick="openCheckoutModal({{ $session->id }})"><i class="fas fa-sign-out-alt me-1"></i>Checkout</button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="extendSessionPrompt({{ $session->id }})"><i class="fas fa-plus me-1"></i>Extend</button>
                        <button class="btn btn-sm btn-outline-danger" onclick="cancelSessionPrompt({{ $session->id }})"><i class="fas fa-times"></i></button>
                        <a href="{{ route('photo-studio.sessions.show', $session->id) }}" class="btn btn-sm btn-outline-dark"><i class="fas fa-eye"></i></a>
                    </div>
                </div>
            </div>

            @if($session->status !== 'pending')
            @php
                $progress = min(100, ($session->getCurrentDuration() / max(1, $session->booked_duration)) * 100);
            @endphp
            <div class="mt-3">
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar {{ $session->isOvertime() ? 'bg-danger' : 'bg-success' }}" id="progress-{{ $session->id }}" style="width: {{ $progress }}%"></div>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <small class="text-muted">0 min</small>
                    <small class="text-muted">{{ $session->booked_duration }} min</small>
                </div>
            </div>
            @endif
        </div>
        @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-clock fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No Active Sessions</h5>
                <p class="text-muted">Check in a customer from the dashboard to begin.</p>
                <a href="{{ route('photo-studio.index') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-user-check me-2"></i>Go To Dashboard
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>

<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-sign-out-alt me-2"></i>Checkout Session</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="checkoutForm">
                @csrf
                <input type="hidden" id="checkoutSessionId" name="session_id">
                <div class="modal-body" id="checkoutBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="checkoutSubmitBtn">
                        <i class="fas fa-check me-2"></i>Complete Checkout
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const activeSessionsRoute = @json(route('photo-studio.get-active-sessions'));
const sessionDetailTemplate = @json(route('photo-studio.session', ['id' => '__ID__']));
const checkoutTemplate = @json(route('photo-studio.checkout', ['id' => '__ID__']));
const extendTemplate = @json(route('photo-studio.extend', ['id' => '__ID__']));
const cancelTemplate = @json(route('photo-studio.cancel', ['id' => '__ID__']));

const checkoutModalEl = document.getElementById('checkoutModal');
const checkoutModal = checkoutModalEl ? new bootstrap.Modal(checkoutModalEl) : null;

function pathFromTemplate(template, id) {
    return template.replace('__ID__', String(id));
}

setInterval(refreshSessions, 30000);
setInterval(refreshDurationEstimates, 1000);

async function refreshSessions() {
    try {
        const response = await fetch(activeSessionsRoute, { headers: { 'Accept': 'application/json' } });
        const result = await response.json();

        if (!response.ok || !result.success) {
            return;
        }

        result.sessions.forEach(updateSessionDisplay);
    } catch (error) {
        console.error('Failed to refresh sessions', error);
    }
}

function updateSessionDisplay(session) {
    const durationEl = document.getElementById(`duration-${session.id}`);
    if (durationEl) {
        durationEl.textContent = session.formatted_duration;
    }

    const statusEl = document.getElementById(`status-${session.id}`);
    if (statusEl) {
        statusEl.textContent = session.status_label;
    }

    const remainingEl = document.getElementById(`remaining-${session.id}`);
    if (remainingEl) {
        if (session.is_overtime) {
            remainingEl.className = 'badge bg-danger mt-1';
            remainingEl.textContent = `OVERTIME: ${Math.max(0, session.current_duration - session.booked_duration)} min`;
        } else {
            remainingEl.className = 'badge bg-success mt-1';
            remainingEl.textContent = `${session.time_remaining} min left`;
        }
    }

    const progressBar = document.getElementById(`progress-${session.id}`);
    if (progressBar && session.has_timer_started) {
        const booked = Math.max(1, parseInt(session.booked_duration || 0, 10));
        const progress = Math.min(100, (session.current_duration / booked) * 100);
        progressBar.style.width = `${progress}%`;
        progressBar.classList.toggle('bg-danger', session.is_overtime);
        progressBar.classList.toggle('bg-success', !session.is_overtime);
    }
}

function refreshDurationEstimates() {
    // Keep simple and rely on server refresh for authoritative timing.
}

async function openCheckoutModal(sessionId) {
    try {
        const response = await fetch(pathFromTemplate(sessionDetailTemplate, sessionId), {
            headers: { 'Accept': 'application/json' }
        });
        const result = await response.json();

        if (!response.ok || !result.success) {
            showAppToast(result.message || 'Failed to load session details', 'error');
            return;
        }

        const session = result.session;
        const overtimeMinutes = Math.max(0, result.current_duration - (session.booked_duration || 0));

        document.getElementById('checkoutBody').innerHTML = `
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Customer:</strong> ${session.customer.name}<br>
                    <strong>Category:</strong> ${session.category.name}<br>
                    <strong>Duration:</strong> ${result.current_duration} minutes
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <div class="alert alert-info mb-0">
                        <strong>Total Amount</strong>
                        <h4 class="mb-0">₦${parseFloat(session.total_amount || 0).toLocaleString(undefined, {maximumFractionDigits: 2})}</h4>
                    </div>
                </div>
            </div>
            ${result.is_overtime ? `<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>This session is <strong>${overtimeMinutes}</strong> minutes overtime.</div>` : ''}
            <div class="mb-3">
                <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                <select class="form-select" name="payment_method" required>
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="transfer">Bank Transfer</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Discount Amount</label>
                <input type="number" class="form-control" name="discount_amount" min="0" step="0.01" value="0">
            </div>
            <div class="mb-3">
                <label class="form-label">Amount Paid</label>
                <input type="number" class="form-control" name="amount_paid" min="0" step="0.01" value="${parseFloat(session.total_amount || 0)}">
            </div>
        `;

        document.getElementById('checkoutSessionId').value = sessionId;
        checkoutModal?.show();
    } catch (error) {
        showAppToast('Failed to load checkout details', 'error');
    }
}

const checkoutForm = document.getElementById('checkoutForm');
if (checkoutForm) {
    checkoutForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const sessionId = document.getElementById('checkoutSessionId').value;
        const payload = Object.fromEntries(new FormData(e.currentTarget).entries());
        const submitBtn = document.getElementById('checkoutSubmitBtn');

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

        try {
            const response = await fetch(pathFromTemplate(checkoutTemplate, sessionId), {
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
                showAppToast(result.message || 'Checkout failed', 'error');
                return;
            }

            showAppToast(result.message || 'Checkout completed', 'success');
            checkoutModal?.hide();
            window.location.reload();
        } catch (error) {
            showAppToast('Checkout failed', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Complete Checkout';
        }
    });
}

async function extendSessionPrompt(sessionId) {
    const minutes = prompt('How many minutes do you want to add?', '15');
    if (!minutes) return;

    const additionalTime = parseInt(minutes, 10);
    if (Number.isNaN(additionalTime) || additionalTime <= 0) {
        showAppToast('Please enter a valid number of minutes', 'warning');
        return;
    }

    try {
        const response = await fetch(pathFromTemplate(extendTemplate, sessionId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ additional_time: additionalTime })
        });

        const result = await response.json();
        if (!response.ok || !result.success) {
            showAppToast(result.message || 'Failed to extend session', 'error');
            return;
        }

        showAppToast(result.message, 'success');
        window.location.reload();
    } catch (error) {
        showAppToast('Failed to extend session', 'error');
    }
}

async function cancelSessionPrompt(sessionId) {
    const reason = prompt('Reason for cancellation:');
    if (!reason) return;

    try {
        const response = await fetch(pathFromTemplate(cancelTemplate, sessionId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ reason })
        });

        const result = await response.json();
        if (!response.ok || !result.success) {
            showAppToast(result.message || 'Failed to cancel session', 'error');
            return;
        }

        showAppToast(result.message, 'success');
        window.location.reload();
    } catch (error) {
        showAppToast('Failed to cancel session', 'error');
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
@endsection
