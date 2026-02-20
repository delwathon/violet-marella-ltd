@extends('layouts.app')
@section('title', 'Customer Report')

@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-area">
    <div class="page-header mb-4">
        <div class="row align-items-center gy-3">
            <div class="col-md">
                <h1 class="page-title mb-1">Customer Report</h1>
                <p class="text-muted mb-0">Customer distribution, spend tiers, and engagement metrics.</p>
            </div>
            <div class="col-md-auto d-flex gap-2 flex-wrap">
                <a href="{{ route('photo-studio.customers.export') }}" class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i>Export Customers</a>
                <a href="{{ route('photo-studio.reports.index') }}" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-2"></i>Back</a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3"><div class="stat-card-simple"><div class="stat-icon bg-primary"><i class="fas fa-users"></i></div><div><small class="text-muted">Total Customers</small><div class="fw-bold">{{ number_format($stats['total_customers']) }}</div></div></div></div>
        <div class="col-md-3 mb-3"><div class="stat-card-simple"><div class="stat-icon bg-success"><i class="fas fa-user-check"></i></div><div><small class="text-muted">Active Customers</small><div class="fw-bold">{{ number_format($stats['active_customers']) }}</div></div></div></div>
        <div class="col-md-3 mb-3"><div class="stat-card-simple"><div class="stat-icon bg-info"><i class="fas fa-repeat"></i></div><div><small class="text-muted">Total Sessions</small><div class="fw-bold">{{ number_format($stats['total_sessions']) }}</div></div></div></div>
        <div class="col-md-3 mb-3"><div class="stat-card-simple"><div class="stat-icon bg-warning"><i class="fas fa-naira-sign"></i></div><div><small class="text-muted">Total Revenue</small><div class="fw-bold text-success">₦{{ number_format($stats['total_revenue'], 2) }}</div></div></div></div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-5 mb-3">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Customer Segments</h6></div>
                <div class="card-body table-responsive">
                    <table class="table table-sm mb-0">
                        <tr><td>New Customers (&lt; 3 sessions)</td><td class="text-end">{{ number_format($stats['new_customers']) }}</td></tr>
                        <tr><td>Regular Customers (10+ sessions)</td><td class="text-end">{{ number_format($stats['regular_customers']) }}</td></tr>
                        <tr><td>Blacklisted Customers</td><td class="text-end">{{ number_format($stats['blacklisted_customers']) }}</td></tr>
                        <tr><td>Average Spent per Customer</td><td class="text-end">₦{{ number_format((float)$stats['average_spent'], 2) }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-7 mb-3">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Tier Distribution</h6></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Tier</th>
                                    <th>Customers</th>
                                    <th>Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($byTier as $tier => $count)
                                @php
                                    $share = $stats['total_customers'] > 0 ? ($count / $stats['total_customers']) * 100 : 0;
                                @endphp
                                <tr>
                                    <td><span class="badge bg-primary">{{ $tier }}</span></td>
                                    <td>{{ number_format($count) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 8px; min-width: 130px;">
                                                <div class="progress-bar bg-primary" style="width: {{ min(100, $share) }}%"></div>
                                            </div>
                                            <strong>{{ number_format($share, 2) }}%</strong>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h6 class="mb-0">Top Customers by Spend</h6></div>
        <div class="card-body p-0">
            @if($customers->isEmpty())
            <div class="text-center py-4 text-muted">No customer data available.</div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Total Sessions</th>
                            <th>Total Spent</th>
                            <th>Tier</th>
                            <th>Status</th>
                            <th>Last Visit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers->take(50) as $customer)
                        <tr>
                            <td><a href="{{ route('photo-studio.customers.show', $customer->id) }}" class="text-decoration-none">{{ $customer->name }}</a></td>
                            <td>{{ $customer->phone }}</td>
                            <td>{{ number_format($customer->total_sessions) }}</td>
                            <td class="fw-semibold text-success">₦{{ number_format($customer->total_spent, 2) }}</td>
                            <td><span class="badge bg-primary">{{ $customer->tier }}</span></td>
                            <td><span class="badge bg-{{ $customer->is_blacklisted ? 'danger' : ($customer->is_active ? 'success' : 'secondary') }}">{{ $customer->status_label }}</span></td>
                            <td>{{ $customer->last_visit ? $customer->last_visit->format('d M Y') : 'Never' }}</td>
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
