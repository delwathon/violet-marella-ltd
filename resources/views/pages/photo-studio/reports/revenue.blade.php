@extends('layouts.app')
@section('title', 'Revenue Report')

@push('styles')
<link href="{{ asset('assets/css/photo-studio.css') }}" rel="stylesheet">
@endpush

@section('content')
@php
    $start = \Illuminate\Support\Carbon::parse($startDate);
    $end = \Illuminate\Support\Carbon::parse($endDate);
@endphp

<div class="content-area">
    <div class="page-header mb-4">
        <div class="row align-items-center gy-3">
            <div class="col-md">
                <h1 class="page-title mb-1">Revenue Report</h1>
                <p class="text-muted mb-0">Financial performance from {{ $start->format('d M Y') }} to {{ $end->format('d M Y') }}.</p>
            </div>
            <div class="col-md-auto d-flex gap-2 flex-wrap">
                <a href="{{ route('photo-studio.reports.export', ['type' => 'revenue', 'date' => now()->format('Y-m-d')]) }}" class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i>Export CSV</a>
                <a href="{{ route('photo-studio.reports.index') }}" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-2"></i>Back</a>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('photo-studio.reports.revenue') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" name="start_date" value="{{ $start->format('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" name="end_date" value="{{ $end->format('Y-m-d') }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i>Apply</button>
                    <a href="{{ route('photo-studio.reports.revenue', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" class="btn btn-outline-secondary">This Month</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple"><div class="stat-icon bg-success"><i class="fas fa-naira-sign"></i></div><div><small class="text-muted">Total Revenue</small><div class="fw-bold text-success">₦{{ number_format($stats['total_revenue'], 2) }}</div></div></div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple"><div class="stat-icon bg-primary"><i class="fas fa-receipt"></i></div><div><small class="text-muted">Paid Sessions</small><div class="fw-bold">{{ number_format($stats['total_sessions']) }}</div></div></div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple"><div class="stat-icon bg-info"><i class="fas fa-calculator"></i></div><div><small class="text-muted">Avg Session Value</small><div class="fw-bold">₦{{ number_format((float)$stats['average_session_value'], 2) }}</div></div></div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card-simple"><div class="stat-icon bg-warning"><i class="fas fa-tags"></i></div><div><small class="text-muted">Discounts Given</small><div class="fw-bold">₦{{ number_format($stats['discounts_given'], 2) }}</div></div></div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Revenue Breakdown</h6></div>
                <div class="card-body table-responsive">
                    <table class="table table-sm mb-0">
                        <tr><td>Base Revenue</td><td class="text-end">₦{{ number_format($stats['base_revenue'], 2) }}</td></tr>
                        <tr><td>Overtime Revenue</td><td class="text-end">₦{{ number_format($stats['overtime_revenue'], 2) }}</td></tr>
                        <tr><td>Total Revenue</td><td class="text-end fw-bold text-success">₦{{ number_format($stats['total_revenue'], 2) }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Revenue by Payment Method</h6></div>
                <div class="card-body p-0">
                    @if($byPaymentMethod->isEmpty())
                    <div class="text-center py-4 text-muted">No payment data available.</div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Method</th>
                                    <th>Transactions</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($byPaymentMethod as $row)
                                <tr>
                                    <td>{{ $row['method'] ?? 'Unknown' }}</td>
                                    <td>{{ $row['count'] }}</td>
                                    <td class="fw-semibold text-success">₦{{ number_format($row['amount'], 2) }}</td>
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

    <div class="row mb-4">
        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Revenue by Category</h6></div>
                <div class="card-body p-0">
                    @if($byCategory->isEmpty())
                    <div class="text-center py-4 text-muted">No category revenue for selected range.</div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Sessions</th>
                                    <th>Average</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($byCategory as $row)
                                <tr>
                                    <td>{{ $row['category'] }}</td>
                                    <td>{{ $row['sessions'] }}</td>
                                    <td>₦{{ number_format($row['average'], 2) }}</td>
                                    <td class="fw-semibold text-success">₦{{ number_format($row['revenue'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Daily Revenue Trend</h6></div>
                <div class="card-body p-0">
                    @if(empty($dailyRevenue))
                    <div class="text-center py-4 text-muted">No daily trend data available.</div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dailyRevenue as $day)
                                <tr>
                                    <td>{{ $day['date'] }}</td>
                                    <td class="fw-semibold text-success">₦{{ number_format($day['revenue'], 2) }}</td>
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

    <div class="card">
        <div class="card-header"><h6 class="mb-0">Paid Sessions ({{ $sessions->count() }})</h6></div>
        <div class="card-body p-0">
            @if($sessions->isEmpty())
            <div class="text-center py-4 text-muted">No paid sessions in selected date range.</div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Session</th>
                            <th>Customer</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Base</th>
                            <th>Overtime</th>
                            <th>Discount</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $session)
                        <tr>
                            <td>{{ $session->session_code }}</td>
                            <td>{{ $session->customer->name }}</td>
                            <td>{{ $session->category->name }}</td>
                            <td>{{ $session->check_in_time->format('d M Y') }}</td>
                            <td>₦{{ number_format($session->base_amount, 2) }}</td>
                            <td>₦{{ number_format($session->overtime_amount, 2) }}</td>
                            <td>₦{{ number_format($session->discount_amount, 2) }}</td>
                            <td class="fw-semibold text-success">₦{{ number_format($session->total_amount, 2) }}</td>
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
