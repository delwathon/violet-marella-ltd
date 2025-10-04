@extends('layouts.app')
@section('title', 'Gift Store - Violet Marella Limited')
@push('styles')
<link href="{{ asset('assets/css/gift-store.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">Gift Store Management</h1>
            <p class="page-subtitle">Manage your gift store inventory, sales, and customer orders</p>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="fas fa-plus me-2"></i>Add New Product
            </button>
        </div>
    </div>
</div>
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary"><i class="fas fa-boxes"></i></div>
            <div class="stat-info">
                <div class="stat-value">247</div>
                <div class="stat-label">Total Products</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-info">
                <div class="stat-value">12</div>
                <div class="stat-label">Low Stock Items</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-danger"><i class="fas fa-times-circle"></i></div>
            <div class="stat-info">
                <div class="stat-value">3</div>
                <div class="stat-label">Out of Stock</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-success"><i class="fas fa-dollar-sign"></i></div>
            <div class="stat-info">
                <div class="stat-value">₦450K</div>
                <div class="stat-label">Monthly Sales</div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Product Inventory</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-download me-1"></i>Export
                    </button>
                    <div class="input-group input-group-sm" style="width: 200px;">
                        <input type="text" class="form-control" placeholder="Search products...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th>Stock</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="product-image me-3">
                                            <i class="fas fa-gift text-danger"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Valentine's Gift Box</div>
                                            <small class="text-muted">Luxury gift packaging</small>
                                        </div>
                                    </div>
                                </td>
                                <td><code>VGB-001</code></td>
                                <td>Seasonal</td>
                                <td><span class="badge bg-warning">15</span></td>
                                <td>₦5,500</td>
                                <td><span class="badge bg-warning">Low Stock</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-success" title="Restock">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- ...other products... -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <nav aria-label="Product pagination">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled">
                            <span class="page-link">Previous</span>
                        </li>
                        <li class="page-item active">
                            <span class="page-link">1</span>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">2</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">3</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Product Categories</h6>
            </div>
            <div class="card-body">
                <div class="category-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Seasonal Items</span>
                        <span class="badge bg-primary">45</span>
                    </div>
                </div>
                <!-- ...other categories... -->
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Low Stock Alerts</h6>
                <span class="badge bg-warning">12</span>
            </div>
            <div class="card-body">
                <div class="alert-item">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">Valentine's Gift Box</div>
                            <small class="text-muted">Only 15 items left</small>
                        </div>
                    </div>
                </div>
                <!-- ...other alerts... -->
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus me-2"></i>Add Product
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-boxes me-2"></i>Bulk Restock
                    </button>
                    <button class="btn btn-outline-info">
                        <i class="fas fa-chart-bar me-2"></i>Inventory Report
                    </button>
                    <button class="btn btn-outline-warning">
                        <i class="fas fa-download me-2"></i>Export Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addProductForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Product Name</label>
                                <input type="text" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">SKU</label>
                                <input type="text" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" required>
                                    <option value="">Select Category</option>
                                    <option value="seasonal">Seasonal Items</option>
                                    <option value="cards">Greeting Cards</option>
                                    <option value="flowers">Flowers & Plants</option>
                                    <option value="toys">Toys & Games</option>
                                    <option value="accessories">Accessories</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Price (₦)</label>
                                <input type="number" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Initial Stock</label>
                                <input type="number" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Minimum Stock Level</label>
                                <input type="number" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Save Product</button>
            </div>
        </div>
    </div>
</div>
@endsection
