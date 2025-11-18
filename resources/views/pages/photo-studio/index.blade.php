@extends('layouts.app')
@section('title', 'Photo Studio Management')
@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title mb-1">Photo Studio Management</h1>
                <p class="text-muted mb-0">Manage studio sessions, customer check-ins, and time-based billing</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkInModal">
                    <i class="fas fa-user-plus me-2"></i>New Check-in
                </button>
            </div>
        </div>
    </div>

    <!-- Studio Status Cards -->
    <div class="row mb-4">
        @foreach($studios as $studio)
        <div class="col-md-3 mb-3">
            <div class="studio-status-card studio-{{ strtolower(str_replace(' ', '-', $studio->code)) }}" data-studio-id="{{ $studio->id }}">
                <div class="studio-header">
                    <h5 class="mb-0">{{ $studio->name }}</h5>
                    <span class="status-badge {{ $studio->status }}">
                        {{ ucfirst($studio->status) }}
                    </span>
                </div>
                <div class="studio-info">
                    @if($studio->status === 'occupied' && $studio->activeSession)
                        @php
                            $session = $studio->activeSession;
                            $duration = $session->getCurrentDuration();
                            $hours = floor($duration / 60);
                            $minutes = $duration % 60;
                            $durationText = $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
                        @endphp
                        <div class="customer-name">{{ $session->customer->name }}</div>
                        <div class="session-time">Started: {{ $session->check_in_time->format('g:i A') }}</div>
                        <div class="duration">Duration: {{ $durationText }}</div>
                    @elseif($studio->status === 'available')
                        <div class="empty-state">
                            <i class="fas fa-door-open"></i>
                            <div>Ready for next customer</div>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-tools"></i>
                            <div>Under maintenance</div>
                        </div>
                    @endif
                </div>
                <div class="studio-actions">
                    @if($studio->status === 'occupied' && $studio->activeSession)
                        <button class="btn btn-sm btn-outline-primary" onclick="viewSession({{ $studio->activeSession->id }})">View</button>
                        <button class="btn btn-sm btn-success" onclick="showCheckoutModal({{ $studio->activeSession->id }})">Checkout</button>
                    @elseif($studio->status === 'available')
                        <button class="btn btn-sm btn-primary" onclick="selectStudio({{ $studio->id }})" data-bs-toggle="modal" data-bs-target="#checkInModal">Check-in</button>
                    @else
                        <button class="btn btn-sm btn-outline-secondary" disabled>Unavailable</button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Main Content Tabs -->
    <div class="row">
        <div class="col-lg-8">
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs mb-4" id="studioTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="sessions-tab" data-bs-toggle="tab" data-bs-target="#sessions" type="button">
                        <i class="fas fa-list me-2"></i>Active Sessions
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="billing-tab" data-bs-toggle="tab" data-bs-target="#billing" type="button">
                        <i class="fas fa-calculator me-2"></i>Billing Calculator
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="studioTabContent">
                <!-- Active Sessions Tab -->
                <div class="tab-pane fade show active" id="sessions">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Active Studio Sessions</h5>
                        </div>
                        <div class="card-body" id="activeSessionsContainer">
                            @if($activeSessions->isEmpty())
                                <div class="text-center py-4">
                                    <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                                    <div>No active sessions</div>
                                    <small class="text-muted">Check in customers to see active sessions here</small>
                                </div>
                            @else
                                @foreach($activeSessions as $session)
                                    @php
                                        $duration = $session->getCurrentDuration();
                                        $hours = floor($duration / 60);
                                        $minutes = $duration % 60;
                                        $durationText = $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
                                        $isOvertime = $duration > $session->expected_duration + 30;
                                        $isWarning = $duration > $session->expected_duration && !$isOvertime;
                                        $durationClass = $isOvertime ? 'overtime' : ($isWarning ? 'warning' : 'active');
                                    @endphp
                                    <div class="session-item" data-session-id="{{ $session->id }}">
                                        <div class="row align-items-center">
                                            <div class="col-md-3">
                                                <div class="customer-info">
                                                    <div class="customer-avatar">{{ $session->customer->initials }}</div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $session->customer->name }}</div>
                                                        <small class="text-muted">{{ $session->customer->phone }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="studio-badge">{{ $session->studio->name }}</div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="time-info">
                                                    <div class="check-in-time">{{ $session->check_in_time->format('g:i A') }}</div>
                                                    <small class="text-muted">Check-in</small>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="duration-badge {{ $durationClass }}">{{ $durationText }}</div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="session-actions">
                                                    <button class="btn btn-sm btn-outline-info" onclick="showQRCode({{ $session->id }})" title="Show QR Code">
                                                        <i class="fas fa-qrcode"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-primary" onclick="extendSession({{ $session->id }})" title="Extend Session">
                                                        <i class="fas fa-clock"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-success" onclick="showCheckoutModal({{ $session->id }})" title="Checkout">
                                                        <i class="fas fa-sign-out-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Billing Calculator Tab -->
                <div class="tab-pane fade" id="billing">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Time-based Billing Calculator</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="billing-form">
                                        <div class="mb-3">
                                            <label class="form-label">Base Time (minutes)</label>
                                            <input type="number" class="form-control" id="baseTime" value="{{ $defaultRate->base_time ?? 30 }}">
                                            <small class="form-text text-muted">Minimum billable time</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Base Amount (₦)</label>
                                            <input type="number" class="form-control" id="baseAmount" value="{{ $defaultRate->base_amount ?? 2000 }}">
                                            <small class="form-text text-muted">Amount for base time</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Total Time (minutes)</label>
                                            <input type="number" class="form-control" id="totalTime" placeholder="Enter total session time">
                                        </div>
                                        <button class="btn btn-primary" onclick="calculateBilling()">
                                            <i class="fas fa-calculator me-2"></i>Calculate Bill
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="billing-result">
                                        <div class="result-card">
                                            <h3 class="total-amount" id="totalAmount">₦0</h3>
                                            <p class="text-muted">Total Amount</p>
                                            <div class="calculation-breakdown" id="calculationBreakdown" style="display: none;">
                                                <hr>
                                                <div class="breakdown-item">
                                                    <span>Base Time:</span>
                                                    <span id="breakdownBaseTime">30 minutes</span>
                                                </div>
                                                <div class="breakdown-item">
                                                    <span>Base Amount:</span>
                                                    <span id="breakdownBaseAmount">₦2,000</span>
                                                </div>
                                                <div class="breakdown-item">
                                                    <span>Extra Time:</span>
                                                    <span id="breakdownExtraTime">0 minutes</span>
                                                </div>
                                                <div class="breakdown-item">
                                                    <span>Extra Fee:</span>
                                                    <span id="breakdownExtraFee">₦0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Today's Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Today's Summary</h6>
                </div>
                <div class="card-body">
                    <div class="summary-item">
                        <div class="summary-icon bg-primary">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="summary-info">
                            <div class="summary-value">{{ $todayStats['totalSessions'] }}</div>
                            <div class="summary-label">Total Sessions</div>
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-icon bg-success">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="summary-info">
                            <div class="summary-value">{{ $todayStats['totalHours'] }}</div>
                            <div class="summary-label">Total Hours</div>
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-icon bg-warning">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="summary-info">
                            <div class="summary-value">₦{{ number_format($todayStats['revenue'], 2) }}</div>
                            <div class="summary-label">Revenue</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Studio Rates -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Studio Rates</h6>
                </div>
                <div class="card-body">
                    @if($defaultRate)
                    <div class="rate-item">
                        <div class="d-flex justify-content-between">
                            <span>Base Rate ({{ $defaultRate->base_time }} min)</span>
                            <strong>₦{{ number_format($defaultRate->base_amount, 2) }}</strong>
                        </div>
                    </div>
                    <div class="rate-item">
                        <div class="d-flex justify-content-between">
                            <span>Per Additional Minute</span>
                            <strong>₦{{ number_format($defaultRate->per_minute_rate, 2) }}</strong>
                        </div>
                    </div>
                    <div class="rate-item">
                        <div class="d-flex justify-content-between">
                            <span>Hourly Rate</span>
                            <strong>₦{{ number_format($defaultRate->hourly_rate, 2) }}</strong>
                        </div>
                    </div>
                    @else
                    <p class="text-muted">No rates configured</p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkInModal">
                            <i class="fas fa-user-plus me-2"></i>New Check-in
                        </button>
                        <button class="btn btn-outline-success" onclick="window.location.href='{{ route('reports.index') }}'">
                            <i class="fas fa-chart-bar me-2"></i>Daily Report
                        </button>
                        <button class="btn btn-outline-info" onclick="exportSessions()">
                            <i class="fas fa-download me-2"></i>Export Sessions
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Check-in Modal -->
<div class="modal fade" id="checkInModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Customer Check-in</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="checkInForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="customer_name" required placeholder="Enter customer full name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" name="customer_phone" required placeholder="+234 xxx xxx xxxx">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email (Optional)</label>
                        <input type="email" class="form-control" name="customer_email" placeholder="customer@email.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Studio Room <span class="text-danger">*</span></label>
                        <select class="form-select" name="studio_id" id="studioSelect" required>
                            <option value="">Select Studio</option>
                            @foreach($studios->where('status', 'available') as $studio)
                                <option value="{{ $studio->id }}">{{ $studio->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expected Duration <span class="text-danger">*</span></label>
                        <select class="form-select" name="expected_duration" required>
                            <option value="30">30 minutes</option>
                            <option value="60" selected>1 hour</option>
                            <option value="90">1.5 hours</option>
                            <option value="120">2 hours</option>
                            <option value="180">3 hours</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-2"></i>Check-in Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Checkout Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="checkoutForm">
                @csrf
                <input type="hidden" name="session_id" id="checkoutSessionId">
                <div class="modal-body">
                    <div id="checkoutDetails" class="mb-3">
                        <!-- Session details will be loaded here -->
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select" name="payment_method" required>
                            <option value="">Select payment method</option>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="transfer">Transfer</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discount Amount (Optional)</label>
                        <input type="number" class="form-control" name="discount_amount" min="0" step="0.01" placeholder="0.00">
                    </div>
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
<script src="{{ asset('assets/js/photo-studio.js') }}"></script>
@endpush
@endsection