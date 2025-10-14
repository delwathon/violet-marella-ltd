@extends('layouts.app')
@section('title', 'Reports & Analytics')
@push('styles')
<link href="{{ asset('assets/css/reports.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Reports & Analytics</h1>
                <p class="page-subtitle">Business insights and performance analytics</p>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary">
                        <i class="fas fa-download me-2"></i>Export
                    </button>
                    <button class="btn btn-primary">
                        <i class="fas fa-chart-line me-2"></i>Custom Report
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Report Filters -->
    <div class="filter-section mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Business Unit</label>
                        <select class="form-select">
                            <option value="all">All Business Units</option>
                            <option value="gift-store">Gift Store</option>
                            <option value="supermarket">Mini Supermarket</option>
                            <option value="music-studio">Music Studio</option>
                            <option value="instrument-rental">Instrument Rental</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date Range</label>
                        <select class="form-select">
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month" selected>This Month</option>
                            <option value="quarter">This Quarter</option>
                            <option value="year">This Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div class="col-md-4" style="display: none;">
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label">From</label>
                                <input type="date" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="form-label">To</label>
                                <input type="date" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i>Apply
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="summary-card revenue">
                <div class="summary-header">
                    <div class="summary-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="summary-trend">
                        <span class="trend-value">+12%</span>
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>
                <div class="summary-content">
                    <h3 class="summary-amount">₦2,450,000</h3>
                    <p class="summary-label">Total Revenue</p>
                    <small class="summary-period">This Month</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="summary-card transactions">
                <div class="summary-header">
                    <div class="summary-icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="summary-trend">
                        <span class="trend-value">+8%</span>
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>
                <div class="summary-content">
                    <h3 class="summary-amount">1,847</h3>
                    <p class="summary-label">Transactions</p>
                    <small class="summary-period">This Month</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="summary-card customers">
                <div class="summary-header">
                    <div class="summary-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="summary-trend">
                        <span class="trend-value">+15%</span>
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>
                <div class="summary-content">
                    <h3 class="summary-amount">456</h3>
                    <p class="summary-label">Customers</p>
                    <small class="summary-period">This Month</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="summary-card average">
                <div class="summary-header">
                    <div class="summary-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="summary-trend">
                        <span class="trend-value">+5%</span>
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>
                <div class="summary-content">
                    <h3 class="summary-amount">₦1,327</h3>
                    <p class="summary-label">Avg Transaction</p>
                    <small class="summary-period">This Month</small>
                </div>
            </div>
        </div>
    </div>
    <!-- Charts and Analytics -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">Revenue Trends</h5>
                    <div class="chart-controls">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary active">Daily</button>
                            <button type="button" class="btn btn-outline-primary">Weekly</button>
                            <button type="button" class="btn btn-outline-primary">Monthly</button>
                        </div>
                    </div>
                </div>
                <div class="chart-body">
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">Business Unit Performance</h5>
                </div>
                <div class="chart-body">
                    <canvas id="businessChart" width="200" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- Detailed Reports Tabs -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="reportTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button">
                                <i class="fas fa-shopping-cart me-2"></i>Sales Report
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button">
                                <i class="fas fa-boxes me-2"></i>Inventory Report
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="customer-tab" data-bs-toggle="tab" data-bs-target="#customer" type="button">
                                <i class="fas fa-users me-2"></i>Customer Report
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" type="button">
                                <i class="fas fa-chart-pie me-2"></i>Financial Report
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="reportTabContent">
                        <!-- Sales Report Tab -->
                        <div class="tab-pane fade show active" id="sales" role="tabpanel">
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="metric-card">
                                        <h4>₦2.4M</h4>
                                        <p>Total Sales</p>
                                        <span class="metric-change positive">+12%</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-card">
                                        <h4>1,847</h4>
                                        <p>Orders</p>
                                        <span class="metric-change positive">+8%</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-card">
                                        <h4>₦1,327</h4>
                                        <p>Avg Order Value</p>
                                        <span class="metric-change positive">+5%</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-card">
                                        <h4>94.2%</h4>
                                        <p>Customer Satisfaction</p>
                                        <span class="metric-change positive">+2%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Product/Service</th>
                                            <th>Units Sold</th>
                                            <th>Revenue</th>
                                            <th>Growth</th>
                                            <th>Margin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Sales data will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Inventory Report Tab -->
                        <div class="tab-pane fade" id="inventory" role="tabpanel">
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="metric-card">
                                        <h4>847</h4>
                                        <p>Total Products</p>
                                        <span class="metric-change neutral">0%</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-card">
                                        <h4>23</h4>
                                        <p>Low Stock</p>
                                        <span class="metric-change negative">+15%</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-card">
                                        <h4>₦892K</h4>
                                        <p>Inventory Value</p>
                                        <span class="metric-change positive">+7%</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-card">
                                        <h4>4.2</h4>
                                        <p>Turnover Ratio</p>
                                        <span class="metric-change positive">+0.3</span>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Category</th>
                                            <th>Stock Level</th>
                                            <th>Reorder Point</th>
                                            <th>Value</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Inventory data will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Customer Report Tab -->
                        <div class="tab-pane fade" id="customer" role="tabpanel">
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="metric-card">
                                        <h4>456</h4>
                                        <p>Total Customers</p>
                                        <span class="metric-change positive">+15%</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-card">
                                        <h4>78</h4>
                                        <p>New Customers</p>
                                        <span class="metric-change positive">+25%</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-card">
                                        <h4>89%</h4>
                                        <p>Retention Rate</p>
                                        <span class="metric-change positive">+3%</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-card">
                                        <h4>₦5,374</h4>
                                        <p>Avg Lifetime Value</p>
                                        <span class="metric-change positive">+12%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Customer Segment</th>
                                            <th>Count</th>
                                            <th>Avg Spend</th>
                                            <th>Frequency</th>
                                            <th>Satisfaction</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Customer data will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Financial Report Tab -->
                        <div class="tab-pane fade" id="financial" role="tabpanel">
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="metric-card">
                                        <h4>₦2.4M</h4>
                                        <p>Gross Revenue</p>
                                        <span class="metric-change positive">+12%</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-card">
                                        <h4>₦1.8M</h4>
                                        <p>Net Revenue</p>
                                        <span class="metric-change positive">+10%</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-card">
                                        <h4>75%</h4>
                                        <p>Gross Margin</p>
                                        <span class="metric-change positive">+2%</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-card">
                                        <h4>22%</h4>
                                        <p>Net Margin</p>
                                        <span class="metric-change positive">+1.5%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="chart-container">
                                        <h6>Revenue by Business Unit</h6>
                                        <canvas id="revenueByUnitChart"></canvas>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="chart-container">
                                        <h6>Profit Margin Trends</h6>
                                        <canvas id="profitMarginChart"></canvas>
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
<!-- Custom Report Modal -->
<div class="modal fade" id="customReportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Custom Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="customReportForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Report Name</label>
                                <input type="text" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Report Type</label>
                                <select class="form-select" required>
                                    <option value="">Select Type</option>
                                    <option value="sales">Sales Analysis</option>
                                    <option value="inventory">Inventory Analysis</option>
                                    <option value="customer">Customer Analysis</option>
                                    <option value="financial">Financial Analysis</option>
                                    <option value="performance">Performance Analysis</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Date From</label>
                                <input type="date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Date To</label>
                                <input type="date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Business Units</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="gift-store" checked>
                            <label class="form-check-label">Gift Store</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="supermarket" checked>
                            <label class="form-check-label">Mini Supermarket</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="music-studio" checked>
                            <label class="form-check-label">Music Studio</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="instrument-rental" checked>
                            <label class="form-check-label">Instrument Rental</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Export Format</label>
                        <select class="form-select">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-chart-line me-2"></i>Generate Report
                </button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script src="{{ asset('assets/js/reports.js') }}"></script>
@endpush
@endsection
