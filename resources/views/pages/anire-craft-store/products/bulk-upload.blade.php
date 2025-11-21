@extends('layouts.app')
@section('title', 'Bulk Product Upload')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="fas fa-file-upload text-primary"></i> Bulk Product Upload
                </h2>
                <p class="text-muted">Import multiple products at once using CSV file</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('anire-craft-store.products.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Products
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        
        @if(session('import_stats'))
            @php $stats = session('import_stats'); @endphp
            <div class="card border-info mb-4">
                <div class="card-body">
                    <h5 class="card-title text-info">
                        <i class="fas fa-chart-pie"></i> Import Summary
                    </h5>
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="p-3 border rounded">
                                <h3 class="text-success mb-0">{{ $stats['imported'] }}</h3>
                                <small class="text-muted">New Products</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded">
                                <h3 class="text-primary mb-0">{{ $stats['updated'] }}</h3>
                                <small class="text-muted">Updated</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded">
                                <h3 class="text-warning mb-0">{{ $stats['skipped'] }}</h3>
                                <small class="text-muted">Skipped</small>
                            </div>
                        </div>
                    </div>
                    
                    @if(!empty($stats['errors']))
                        <div class="mt-3">
                            <button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="collapse" data-bs-target="#errorDetails">
                                <i class="fas fa-exclamation-triangle"></i> View {{ count($stats['errors']) }} Error(s)
                            </button>
                            <div class="collapse mt-2" id="errorDetails">
                                <div class="alert alert-danger mb-0">
                                    <ul class="mb-0">
                                        @foreach($stats['errors'] as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Instructions Card -->
        <div class="col-lg-4">
            <div class="card border-primary mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Instructions
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="text-primary">Step 1: Download Template</h6>
                    <p class="small">Download the CSV template file with sample data and column headers.</p>
                    
                    <a href="{{ route('anire-craft-store.products.download-template') }}" class="btn btn-success w-100 mb-3">
                        <i class="fas fa-download"></i> Download CSV Template
                    </a>
                    
                    <hr>
                    
                    <h6 class="text-primary">Step 2: Fill in Product Data</h6>
                    <p class="small">Open the template in Excel or Google Sheets and fill in your product information:</p>
                    <ul class="small">
                        <li><strong>Required:</strong> name, category_name, price</li>
                        <li><strong>Optional:</strong> SKU (auto-generated if empty), barcode, stock, etc.</li>
                        <li>Follow the format in the sample rows</li>
                        <li>Do not modify the header row</li>
                    </ul>
                    
                    <hr>
                    
                    <h6 class="text-primary">Step 3: Upload CSV File</h6>
                    <p class="small">Save your file as CSV and upload it using the form.</p>
                    
                    <div class="alert alert-info small mb-0">
                        <i class="fas fa-lightbulb"></i> <strong>Tips:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Maximum file size: 5MB</li>
                            <li>Categories will be auto-created if they don't exist</li>
                            <li>SKUs will be auto-generated if left empty</li>
                            <li>Use "yes" or "no" for boolean fields</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- CSV Format Guide -->
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-table"></i> CSV Format Guide</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm small">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>name</td>
                                <td><span class="badge bg-danger">Required</span></td>
                            </tr>
                            <tr>
                                <td>category_name</td>
                                <td><span class="badge bg-danger">Required</span></td>
                            </tr>
                            <tr>
                                <td>price</td>
                                <td><span class="badge bg-danger">Required</span></td>
                            </tr>
                            <tr>
                                <td>sku</td>
                                <td><span class="badge bg-success">Optional</span></td>
                            </tr>
                            <tr>
                                <td>barcode</td>
                                <td><span class="badge bg-success">Optional</span></td>
                            </tr>
                            <tr>
                                <td>stock_quantity</td>
                                <td><span class="badge bg-success">Optional</span></td>
                            </tr>
                            <tr>
                                <td>track_stock</td>
                                <td><span class="badge bg-warning">yes/no</span></td>
                            </tr>
                            <tr>
                                <td>is_active</td>
                                <td><span class="badge bg-warning">yes/no</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Upload Form Card -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cloud-upload-alt"></i> Upload CSV File
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('anire-craft-store.products.import-csv') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf
                        
                        <!-- File Upload Area -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                Select CSV File <span class="text-danger">*</span>
                            </label>
                            <div class="upload-area border border-2 border-dashed rounded p-5 text-center" id="uploadArea">
                                <input type="file" name="csv_file" id="csvFile" class="d-none" accept=".csv,text/csv" required>
                                <div id="uploadPlaceholder">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Drag & Drop CSV file here</h5>
                                    <p class="text-muted mb-3">or</p>
                                    <button type="button" class="btn btn-primary" onclick="document.getElementById('csvFile').click()">
                                        <i class="fas fa-file-csv"></i> Browse Files
                                    </button>
                                    <p class="small text-muted mt-3">Maximum file size: 5MB</p>
                                </div>
                                <div id="fileInfo" class="d-none">
                                    <i class="fas fa-file-csv fa-3x text-success mb-3"></i>
                                    <h5 id="fileName" class="text-success"></h5>
                                    <p id="fileSize" class="text-muted"></p>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearFile()">
                                        <i class="fas fa-times"></i> Remove File
                                    </button>
                                </div>
                            </div>
                            @error('csv_file')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Import Options -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-cog"></i> Import Options
                                </h6>
                                
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="skip_duplicates" id="skipDuplicates" checked>
                                    <label class="form-check-label" for="skipDuplicates">
                                        <strong>Skip Duplicates</strong>
                                        <br>
                                        <small class="text-muted">Skip products that already exist (based on SKU or name)</small>
                                    </label>
                                </div>
                                
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="update_existing" id="updateExisting">
                                    <label class="form-check-label" for="updateExisting">
                                        <strong>Update Existing Products</strong>
                                        <br>
                                        <small class="text-muted">Update products if they already exist (overrides skip duplicates)</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- CSV Preview -->
                        <div id="csvPreview" class="d-none mb-4">
                            <h6 class="mb-3">
                                <i class="fas fa-eye"></i> CSV Preview
                                <small class="text-muted">(First 5 rows)</small>
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="previewTable">
                                    <thead class="table-light">
                                        <tr id="previewHeader"></tr>
                                    </thead>
                                    <tbody id="previewBody"></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg" id="importBtn" disabled>
                                <i class="fas fa-upload"></i> Import Products
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='{{ route('anire-craft-store.products.index') }}'">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent Imports (Optional - for future implementation) -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-history"></i> Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('anire-craft-store.products.export') }}" class="btn btn-outline-primary">
                            <i class="fas fa-download"></i> Export All Products to CSV
                        </a>
                        <a href="{{ route('anire-craft-store.products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list"></i> View All Products
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const csvFileInput = document.getElementById('csvFile');
const uploadArea = document.getElementById('uploadArea');
const uploadPlaceholder = document.getElementById('uploadPlaceholder');
const fileInfo = document.getElementById('fileInfo');
const importBtn = document.getElementById('importBtn');
const csvPreview = document.getElementById('csvPreview');

