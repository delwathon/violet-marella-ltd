@extends('layouts.app')
@section('title', 'Today\'s Sales - Violet Marella Limited')
@push('styles')
<link href="{{ asset('assets/css/lounge.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Today's Sales</h1>
                <p class="page-subtitle">{{ now()->format('l, F d, Y') }}</p>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <a href="{{ route('anire-craft-store.index') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>New Sale
                    </a>
                    <button class="btn btn-outline-primary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">₦{{ number_format($todayTotal, 2) }}</div>
                    <div class="stat-label">Total Sales</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ number_format($todayCount) }}</div>
                    <div class="stat-label">Transactions</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ number_format($todayItems) }}</div>
                    <div class="stat-label">Items Sold</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">₦{{ number_format($todayAverage, 2) }}</div>
                    <div class="stat-label">Avg Transaction</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hourly Sales Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Hourly Sales Breakdown</h5>
                </div>
                <div class="card-body">
                    <canvas id="hourlySalesChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('anire-craft-store.sales.today') }}">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by receipt # or customer name..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Today's Transactions</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Receipt #</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Staff</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td>{{ $sale->sale_date->format('H:i') }}</td>
                                <td>
                                    <strong>{{ $sale->receipt_number }}</strong>
                                </td>
                                <td>
                                    @if($sale->customer)
                                        <a href="{{ route('anire-craft-store.customers.show', $sale->customer->id) }}">
                                            {{ $sale->customer->full_name }}
                                        </a>
                                    @else
                                        <span class="text-muted">Walk-in</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $sale->saleItems->count() }}</span>
                                </td>
                                <td>
                                    <strong>₦{{ number_format($sale->total_amount, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $sale->payment_method === 'cash' ? 'success' : ($sale->payment_method === 'card' ? 'primary' : 'info') }}">
                                        {{ ucfirst($sale->payment_method) }}
                                    </span>
                                </td>
                                <td>
                                    @if($sale->staff)
                                        {{ $sale->staff->first_name }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('anire-craft-store.sales.show', $sale->id) }}" class="btn btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-outline-secondary" onclick="printReceipt({{ $sale->id }})" title="Print">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-receipt fa-3x mb-3"></i>
                                    <br>No sales today yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $sales->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Hourly Sales Chart
const hourlySalesData = @json($hourlySales);

const hours = Array.from({length: 24}, (_, i) => i);
const salesByHour = hours.map(hour => {
    const data = hourlySalesData.find(item => item.hour === hour);
    return data ? parseFloat(data.total) : 0;
});

const ctx = document.getElementById('hourlySalesChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: hours.map(h => `${h}:00`),
        datasets: [{
            label: 'Sales Amount (₦)',
            data: salesByHour,
            backgroundColor: 'rgba(59, 130, 246, 0.5)',
            borderColor: 'rgba(59, 130, 246, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₦' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Sales: ₦' + context.parsed.y.toLocaleString();
                    }
                }
            }
        }
    }
});

function printReceipt(saleId) {
    window.open(`/app/anire-craft-store/sales/${saleId}/receipt`, '_blank');
}
</script>
@endpush
@endsection