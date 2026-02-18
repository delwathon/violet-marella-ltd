@extends('layouts.app')
@section('title', 'Customer Report')
@push('styles')
<link href="{{ asset('assets/css/photo-studio-light.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area" style="background-color: #f9fafb;">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title mb-1" style="color: #1f2937;">Customer Report</h1>
                <p class="text-muted mb-0">Customer statistics and spending analysis</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('photo-studio.reports.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Reports
                </a>
                <button class="btn btn-primary" onclick="window.location.href='{{ route('photo-studio.customers.export') }}'">
                    <i class="fas fa-download me-2"></i>Export CSV
                </button>
            </div>
        </div>
    </div>

    <!-- Customer Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card" style="background: white;">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Customers</div>
                    <div class="stat-value">{{ $stats['total_customers'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card" style="background: white;">
                <div class="stat-icon bg-success">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Active Customers</div>
                    <div class="stat-value">{{ $stats['active_customers'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card" style="background: white;">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value">₦{{ number_format($stats['total_revenue'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card" style="background: white;">
                <div class="stat-icon bg-info">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Avg Spent</div>
                    <div class="stat-value">₦{{ number_format($stats['average_spent'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Customers -->
    <div class="card" style="background: white;">
        <div class="card-header" style="background: white; border-bottom: 1px solid #e5e7eb;">
            <h5 class="mb-0" style="color: #1f2937;">Top Customers by Spending</h5>
        </div>
        <div class="card-body" style="background: white;">
            @if($customers->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No customers found</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover" style="background: white;">
                        <thead style="background-color: #f9fafb;">
                            <tr>
                                <th style="color: #1f2937;">Rank</th>
                                <th style="color: #1f2937;">Customer</th>
                                <th style="color: #1f2937;">Contact</th>
                                <th style="color: #1f2937;">Total Sessions</th>
                                <th style="color: #1f2937;">Total Spent</th>
                                <th style="color: #1f2937;">Last Visit</th>
                                <th style="color: #1f2937;">Status</th>
                                <th style="color: #1f2937;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $index => $customer)
                            <tr style="background: white;">
                                <td style="color: #1f2937;">
                                    @if($index < 3)
                                        <span class="badge bg-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'danger') }}" style="font-size: 1rem; padding: 0.5rem 0.75rem;">
                                            #{{ $index + 1 }}
                                        </span>
                                    @else
                                        <strong>{{ $index + 1 }}</strong>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="customer-avatar-sm me-2">{{ $customer->initials }}</div>
                                        <div>
                                            <div class="fw-semibold" style="color: #1f2937;">{{ $customer->name }}</div>
                                            <small class="text-muted">ID: {{ $customer->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td style="color: #1f2937;">
                                    {{ $customer->phone }}<br>
                                    @if($customer->email)
                                        <small class="text-muted">{{ $customer->email }}</small>
                                    @endif
                                </td>
                                <td style="color: #1f2937;">
                                    <strong>{{ $customer->total_sessions }}</strong>
                                </td>
                                <td style="color: #1f2937;">
                                    <strong class="text-success" style="font-size: 1.1rem;">₦{{ number_format($customer->total_spent, 2) }}</strong>
                                </td>
                                <td style="color: #1f2937;">
                                    @if($customer->last_visit)
                                        {{ $customer->last_visit->format('M d, Y') }}<br>
                                        <small class="text-muted">{{ $customer->last_visit->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted">Never</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $customer->is_active ? 'success' : 'secondary' }}">
                                        {{ $customer->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('photo-studio.customers.show', $customer->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
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