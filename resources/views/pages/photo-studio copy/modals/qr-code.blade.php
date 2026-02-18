<!-- QR Code Modal -->
<div class="modal fade" id="qrCodeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-qrcode me-2"></i>Session QR Code
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qrCodeDetails" class="mb-4">
                    <!-- Session details will be loaded here -->
                </div>

                <div class="qr-code-display mb-3">
                    <div id="qrCodeImage" class="p-4 bg-light rounded">
                        <!-- QR Code will be generated here -->
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <small>
                        <i class="fas fa-info-circle me-1"></i>
                        Scan this QR code for quick checkout
                    </small>
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-primary" onclick="printQRCode()">
                        <i class="fas fa-print me-2"></i>Print QR Code
                    </button>
                    <button class="btn btn-outline-secondary" onclick="downloadQRCode()">
                        <i class="fas fa-download me-2"></i>Download QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Scanner Modal -->
<div class="modal fade" id="qrScannerModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-camera me-2"></i>Scan QR Code
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="scanner-container mb-3">
                    <div id="qrScannerPreview" class="bg-dark rounded" style="height: 400px; position: relative;">
                        <div class="position-absolute top-50 start-50 translate-middle text-white">
                            <i class="fas fa-camera fa-3x mb-2"></i>
                            <p>Position QR code in front of camera</p>
                        </div>
                    </div>
                </div>

                <div class="text-center mb-3">
                    <button class="btn btn-primary" id="startScanBtn">
                        <i class="fas fa-camera me-2"></i>Start Camera
                    </button>
                    <button class="btn btn-danger" id="stopScanBtn" style="display: none;">
                        <i class="fas fa-stop me-2"></i>Stop Camera
                    </button>
                </div>

                <div class="alert alert-secondary">
                    <strong>Manual Entry:</strong>
                    <div class="input-group mt-2">
                        <input type="text" class="form-control" id="manualQRInput" placeholder="Enter QR code manually">
                        <button class="btn btn-outline-primary" onclick="searchByQRCode()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div id="scanResult" style="display: none;">
                    <!-- Scan result will be displayed here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>