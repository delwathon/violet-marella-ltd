<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supermarket Dashboard - Violet Marella Limited</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 0;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .top-navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .content-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: none;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
        }

        .badge {
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <i class="fas fa-store fa-2x mb-2"></i>
                        <h6>Violet Marella</h6>
                        <small>Supermarket POS</small>
                    </div>

                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('supermarket.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('supermarket.pos') }}">
                                <i class="fas fa-cash-register me-2"></i>
                                POS System
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('supermarket.products') }}">
                                <i class="fas fa-boxes me-2"></i>
                                Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('supermarket.customers') }}">
                                <i class="fas fa-users me-2"></i>
                                Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('supermarket.sales') }}">
                                <i class="fas fa-receipt me-2"></i>
                                Sales
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('supermarket.inventory') }}">
                                <i class="fas fa-warehouse me-2"></i>
                                Inventory
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('supermarket.reports') }}">
                                <i class="fas fa-chart-bar me-2"></i>
                                Reports
                            </a>
                        </li>
                    </ul>

                    <hr class="my-4">

                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Top Navbar -->
                <nav class="navbar navbar-expand-lg top-navbar">
                    <div class="container-fluid">
                        <div class="navbar-nav">
                            <span class="navbar-text">
                                Welcome back, {{ Auth::guard('staff')->user()->full_name }}!
                            </span>
                        </div>
                        <div class="navbar-nav ms-auto">
                            <span class="badge bg-primary me-2">{{ Auth::guard('staff')->user()->role }}</span>
                            <span class="navbar-text">
                                {{ now()->format('M d, Y') }}
                            </span>
                        </div>
                    </div>
                </nav>

                <!-- Content -->
                <div class="container-fluid py-4">
                    <!-- Page Header -->
                    <div class="row mb-4">
                        <div class="col">
                            <h2>Dashboard</h2>
                            <p class="text-muted">Overview of your supermarket operations</p>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-success me-3">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div>
                                        <div class="stat-value">₦{{ number_format($todaySales, 2) }}</div>
                                        <div class="stat-label">Today's Sales</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-primary me-3">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                    <div>
                                        <div class="stat-value">{{ number_format($todayTransactions) }}</div>
                                        <div class="stat-label">Transactions</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-warning me-3">
                                        <i class="fas fa-boxes"></i>
                                    </div>
                                    <div>
                                        <div class="stat-value">{{ number_format($totalStock) }}</div>
                                        <div class="stat-label">Items in Stock</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-info me-3">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div>
                                        <div class="stat-value">{{ number_format($customersServed) }}</div>
                                        <div class="stat-label">Customers Served</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Low Stock Alert -->
                        <div class="col-md-6 mb-4">
                            <div class="card content-card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Low Stock Alert</h5>
                                    <span class="badge bg-warning">{{ $lowStockProducts->count() }} items</span>
                                </div>
                                <div class="card-body">
                                    @if($lowStockProducts->count() > 0)
                                        <div class="list-group list-group-flush">
                                            @foreach($lowStockProducts as $product)
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ $product->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $product->sku }}</small>
                                                    </div>
                                                    <span class="badge bg-danger">{{ $product->stock_quantity }} left</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">All products are well stocked!</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Recent Transactions -->
                        <div class="col-md-6 mb-4">
                            <div class="card content-card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Recent Transactions</h5>
                                    <a href="{{ route('supermarket.sales') }}" class="btn btn-sm btn-outline-primary">View All</a>
                                </div>
                                <div class="card-body">
                                    @if($recentTransactions->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Receipt</th>
                                                        <th>Customer</th>
                                                        <th>Amount</th>
                                                        <th>Time</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recentTransactions as $sale)
                                                        <tr>
                                                            <td>
                                                                <small class="text-muted">{{ $sale->receipt_number }}</small>
                                                            </td>
                                                            <td>
                                                                {{ $sale->customer ? $sale->customer->full_name : 'Walk-in' }}
                                                            </td>
                                                            <td>
                                                                <strong>₦{{ number_format($sale->total_amount, 2) }}</strong>
                                                            </td>
                                                            <td>
                                                                <small>{{ $sale->sale_date->format('H:i') }}</small>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">No recent transactions</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card content-card">
                                <div class="card-header">
                                    <h5 class="mb-0">Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('supermarket.pos') }}" class="btn btn-success btn-lg w-100">
                                                <i class="fas fa-cash-register me-2"></i>
                                                New Sale
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('supermarket.products') }}" class="btn btn-primary btn-lg w-100">
                                                <i class="fas fa-plus me-2"></i>
                                                Add Product
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('supermarket.customers') }}" class="btn btn-info btn-lg w-100">
                                                <i class="fas fa-user-plus me-2"></i>
                                                New Customer
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('supermarket.reports') }}" class="btn btn-warning btn-lg w-100">
                                                <i class="fas fa-chart-bar me-2"></i>
                                                View Reports
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('staff.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
