@extends('layouts.app')
@section('title', $session->session_code . ' - Session Details')

@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('photo-studio.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('photo-studio.sessions.index') }}">Sessions</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $session->session_code }}</li>
        </ol>
    </nav>

    <div class="page-header mb-4">
        <div class="row align-items-center gy-3">
            <div class="col-md">
                <h1 class="page-title mb-1">Session {{ $session->session_code }}</h1>
                <p class="text-muted mb-0">
                    <span class="badge bg-{{ $session->status === 'completed' ? 'success' : ($session->status === 'cancelled' ? 'secondary' : 'warning') }}">{{ $session->status_label }}</span>
                    <span class="badge bg-{{ $session->payment_status === 'paid' ? 'success' : ($session->payment_status === 'partial' ? 'warning' : 'secondary') }} ms-1">{{ ucfirst($session->payment_status) }}</span>
                </p>
            </div>
            <div class="col-md-auto d-flex gap-2 flex-wrap">
                @if(in_array($session->status, ['pending', 'active', 'overtime']))
                <a href="{{ route('photo-studio.sessions.active') }}" class="btn btn-outline-primary"><i class="fas fa-clock me-2"></i>Manage Active Session</a>
                @endif
                <a href="{{ route('photo-studio.sessions.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-primary"><i class="fas fa-users"></i></div>
                <div>
                    <small class="text-muted">Party Size</small>
                    <div class="fw-bold">{{ $session->number_of_people }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-info"><i class="fas fa-hourglass-half"></i></div>
                <div>
                    <small class="text-muted">Duration</small>
                    <div class="fw-bold">{{ $session->actual_duration ?? $session->booked_duration }} min</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-success"><i class="fas fa-naira-sign"></i></div>
                <div>
                    <small class="text-muted">Total Amount</small>
                    <div class="fw-bold text-success">{{ $session->formatted_total_amount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple">
                <div class="stat-icon bg-warning"><i class="fas fa-wallet"></i></div>
                <div>
                    <small class="text-muted">Balance</small>
                    <div class="fw-bold">₦{{ number_format($session->balance, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Session Information</h6></div>
                <div class="card-body table-responsive">
                    <table class="table table-sm mb-0">
                        <tr><td>Session Code</td><td class="text-end fw-bold">{{ $session->session_code }}</td></tr>
                        <tr><td>Customer</td><td class="text-end">{{ $session->customer->name }} ({{ $session->customer->phone }})</td></tr>
                        <tr><td>Category</td><td class="text-end">{{ $session->category->name }}</td></tr>
                        <tr><td>Booked Duration</td><td class="text-end">{{ $session->booked_duration }} min</td></tr>
                        <tr><td>Actual Duration</td><td class="text-end">{{ $session->actual_duration ?? 'N/A' }}</td></tr>
                        <tr><td>Overtime</td><td class="text-end">{{ $session->overtime_duration ?? 0 }} min</td></tr>
                        <tr><td>Party Names</td><td class="text-end">{{ is_array($session->party_names) ? implode(', ', $session->party_names) : 'N/A' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Timeline & Billing</h6></div>
                <div class="card-body table-responsive">
                    <table class="table table-sm mb-0">
                        <tr><td>Check In</td><td class="text-end">{{ $session->check_in_time->format('d M Y, h:i A') }}</td></tr>
                        <tr><td>Scheduled Start</td><td class="text-end">{{ $session->scheduled_start_time->format('d M Y, h:i A') }}</td></tr>
                        <tr><td>Actual Start</td><td class="text-end">{{ $session->actual_start_time ? $session->actual_start_time->format('d M Y, h:i A') : 'N/A' }}</td></tr>
                        <tr><td>Check Out</td><td class="text-end">{{ $session->check_out_time ? $session->check_out_time->format('d M Y, h:i A') : 'N/A' }}</td></tr>
                        <tr><td>Base Amount</td><td class="text-end">₦{{ number_format($session->base_amount, 2) }}</td></tr>
                        <tr><td>Overtime Amount</td><td class="text-end">₦{{ number_format($session->overtime_amount, 2) }}</td></tr>
                        <tr><td>Discount</td><td class="text-end">₦{{ number_format($session->discount_amount, 2) }}</td></tr>
                        <tr><td>Total Paid</td><td class="text-end">₦{{ number_format($session->amount_paid, 2) }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-3">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Payment History</h6></div>
                <div class="card-body p-0">
                    @if($session->payments->isEmpty())
                    <div class="text-center py-4 text-muted">No payments recorded yet.</div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Received By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($session->payments as $payment)
                                <tr>
                                    <td>{{ $payment->reference }}</td>
                                    <td class="fw-semibold">₦{{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ ucfirst($payment->payment_method) }}</td>
                                    <td>{{ ucfirst($payment->payment_type) }}</td>
                                    <td><span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'secondary' }}">{{ ucfirst($payment->status) }}</span></td>
                                    <td>{{ $payment->payment_date->format('d M Y, h:i A') }}</td>
                                    <td>{{ $payment->receivedBy?->first_name ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-3">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Session Controls</h6></div>
                <div class="card-body">
                    <div class="mb-3"><strong>Created By:</strong> {{ $session->creator?->first_name ? $session->creator->first_name . ' ' . $session->creator->last_name : 'N/A' }}</div>
                    <div class="mb-3"><strong>Checked Out By:</strong> {{ $session->checkoutStaff?->first_name ? $session->checkoutStaff->first_name . ' ' . $session->checkoutStaff->last_name : 'N/A' }}</div>
                    @if($session->cancellation_reason)
                    <div class="alert alert-warning"><strong>Cancellation Reason:</strong><br>{{ $session->cancellation_reason }}</div>
                    @endif

                    @if($session->status === 'completed' && $session->balance > 0)
                    <hr>
                    <h6>Process Additional Payment</h6>
                    <form id="paymentForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="paymentAmount" min="0" step="0.01" value="{{ (float)$session->balance }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Method <span class="text-danger">*</span></label>
                            <select class="form-select" id="paymentMethod" required>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="transfer">Bank Transfer</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success" id="paymentSubmitBtn">
                            <i class="fas fa-check me-2"></i>Process Payment
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const paymentUrl = @json(route('photo-studio.sessions.payment', ['id' => $session->id]));

const paymentForm = document.getElementById('paymentForm');
if (paymentForm) {
    paymentForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const payload = {
            amount: document.getElementById('paymentAmount').value,
            payment_method: document.getElementById('paymentMethod').value,
        };

        const submitBtn = document.getElementById('paymentSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

        try {
            const response = await fetch(paymentUrl, {
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
                showAppToast(result.message || 'Payment failed', 'error');
                return;
            }

            showAppToast(result.message, 'success');
            window.location.reload();
        } catch (error) {
            showAppToast('Payment request failed', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Process Payment';
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
