@extends('layouts.app')
@section('title', 'Active Sessions')
@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title mb-1">Active Sessions</h1>
                <p class="text-muted mb-0">
                    <span class="badge bg-primary">{{ $sessions->count() }} Active</span>
                    <span class="ms-2">Real-time monitoring of ongoing sessions</span>
                </p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" onclick="refreshSessions()">
                    <i class="fas fa-sync-alt me-2"></i>Refresh
                </button>
                <a href="{{ route('photo-studio.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Active Sessions List -->
    @forelse($sessions as $session)
    <div class="session-item mb-3" id="session-{{ $session->id }}">
        <div class="row align-items-center">
            <!-- Customer Info -->
            <div class="col-md-3">
                <div class="customer-info">
                    <div class="customer-avatar">{{ $session->customer->initials }}</div>
                    <div>
                        <div class="fw-semibold">{{ $session->customer->name }}</div>
                        <small class="text-muted">{{ $session->customer->phone }}</small>
                        <div class="mt-1">
                            <span class="badge bg-secondary">{{ $session->session_code }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category & Party Info -->
            <div class="col-md-2">
                <div class="studio-badge">
                    <i class="fas fa-camera me-1"></i>{{ $session->category->name }}
                </div>
                <small class="text-muted d-block mt-2">
                    <i class="fas fa-users me-1"></i>{{ $session->number_of_people }} 
                    {{ $session->number_of_people == 1 ? 'person' : 'people' }}
                </small>
            </div>

            <!-- Time Information -->
            <div class="col-md-3 text-center">
                @if($session->status === 'pending')
                    <div class="alert alert-warning mb-1 py-2">
                        <small class="d-block mb-1"><strong>Prep Time</strong></small>
                        <div>Timer starts at:</div>
                        <strong>{{ $session->scheduled_start_time->format('h:i A') }}</strong>
                    </div>
                    <small class="text-muted">
                        Checked in: {{ $session->check_in_time->format('h:i A') }}
                    </small>
                @else
                    <div class="check-in-time">{{ $session->check_in_time->format('h:i A') }}</div>
                    <small class="text-muted d-block">Booked: {{ $session->booked_duration }}min</small>
                    @if($session->isOvertime())
                        <span class="badge bg-danger mt-1">
                            OVERTIME: {{ $session->getOvertimeMinutes() }}min
                        </span>
                    @else
                        <span class="badge bg-success mt-1">
                            {{ $session->getTimeRemaining() }}min left
                        </span>
                    @endif
                @endif
            </div>

            <!-- Duration Badge -->
            <div class="col-md-2 text-center">
                <div class="duration-badge 
                    {{ $session->isOvertime() ? 'overtime' : ($session->getTimeRemaining() < 10 ? 'warning' : 'active') }}">
                    <i class="fas fa-clock me-1"></i>
                    <span id="duration-{{ $session->id }}">{{ $session->formatted_duration }}</span>
                </div>
                <small class="text-muted d-block mt-2">
                    Status: <strong>{{ $session->status_label }}</strong>
                </small>
            </div>

            <!-- Actions -->
            <div class="col-md-2">
                <div class="session-actions">
                    @if($session->status === 'pending')
                        <button class="btn btn-sm btn-success" onclick="startTimer({{ $session->id }})">
                            <i class="fas fa-play me-1"></i>Start Now
                        </button>
                    @endif
                    <button class="btn btn-sm btn-primary" onclick="openCheckoutModal({{ $session->id }})">
                        <i class="fas fa-sign-out-alt me-1"></i>Check Out
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="extendSession({{ $session->id }})">
                        <i class="fas fa-clock me-1"></i>Extend
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="cancelSession({{ $session->id }})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Progress Bar (for active sessions) -->
        @if($session->status !== 'pending')
        <div class="mt-3">
            @php
                $progress = min(100, ($session->getCurrentDuration() / $session->booked_duration) * 100);
            @endphp
            <div class="progress" style="height: 6px;">
                <div class="progress-bar {{ $session->isOvertime() ? 'bg-danger' : 'bg-success' }}" 
                     role="progressbar" 
                     style="width: {{ $progress }}%" 
                     id="progress-{{ $session->id }}">
                </div>
            </div>
            <div class="d-flex justify-content-between mt-1">
                <small class="text-muted">0min</small>
                <small class="text-muted">{{ $session->booked_duration }}min</small>
            </div>
        </div>
        @endif
    </div>
    @empty
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-clock fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">No Active Sessions</h5>
            <p class="text-muted">Check in a customer to start a new session</p>
            <a href="{{ route('photo-studio.index') }}" class="btn btn-primary mt-3">
                <i class="fas fa-user-check me-2"></i>Check In Customer
            </a>
        </div>
    </div>
    @endforelse
</div>

<!-- Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-sign-out-alt me-2"></i>Checkout Session
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="checkoutForm">
                @csrf
                <input type="hidden" id="checkoutSessionId" name="session_id">
                <div class="modal-body" id="checkoutBody">
                    <!-- Content loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Complete Checkout
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-refresh every 30 seconds
setInterval(refreshSessions, 30000);

// Update durations every second
setInterval(updateDurations, 1000);

async function refreshSessions() {
    try {
        const response = await fetch('{{ route('photo-studio.get-active-sessions') }}');
        const result = await response.json();
        
        if (result.success) {
            // Update session durations and statuses
            result.sessions.forEach(session => {
                updateSessionDisplay(session);
            });
        }
    } catch (error) {
        console.error('Failed to refresh sessions:', error);
    }
}

function updateSessionDisplay(session) {
    const durationEl = document.getElementById(`duration-${session.id}`);
    if (durationEl) {
        durationEl.textContent = session.formatted_duration;
    }
    
    // Update progress bar
    const progressBar = document.getElementById(`progress-${session.id}`);
    if (progressBar && session.has_timer_started) {
        const progress = Math.min(100, (session.current_duration / session.booked_duration) * 100);
        progressBar.style.width = progress + '%';
        
        if (session.is_overtime) {
            progressBar.classList.remove('bg-success');
            progressBar.classList.add('bg-danger');
        }
    }
}

function updateDurations() {
    // Simple client-side duration update (rough estimate between server updates)
    document.querySelectorAll('[id^="duration-"]').forEach(el => {
        // This would need session start times stored in data attributes for accurate updates
        // For now, we rely on the 30-second refresh
    });
}

async function openCheckoutModal(sessionId) {
    try {
        const response = await fetch(`/app/photo-studio/session/${sessionId}`);
        const result = await response.json();
        
        if (result.success) {
            const session = result.session;
            
            const html = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Customer:</strong> ${session.customer.name}<br>
                        <strong>Category:</strong> ${session.category.name}<br>
                        <strong>Duration:</strong> ${result.current_duration} minutes
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="alert alert-info mb-2">
                            <strong>Total Amount:</strong><br>
                            <h3 class="mb-0">₦${parseFloat(session.total_amount).toLocaleString()}</h3>
                        </div>
                    </div>
                </div>
                
                ${result.is_overtime ? `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Overtime!</strong> This session is ${result.current_duration - session.booked_duration} minutes over booked time.
                    </div>
                ` : ''}
                
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
                    <input type="number" class="form-control" name="discount_amount" min="0" value="0">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Amount Paid</label>
                    <input type="number" class="form-control" name="amount_paid" min="0" value="${session.total_amount}">
                </div>
            `;
            
            document.getElementById('checkoutBody').innerHTML = html;
            document.getElementById('checkoutSessionId').value = sessionId;
            $('#checkoutModal').modal('show');
        }
    } catch (error) {
        alert('Failed to load session details');
    }
}

document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const sessionId = document.getElementById('checkoutSessionId').value;
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch(`/app/photo-studio/checkout/${sessionId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Checkout successful!');
            $('#checkoutModal').modal('hide');
            location.reload();
        } else {
            alert(result.message);
        }
    } catch (error) {
        alert('Checkout failed');
    }
});

async function startTimer(sessionId) {
    if (!confirm('Start the session timer now?')) return;
    
    try {
        const response = await fetch(`/app/photo-studio/start-timer/${sessionId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const result = await response.json();
        if (result.success) {
            location.reload();
        }
    } catch (error) {
        alert('Failed to start timer');
    }
}

async function extendSession(sessionId) {
    const minutes = prompt('How many minutes to extend?', '15');
    if (!minutes) return;
    
    try {
        const response = await fetch(`/app/photo-studio/extend/${sessionId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({additional_time: parseInt(minutes)})
        });
        
        const result = await response.json();
        if (result.success) {
            alert(`Session extended by ${minutes} minutes`);
            location.reload();
        }
    } catch (error) {
        alert('Failed to extend session');
    }
}

async function cancelSession(sessionId) {
    const reason = prompt('Reason for cancellation:');
    if (!reason) return;
    
    try {
        const response = await fetch(`/app/photo-studio/cancel/${sessionId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({reason})
        });
        
        const result = await response.json();
        if (result.success) {
            location.reload();
        }
    } catch (error) {
        alert('Failed to cancel session');
    }
}
</script>
@endpush
@endsection