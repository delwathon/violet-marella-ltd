<!-- Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="checkoutModalLabel">
                    <i class="fas fa-sign-out-alt me-2"></i>Checkout Customer
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="checkoutForm">
                @csrf
                <input type="hidden" name="session_id" id="checkoutSessionId">
                <div class="modal-body">
                    <!-- Loading State -->
                    <div id="checkoutLoading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading session details...</p>
                    </div>

                    <!-- Session Details -->
                    <div id="checkoutDetails" class="mb-4" style="display: none;" data-total-amount="0">
                        <div class="card border-primary mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2">Customer</h6>
                                        <p class="mb-1 fw-bold" id="checkoutCustomerName">-</p>
                                        <p class="mb-0 small text-muted" id="checkoutCustomerPhone">-</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2">Studio</h6>
                                        <p class="mb-0 fw-bold" id="checkoutStudioName">-</p>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2">Check-in Time</h6>
                                        <p class="mb-0" id="checkoutCheckInTime">-</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2">Duration</h6>
                                        <p class="mb-0" id="checkoutDuration">-</p>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2">Base Amount</h6>
                                        <p class="mb-0 fw-bold" id="checkoutBaseAmount">₦0.00</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2">Overtime Charge</h6>
                                        <p class="mb-0 fw-bold text-warning" id="checkoutOvertimeCharge">₦0.00</p>
                                    </div>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded">
                                    <h5 class="mb-0">Total Amount:</h5>
                                    <h4 class="mb-0 text-primary" id="checkoutTotalAmount">₦0.00</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div id="checkoutPaymentSection" style="display: none;">
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title mb-3">Payment Information</h6>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                            <select class="form-select" name="payment_method" id="paymentMethod" required>
                                                <option value="">Select payment method</option>
                                                <option value="cash">Cash</option>
                                                <option value="card">Card</option>
                                                <option value="transfer">Bank Transfer</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Discount Amount (Optional)</label>
                                            <div class="input-group">
                                                <span class="input-group-text">₦</span>
                                                <input type="number" class="form-control" name="discount_amount" id="discountAmount" min="0" step="0.01" placeholder="0.00">
                                            </div>
                                            <small class="text-muted">Leave empty for no discount</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Final Amount Display -->
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded">
                                            <div>
                                                <h6 class="mb-0">Final Amount to Collect:</h6>
                                                <small class="text-muted">After discount</small>
                                            </div>
                                            <h3 class="mb-0 text-success" id="finalAmount">₦0.00</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Notes -->
                        <div class="mb-3">
                            <label class="form-label">Payment Notes (Optional)</label>
                            <textarea class="form-control" name="payment_notes" id="paymentNotes" rows="2" placeholder="Any additional notes about this payment..."></textarea>
                        </div>

                        <!-- Options -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="printReceipt" checked>
                            <label class="form-check-label" for="printReceipt">
                                Print receipt after checkout
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="sendEmail">
                            <label class="form-check-label" for="sendEmail">
                                Send receipt via email
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success" id="checkoutSubmitBtn" disabled>
                        <i class="fas fa-check me-2"></i>Complete Checkout
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>