<!-- Extend Session Modal -->
<div class="modal fade" id="extendSessionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-clock me-2"></i>Extend Session
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="extendSessionForm">
                @csrf
                <input type="hidden" id="extendSessionId">
                <div class="modal-body">
                    <div id="extendSessionDetails" class="mb-3">
                        <!-- Session details will be loaded here -->
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Additional Time</label>
                        <select class="form-select" id="additionalTime" name="additional_time">
                            <option value="15">15 minutes</option>
                            <option value="30" selected>30 minutes</option>
                            <option value="45">45 minutes</option>
                            <option value="60">1 hour</option>
                            <option value="90">1.5 hours</option>
                            <option value="120">2 hours</option>
                        </select>
                    </div>

                    <div class="alert alert-info mb-0">
                        <strong>New Expected Duration:</strong> <span id="newExpectedDuration">0 minutes</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-2"></i>Extend Session
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>