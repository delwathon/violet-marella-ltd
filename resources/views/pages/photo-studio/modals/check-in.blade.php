<!-- Check-in Modal -->
<div class="modal fade" id="checkInModal" tabindex="-1" aria-labelledby="checkInModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="checkInModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Customer Check-in
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="checkInForm">
                @csrf
                <div class="modal-body">
                    <!-- Customer Search/Select -->
                    <div class="mb-3">
                        <label class="form-label">Search Existing Customer</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="customerSearch" placeholder="Search by name or phone...">
                            <button class="btn btn-outline-secondary" type="button" id="searchCustomerBtn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div id="customerSearchResults" class="list-group mt-2" style="display: none;"></div>
                        <small class="text-muted">Or enter new customer details below</small>
                    </div>

                    <hr>

                    <!-- Customer Details -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="customer_name" id="customerName" required placeholder="Enter full name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="customer_phone" id="customerPhone" required placeholder="+234 xxx xxx xxxx">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address (Optional)</label>
                        <input type="email" class="form-control" name="customer_email" id="customerEmail" placeholder="customer@email.com">
                    </div>

                    <hr>

                    <!-- Session Details -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Studio Room <span class="text-danger">*</span></label>
                                <select class="form-select" name="studio_id" id="studioSelect" required>
                                    <option value="">Select Studio</option>
                                    @foreach($studios->where('status', 'available') as $studio)
                                        <option value="{{ $studio->id }}" 
                                                data-hourly-rate="{{ $studio->hourly_rate }}"
                                                data-base-time="{{ $studio->rate->base_time ?? 30 }}"
                                                data-base-amount="{{ $studio->rate->base_amount ?? 2000 }}">
                                            {{ $studio->name }} - ₦{{ number_format($studio->rate->base_amount ?? 2000, 2) }}/{{ $studio->rate->base_time ?? 30 }} mins
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Expected Duration <span class="text-danger">*</span></label>
                                <select class="form-select" name="expected_duration" id="expectedDuration" required>
                                    <option value="30" selected>30 minutes</option>
                                    <option value="60">1 hour</option>
                                    <option value="90">1.5 hours</option>
                                    <option value="120">2 hours</option>
                                    <option value="180">3 hours</option>
                                    <option value="240">4 hours</option>
                                    <option value="custom">Custom...</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Custom Duration Input (hidden by default) -->
                    <div class="mb-3" id="customDurationDiv" style="display: none;">
                        <label class="form-label">Custom Duration (minutes)</label>
                        <input type="number" class="form-control" id="customDuration" min="1" placeholder="Enter duration in minutes">
                    </div>

                    <!-- Estimated Cost -->
                    <div class="alert alert-info mb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Estimated Cost:</strong>
                                <p class="mb-0 small" id="rateInfo">Select a studio to see pricing</p>
                            </div>
                            <div class="text-end">
                                <h4 class="mb-0" id="estimatedCost">₦0.00</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-2"></i>Check-in Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>