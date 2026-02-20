@extends('layouts.app')
@section('title', 'Studio Sessions')

@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-area">
    <div class="page-header mb-4">
        <div class="row align-items-center gy-3">
            <div class="col-md">
                <h1 class="page-title mb-1">Session History</h1>
                <p class="text-muted mb-0">Review all studio sessions, payment status, and operational history.</p>
            </div>
            <div class="col-md-auto d-flex gap-2 flex-wrap">
                <a href="{{ route('photo-studio.sessions.export', request()->query()) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-file-csv me-2"></i>Export CSV
                </a>
                <a href="{{ route('photo-studio.sessions.active') }}" class="btn btn-outline-primary">
                    <i class="fas fa-clock me-2"></i>Active Sessions
                </a>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('photo-studio.sessions.index') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="overtime" {{ $status === 'overtime' ? 'selected' : '' }}>Overtime</option>
                        <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="no_show" {{ $status === 'no_show' ? 'selected' : '' }}>No Show</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Category</label>
                    <select class="form-select" name="category_id">
                        <option value="">All</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ (string)$categoryId === (string)$category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Payment</label>
                    <select class="form-select" name="payment_status">
                        <option value="">All</option>
                        <option value="pending" {{ $paymentStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="partial" {{ $paymentStatus === 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="paid" {{ $paymentStatus === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="refunded" {{ $paymentStatus === 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" class="form-control" name="date_from" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="date" class="form-control" name="date_to" value="{{ $dateTo }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-filter me-2"></i>Apply</button>
                    <a href="{{ route('photo-studio.sessions.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($sessions->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No sessions found</h5>
                <p class="text-muted">Try adjusting your filters.</p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Session</th>
                            <th>Customer</th>
                            <th>Category</th>
                            <th>Check In</th>
                            <th>Duration</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $session)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $session->session_code }}</div>
                                <small class="text-muted">{{ $session->number_of_people }} people</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $session->customer->name }}</div>
                                <small class="text-muted">{{ $session->customer->phone }}</small>
                            </td>
                            <td>{{ $session->category->name }}</td>
                            <td>
                                <div>{{ $session->check_in_time->format('d M Y') }}</div>
                                <small class="text-muted">{{ $session->check_in_time->format('h:i A') }}</small>
                            </td>
                            <td>
                                <div>{{ $session->actual_duration ?? $session->booked_duration }} min</div>
                                <small class="text-muted">Booked: {{ $session->booked_duration }} min</small>
                            </td>
                            <td>
                                <div class="fw-semibold text-success">{{ $session->formatted_total_amount }}</div>
                                @if($session->balance > 0)
                                <small class="text-danger">Balance: ₦{{ number_format($session->balance, 2) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $session->payment_status === 'paid' ? 'success' : ($session->payment_status === 'partial' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($session->payment_status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $session->status === 'completed' ? 'success' : ($session->status === 'cancelled' ? 'secondary' : ($session->status === 'no_show' ? 'dark' : 'warning')) }}">
                                    {{ $session->status_label }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('photo-studio.sessions.show', $session->id) }}" class="btn btn-outline-primary"><i class="fas fa-eye"></i></a>
                                    @if($session->status === 'completed' && $session->balance > 0)
                                    <button class="btn btn-outline-success" onclick="openPaymentModal({{ $session->id }}, {{ (float)$session->balance }})"><i class="fas fa-naira-sign"></i></button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        @if($sessions->hasPages())
        <div class="card-footer">
            {{ $sessions->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="paymentForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-credit-card me-2"></i>Process Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="paymentSessionId">
                    <div class="mb-3">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="paymentAmount" min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select" id="paymentMethod" required>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="transfer">Bank Transfer</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="paymentSubmitBtn"><i class="fas fa-check me-2"></i>Submit Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const paymentTemplate = @json(route('photo-studio.sessions.payment', ['id' => '__ID__']));
const paymentModalEl = document.getElementById('paymentModal');
const paymentModal = paymentModalEl ? new bootstrap.Modal(paymentModalEl) : null;

function pathFromTemplate(template, id) {
    return template.replace('__ID__', String(id));
}

function openPaymentModal(sessionId, suggestedAmount) {
    document.getElementById('paymentSessionId').value = sessionId;
    document.getElementById('paymentAmount').value = suggestedAmount;
    paymentModal?.show();
}

const paymentForm = document.getElementById('paymentForm');
if (paymentForm) {
    paymentForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const sessionId = document.getElementById('paymentSessionId').value;
        const payload = {
            amount: document.getElementById('paymentAmount').value,
            payment_method: document.getElementById('paymentMethod').value,
        };

        const submitBtn = document.getElementById('paymentSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

        try {
            const response = await fetch(pathFromTemplate(paymentTemplate, sessionId), {
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
                showAppToast(result.message || 'Unable to process payment', 'error');
                return;
            }

            showAppToast(result.message, 'success');
            paymentModal?.hide();
            window.location.reload();
        } catch (error) {
            showAppToast('Payment request failed', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Submit Payment';
        }
    });
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
