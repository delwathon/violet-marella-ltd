@extends('layouts.app')
@section('title', 'Customer Details')
@push('styles')
<link href="{{ asset('assets/css/photo-studio-light.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area" style="background-color: #f9fafb; min-height: 100vh;">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title mb-1" style="color: #1f2937;">Customer Details</h1>
                <p class="text-muted mb-0">View customer information and session history</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary" onclick="editCustomer({{ $customer->id }})">
                    <i class="fas fa-edit me-2"></i>Edit Customer
                </button>
                <a href="{{ route('photo-studio.customers.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Customer Information Card -->
        <div class="col-lg-4 mb-4">
            <div class="card" style="background: white; border: 1px solid #e5e7eb;">
                <div class="card-body text-center" style="background: white; padding: 2rem;">
                    <div class="customer-avatar mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem; background: linear-gradient(135deg, #6f42c1, #8b5cf6); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                        {{ $customer->initials }}
                    </div>
                    <h4 style="color: #1f2937; margin-bottom: 0.5rem;">{{ $customer->name }}</h4>
                    <p class="text-muted mb-3">Customer ID: #{{ $customer->id }}</p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <span class="badge bg-{{ $customer->is_active ? 'success' : 'secondary' }}" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                            {{ $customer->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    
                    <div class="text-start mt-4" style="background: #f9fafb; padding: 1.5rem; border-radius: 0.75rem;">
                        <h6 style="color: #1f2937; margin-bottom: 1rem; font-weight: 600;">Contact Information</h6>
                        
                        <div class="mb-3 d-flex align-items-start">
                            <i class="fas fa-phone text-muted me-3" style="margin-top: 0.25rem; width: 20px;"></i>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Phone Number</small>
                                <strong style="color: #1f2937;">{{ $customer->phone }}</strong>
                            </div>
                        </div>

                        @if($customer->email)
                        <div class="mb-3 d-flex align-items-start">
                            <i class="fas fa-envelope text-muted me-3" style="margin-top: 0.25rem; width: 20px;"></i>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Email Address</small>
                                <strong style="color: #1f2937;">{{ $customer->email }}</strong>
                            </div>
                        </div>
                        @endif

                        @if($customer->address)
                        <div class="mb-3 d-flex align-items-start">
                            <i class="fas fa-map-marker-alt text-muted me-3" style="margin-top: 0.25rem; width: 20px;"></i>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Address</small>
                                <strong style="color: #1f2937;">{{ $customer->address }}</strong>
                            </div>
                        </div>
                        @endif

                        @if($customer->date_of_birth)
                        <div class="mb-3 d-flex align-items-start">
                            <i class="fas fa-birthday-cake text-muted me-3" style="margin-top: 0.25rem; width: 20px;"></i>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Date of Birth</small>
                                <strong style="color: #1f2937;">{{ $customer->date_of_birth->format('M d, Y') }}</strong>
                            </div>
                        </div>
                        @endif

                        @if($customer->last_visit)
                        <div class="d-flex align-items-start">
                            <i class="fas fa-history text-muted me-3" style="margin-top: 0.25rem; width: 20px;"></i>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Last Visit</small>
                                <strong style="color: #1f2937;">{{ $customer->last_visit->format('M d, Y') }}</strong>
                                <br>
                                <small class="text-muted">{{ $customer->last_visit->diffForHumans() }}</small>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    @if($customer->notes)
                    <div class="alert alert-light mt-3 text-start" style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 0.5rem;">
                        <div class="d-flex">
                            <i class="fas fa-sticky-note text-warning me-2"></i>
                            <div>
                                <strong style="color: #1f2937;">Notes</strong>
                                <p class="mb-0 mt-1" style="color: #6b7280; font-size: 0.875rem;">{{ $customer->notes }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics and Sessions -->
        <div class="col-lg-8">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="stat-card" style="background: white; border: 1px solid #e5e7eb; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div class="stat-icon bg-primary" style="width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #6f42c1, #8b5cf6); color: white; font-size: 1.5rem; margin-bottom: 1rem;">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label" style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;">Total Sessions</div>
                            <div class="stat-value" style="font-size: 2rem; font-weight: 700; color: #1f2937; line-height: 1;">{{ $completedSessions }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="stat-card" style="background: white; border: 1px solid #e5e7eb; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div class="stat-icon bg-success" style="width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #10b981, #059669); color: white; font-size: 1.5rem; margin-bottom: 1rem;">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label" style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;">Total Spent</div>
                            <div class="stat-value" style="font-size: 2rem; font-weight: 700; color: #1f2937; line-height: 1;">₦{{ number_format($totalRevenue, 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="stat-card" style="background: white; border: 1px solid #e5e7eb; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div class="stat-icon bg-info" style="width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; font-size: 1.5rem; margin-bottom: 1rem;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label" style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;">Total Time</div>
                            <div class="stat-value" style="font-size: 2rem; font-weight: 700; color: #1f2937; line-height: 1;">
                                @php
                                    $hours = floor($totalMinutes / 60);
                                    $mins = $totalMinutes % 60;
                                @endphp
                                {{ $hours }}h {{ $mins }}m
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Sessions -->
            <div class="card" style="background: white; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: white; border-bottom: 1px solid #e5e7eb; padding: 1.25rem;">
                    <h5 class="mb-0" style="color: #1f2937; font-weight: 600;">
                        <i class="fas fa-history me-2 text-primary"></i>Recent Sessions
                    </h5>
                    <span class="badge bg-light text-dark">{{ $customer->sessions->count() }} Total</span>
                </div>
                <div class="card-body" style="background: white; padding: 0;">
                    @if($customer->sessions->isEmpty())
                        <div class="text-center py-5" style="padding: 3rem 1rem;">
                            <i class="fas fa-inbox fa-3x text-muted mb-3" style="opacity: 0.5;"></i>
                            <h5 class="text-muted mb-2">No sessions yet</h5>
                            <p class="text-muted mb-3">This customer hasn't booked any sessions</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkInModal" onclick="selectCustomerForCheckIn({{ $customer->id }})">
                                <i class="fas fa-plus me-2"></i>Book First Session
                            </button>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="background: white;">
                                <thead style="background-color: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                                    <tr>
                                        <th style="color: #1f2937; font-weight: 600; padding: 1rem;">Date & Time</th>
                                        <th style="color: #1f2937; font-weight: 600; padding: 1rem;">Studio</th>
                                        <th style="color: #1f2937; font-weight: 600; padding: 1rem;">Duration</th>
                                        <th style="color: #1f2937; font-weight: 600; padding: 1rem;">Amount</th>
                                        <th style="color: #1f2937; font-weight: 600; padding: 1rem;">Payment</th>
                                        <th style="color: #1f2937; font-weight: 600; padding: 1rem;">Status</th>
                                        <th style="color: #1f2937; font-weight: 600; padding: 1rem;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->sessions as $session)
                                    <tr style="background: white; border-bottom: 1px solid #f3f4f6;">
                                        <td style="color: #1f2937; padding: 1rem;">
                                            <div>
                                                <strong>{{ $session->check_in_time->format('M d, Y') }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $session->check_in_time->format('g:i A') }}</small>
                                            </div>
                                        </td>
                                        <td style="color: #1f2937; padding: 1rem;">
                                            <span class="badge" style="background: #f3f0ff; color: #6f42c1; padding: 0.5rem 0.75rem; border: 1px solid #6f42c1; border-radius: 20px; font-weight: 600;">
                                                {{ $session->studio->name }}
                                            </span>
                                        </td>
                                        <td style="color: #1f2937; padding: 1rem;">
                                            @if($session->actual_duration)
                                                <strong>{{ $session->actual_duration }}</strong> min
                                            @else
                                                <span class="text-muted">In progress</span>
                                            @endif
                                        </td>
                                        <td style="padding: 1rem;">
                                            <strong class="text-success" style="font-size: 1.1rem;">₦{{ number_format($session->total_amount, 2) }}</strong>
                                        </td>
                                        <td style="padding: 1rem;">
                                            <span class="badge bg-{{ $session->payment_status === 'paid' ? 'success' : ($session->payment_status === 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($session->payment_status) }}
                                            </span>
                                        </td>
                                        <td style="padding: 1rem;">
                                            <span class="badge bg-{{ $session->status === 'completed' ? 'primary' : ($session->status === 'active' ? 'success' : 'secondary') }}">
                                                {{ ucfirst($session->status) }}
                                            </span>
                                        </td>
                                        <td style="padding: 1rem;">
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewSessionDetails({{ $session->id }})" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
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
    </div>
</div>

<!-- Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background: white;">
            <div class="modal-header" style="background: linear-gradient(135deg, #f3f0ff, white); border-bottom: 1px solid #e5e7eb;">
                <h5 class="modal-title" style="color: #1f2937;">
                    <i class="fas fa-edit me-2"></i>Edit Customer
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCustomerForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" id="editCustomerBody" style="background: white;">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer" style="background: white; border-top: 1px solid #e5e7eb;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('pages.photo-studio.modals.session-details')

@push('scripts')
<script src="{{ asset('assets/js/photo-studio.js') }}"></script>
@endpush
@endsection