// Handle file selection
csvFileInput.addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        handleFile(this.files[0]);
    }
});

// Drag and drop handlers
uploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    e.stopPropagation();
    this.classList.add('border-primary', 'bg-light');
});

uploadArea.addEventListener('dragleave', function(e) {
    e.preventDefault();
    e.stopPropagation();
    this.classList.remove('border-primary', 'bg-light');
});

uploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    e.stopPropagation();
    this.classList.remove('border-primary', 'bg-light');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        // Check if it's a CSV file
        if (files[0].type === 'text/csv' || files[0].name.endsWith('.csv')) {
            csvFileInput.files = files;
            handleFile(files[0]);
        } else {
            alert('Please upload a CSV file');
        }
    }
});

function handleFile(file) {
    // Show file info
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('fileSize').textContent = formatFileSize(file.size);
    
    uploadPlaceholder.classList.add('d-none');
    fileInfo.classList.remove('d-none');
    importBtn.disabled = false;
    
    // Preview CSV
    previewCSV(file);
}

function clearFile() {
    csvFileInput.value = '';
    uploadPlaceholder.classList.remove('d-none');
    fileInfo.classList.add('d-none');
    csvPreview.classList.add('d-none');
    importBtn.disabled = true;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

function previewCSV(file) {
    const reader = new FileReader();
    
    reader.onload = function(e) {
        const text = e.target.result;
        const lines = text.split('\n').filter(line => line.trim());
        
        if (lines.length === 0) {
            alert('CSV file is empty');
            clearFile();
            return;
        }
        
        // Parse CSV (simple parsing - doesn't handle quoted commas)
        const rows = lines.map(line => line.split(',').map(cell => cell.trim()));
        
        // Show header
        const header = rows[0];
        const headerRow = document.getElementById('previewHeader');
        headerRow.innerHTML = header.map(h => `<th class="small">${h}</th>`).join('');
        
        // Show first 5 data rows
        const previewBody = document.getElementById('previewBody');
        previewBody.innerHTML = '';
        
        const dataRows = rows.slice(1, 6);
        dataRows.forEach(row => {
            const tr = document.createElement('tr');
            tr.innerHTML = row.map(cell => `<td class="small">${cell || '<em class="text-muted">empty</em>'}</td>`).join('');
            previewBody.appendChild(tr);
        });
        
        csvPreview.classList.remove('d-none');
    };
    
    reader.readAsText(file);
}

// Handle checkbox logic
document.getElementById('updateExisting').addEventListener('change', function() {
    const skipDuplicates = document.getElementById('skipDuplicates');
    if (this.checked) {
        skipDuplicates.checked = false;
        skipDuplicates.disabled = true;
    } else {
        skipDuplicates.disabled = false;
    }
});

document.getElementById('skipDuplicates').addEventListener('change', function() {
    const updateExisting = document.getElementById('updateExisting');
    if (this.checked) {
        updateExisting.checked = false;
    }
});

// Form validation
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    if (!csvFileInput.files || csvFileInput.files.length === 0) {
        e.preventDefault();
        alert('Please select a CSV file');
        return false;
    }
    
    importBtn.disabled = true;
    importBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importing...';
});
</script>

<style>
.upload-area {
    cursor: pointer;
    transition: all 0.3s;
}

.upload-area:hover {
    background-color: #f8f9fa;
}

#previewTable {
    font-size: 0.85rem;
}

#previewTable th {
    background-color: #e9ecef;
    white-space: nowrap;
}

#previewTable td {
    max-width: 150px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>
@endpush
@endsection