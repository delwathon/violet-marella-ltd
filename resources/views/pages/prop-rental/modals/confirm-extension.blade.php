<!-- Extension Confirmation Modal -->
<div class="modal fade" id="confirmExtensionModal" tabindex="-1" aria-labelledby="confirmExtensionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="confirmExtensionModalLabel">
                    <i class="fas fa-check-circle me-2"></i>Confirm Rental Extension
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-info-circle fa-2x me-3 mt-1"></i>
                        <div>
                            <h6 class="alert-heading">Extension Summary</h6>
                            <p class="mb-0">Please review the extension details before confirming:</p>
                        </div>
                    </div>
                </div>

                <div class="card border-0 bg-light mb-3">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6 text-muted">Customer:</div>
                            <div class="col-6 fw-bold text-end">{{ $rental->customer->name }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 text-muted">Prop:</div>
                            <div class="col-6 fw-bold text-end">{{ $rental->prop->name }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 text-muted">Current End Date:</div>
                            <div class="col-6 fw-bold text-end text-danger">{{ $rental->end_date->format('d M Y') }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-2">
                            <div class="col-6 text-muted">Additional Days:</div>
                            <div class="col-6 fw-bold text-end text-primary" id="modalAdditionalDays">-</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 text-muted">New End Date:</div>
                            <div class="col-6 fw-bold text-end text-success" id="modalNewEndDate">-</div>
                        </div>
                    </div>
                </div>

                <div class="card border-primary mb-3">
                    <div class="card-header bg-primary text-white py-2">
                        <strong>Payment Breakdown</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-7 text-muted">Previous Balance Due:</div>
                            <div class="col-5 text-end" id="modalPreviousBalance">{{ $rental->formatted_balance_due }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-7 text-muted">Extension Charge:</div>
                            <div class="col-5 text-end text-success" id="modalExtensionCharge">₦0.00</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-2">
                            <div class="col-7 fw-bold">Total Amount Due:</div>
                            <div class="col-5 text-end fw-bold text-primary" id="modalTotalDue">₦0.00</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-7 text-muted">Payment Now:</div>
                            <div class="col-5 text-end fw-bold text-success" id="modalPaymentNow">₦0.00</div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-7 fw-bold">Remaining Balance:</div>
                            <div class="col-5 text-end fw-bold" id="modalRemainingBalance">₦0.00</div>
                        </div>
                    </div>
                </div>

                <div id="modalBalanceAlert" class="alert" style="display: none;">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <span id="modalBalanceAlertText"></span>
                </div>

                <p class="text-muted small mb-0">
                    <i class="fas fa-shield-alt me-1"></i>
                    By confirming, you acknowledge that the payment has been collected and the rental period will be extended.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-success" id="finalConfirmBtn" onclick="submitExtensionForm()">
                    <i class="fas fa-check me-2"></i>Confirm Extension
                </button>
            </div>
        </div>
    </div>
</div>