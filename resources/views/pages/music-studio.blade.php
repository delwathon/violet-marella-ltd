@extends('layouts.app')
@section('title', 'Photo Studio Management')
@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Photo Studio Management</h1>
                <p class="page-subtitle">Manage studio sessions, customer check-ins, and time-based billing</p>
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
        <!-- Studio A -->
        <div class="col-md-3">
            <div class="studio-status-card studio-a">
                <div class="studio-header">
                    <h5>Studio A</h5>
                    <span class="status-badge occupied">Occupied</span>
                </div>
                <div class="studio-info">
                    <div class="customer-name">John Smith</div>
                    <div class="session-time">Started: 2:30 PM</div>
                    <div class="duration">Duration: 1h 45m</div>
                </div>
                <div class="studio-actions">
                    <button class="btn btn-sm btn-outline-primary">View</button>
                    <button class="btn btn-sm btn-success">Checkout</button>
                </div>
            </div>
        </div>
        <!-- Studio B -->
        <div class="col-md-3">
            <div class="studio-status-card studio-b">
                <div class="studio-header">
                    <h5>Studio B</h5>
                    <span class="status-badge occupied">Occupied</span>
                </div>
                <div class="studio-info">
                    <div class="customer-name">Sarah Johnson</div>
                    <div class="session-time">Started: 3:15 PM</div>
                    <div class="duration">Duration: 20m</div>
                </div>
                <div class="studio-actions">
                    <button class="btn btn-sm btn-outline-primary">View</button>
                    <button class="btn btn-sm btn-success">Checkout</button>
                </div>
            </div>
        </div>
        <!-- Studio C -->
        <div class="col-md-3">
            <div class="studio-status-card studio-c">
                <div class="studio-header">
                    <h5>Studio C</h5>
                    <span class="status-badge available">Available</span>
                </div>
                <div class="studio-info">
                    <div class="empty-state">
                        <i class="fas fa-door-open"></i>
                        <div>Ready for next customer</div>
                    </div>
                </div>
                <div class="studio-actions">
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#checkInModal">Check-in</button>
                </div>
            </div>
        </div>
        <!-- Studio D -->
        <div class="col-md-3">
            <div class="studio-status-card studio-d">
                <div class="studio-header">
                    <h5>Studio D</h5>
                    <span class="status-badge maintenance">Maintenance</span>
                </div>
                <div class="studio-info">
                    <div class="empty-state">
                        <i class="fas fa-tools"></i>
                        <div>Under maintenance</div>
                    </div>
                </div>
                <div class="studio-actions">
                    <button class="btn btn-sm btn-outline-secondary" disabled>Unavailable</button>
                </div>
            </div>
        </div>
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
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="qr-scanner-tab" data-bs-toggle="tab" data-bs-target="#qr-scanner" type="button">
                        <i class="fas fa-qrcode me-2"></i>QR Scanner
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
                        <div class="card-body">
                            <div class="session-item">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <div class="customer-info">
                                            <div class="customer-avatar">JS</div>
                                            <div>
                                                <div class="fw-semibold">John Smith</div>
                                                <small class="text-muted">+234 801 234 5678</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="studio-badge">Studio A</div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="time-info">
                                            <div class="check-in-time">2:30 PM</div>
                                            <small class="text-muted">Check-in</small>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="duration-badge active">1h 45m</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="session-actions">
                                            <button class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-qrcode"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-clock"></i>
                                            </button>
                                            <button class="btn btn-sm btn-success">
                                                <i class="fas fa-sign-out-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="session-item">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <div class="customer-info">
                                            <div class="customer-avatar">SJ</div>
                                            <div>
                                                <div class="fw-semibold">Sarah Johnson</div>
                                                <small class="text-muted">+234 802 345 6789</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="studio-badge">Studio B</div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="time-info">
                                            <div class="check-in-time">3:15 PM</div>
                                            <small class="text-muted">Check-in</small>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="duration-badge">20m</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="session-actions">
                                            <button class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-qrcode"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-clock"></i>
                                            </button>
                                            <button class="btn btn-sm btn-success">
                                                <i class="fas fa-sign-out-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                            <input type="number" class="form-control" id="baseTime" value="30">
                                            <small class="form-text text-muted">Minimum billable time</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Base Amount (₦)</label>
                                            <input type="number" class="form-control" id="baseAmount" value="2000">
                                            <small class="form-text text-muted">Amount for base time</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Total Time (minutes)</label>
                                            <input type="number" class="form-control" id="totalTime" placeholder="Enter total session time">
                                        </div>
                                        <button class="btn btn-primary">
                                            <i class="fas fa-calculator me-2"></i>Calculate Bill
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="billing-result">
                                        <div class="result-card">
                                            <h3 class="total-amount">₦0</h3>
                                            <p class="text-muted">Total Amount</p>
                                            <div class="calculation-breakdown" style="display: none;">
                                                <hr>
                                                <div class="breakdown-item">
                                                    <span>Base Time:</span>
                                                    <span>30 minutes</span>
                                                </div>
                                                <div class="breakdown-item">
                                                    <span>Base Amount:</span>
                                                    <span>₦2,000</span>
                                                </div>
                                                <div class="breakdown-item">
                                                    <span>Extra Time:</span>
                                                    <span>0 minutes</span>
                                                </div>
                                                <div class="breakdown-item">
                                                    <span>Extra Fee:</span>
                                                    <span>₦0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- QR Scanner Tab -->
                <div class="tab-pane fade" id="qr-scanner">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">QR Code Scanner</h5>
                        </div>
                        <div class="card-body">
                            <div class="qr-scanner-area text-center">
                                <div class="scanner-frame">
                                    <i class="fas fa-qrcode fa-5x text-muted mb-3"></i>
                                    <h5>Scan Customer QR Code</h5>
                                    <p class="text-muted">Position the QR code within the frame to scan</p>
                                    <button class="btn btn-primary btn-lg">
                                        <i class="fas fa-camera me-2"></i>Start Scanner
                                    </button>
                                </div>
                            </div>
                            <div class="recent-scans mt-4">
                                <h6>Recent Scans</h6>
                                <div class="scan-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold">John Smith</div>
                                            <small class="text-muted">QR_001_1640995200 • Scanned 2 minutes ago</small>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary">Process Checkout</button>
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
                            <div class="summary-value">24</div>
                            <div class="summary-label">Total Sessions</div>
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-icon bg-success">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="summary-info">
                            <div class="summary-value">48h 30m</div>
                            <div class="summary-label">Total Hours</div>
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-icon bg-warning">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="summary-info">
                            <div class="summary-value">₦96,600</div>
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
                    <div class="rate-item">
                        <div class="d-flex justify-content-between">
                            <span>Base Rate (30 min)</span>
                            <strong>₦2,000</strong>
                        </div>
                    </div>
                    <div class="rate-item">
                        <div class="d-flex justify-content-between">
                            <span>Per Additional Minute</span>
                            <strong>₦66.67</strong>
                        </div>
                    </div>
                    <div class="rate-item">
                        <div class="d-flex justify-content-between">
                            <span>Hourly Rate</span>
                            <strong>₦4,000</strong>
                        </div>
                    </div>
                    <hr>
                    <button class="btn btn-outline-primary btn-sm w-100">Update Rates</button>
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
                        <button class="btn btn-outline-success">
                            <i class="fas fa-chart-bar me-2"></i>Daily Report
                        </button>
                        <button class="btn btn-outline-info">
                            <i class="fas fa-print me-2"></i>Print QR Codes
                        </button>
                        <button class="btn btn-outline-warning">
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
            <div class="modal-body">
                <form id="checkInForm">
                    <div class="mb-3">
                        <label class="form-label">Customer Name</label>
                        <input type="text" class="form-control" required placeholder="Enter customer full name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" required placeholder="+234 xxx xxx xxxx">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email (Optional)</label>
                        <input type="email" class="form-control" placeholder="customer@email.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Studio Room</label>
                        <select class="form-select" required id="studioSelect">
                            <option value="">Select Studio</option>
                            <option value="studio-a">Studio A</option>
                            <option value="studio-b">Studio B</option>
                            <option value="studio-c">Studio C</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expected Duration</label>
                        <select class="form-select">
                            <option value="30">30 minutes</option>
                            <option value="60">1 hour</option>
                            <option value="90">1.5 hours</option>
                            <option value="120">2 hours</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="generateQR" checked>
                        <label class="form-check-label" for="generateQR">
                            Generate QR code for checkout
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-qrcode me-2"></i>Check-in & Generate QR
                </button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="{{ asset('assets/js/photo-studio.js') }}"></script>
@endpush
@endsection
