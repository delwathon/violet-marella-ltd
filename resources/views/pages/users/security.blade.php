@extends('layouts.app')
@section('title', 'Security Settings')

@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h3 mb-1 fw-bold">Security Settings</h1>
                <p class="text-muted mb-0">Configure system-wide security policies and authentication settings</p>
            </div>
        </div>
    </div>

    <!-- Security Overview -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 text-success rounded p-3 me-3">
                            <i class="fas fa-shield-alt fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Security Score</div>
                            <h3 class="mb-0 fw-bold text-success">95%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded p-3 me-3">
                            <i class="fas fa-key fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small">2FA Enabled Users</div>
                            <h3 class="mb-0 fw-bold">38</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 text-warning rounded p-3 me-3">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Failed Logins (24h)</div>
                            <h3 class="mb-0 fw-bold">12</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 text-danger rounded p-3 me-3">
                            <i class="fas fa-ban fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Blocked IPs</div>
                            <h3 class="mb-0 fw-bold">5</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Password Policies -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Password Policies</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.security.password-policy') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="min_length" class="form-label">Minimum Password Length</label>
                            <input type="number" class="form-control" id="min_length" name="min_length" value="8" min="6" max="32">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password Requirements</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="require_uppercase" name="require_uppercase" checked>
                                <label class="form-check-label" for="require_uppercase">
                                    Require uppercase letters
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="require_lowercase" name="require_lowercase" checked>
                                <label class="form-check-label" for="require_lowercase">
                                    Require lowercase letters
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="require_numbers" name="require_numbers" checked>
                                <label class="form-check-label" for="require_numbers">
                                    Require numbers
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="require_special" name="require_special">
                                <label class="form-check-label" for="require_special">
                                    Require special characters
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password_expiry" class="form-label">Password Expiry (days)</label>
                            <input type="number" class="form-control" id="password_expiry" name="password_expiry" value="90" min="0">
                            <div class="form-text">Set to 0 for no expiry</div>
                        </div>
                        <div class="mb-3">
                            <label for="password_history" class="form-label">Password History</label>
                            <input type="number" class="form-control" id="password_history" name="password_history" value="5" min="0">
                            <div class="form-text">Prevent reuse of last N passwords</div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Password Policy
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Authentication Settings -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>Authentication Settings</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.security.authentication') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="enable_2fa" name="enable_2fa" checked>
                                <label class="form-check-label" for="enable_2fa">
                                    <strong>Enable Two-Factor Authentication</strong>
                                </label>
                            </div>
                            <div class="form-text">Require 2FA for all users</div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="force_2fa_admins" name="force_2fa_admins" checked>
                                <label class="form-check-label" for="force_2fa_admins">
                                    <strong>Force 2FA for Administrators</strong>
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="session_timeout" class="form-label">Session Timeout (minutes)</label>
                            <input type="number" class="form-control" id="session_timeout" name="session_timeout" value="120" min="5">
                        </div>
                        <div class="mb-3">
                            <label for="max_login_attempts" class="form-label">Maximum Login Attempts</label>
                            <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts" value="5" min="1">
                            <div class="form-text">Account locked after this many failed attempts</div>
                        </div>
                        <div class="mb-3">
                            <label for="lockout_duration" class="form-label">Lockout Duration (minutes)</label>
                            <input type="number" class="form-control" id="lockout_duration" name="lockout_duration" value="30" min="5">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Authentication Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- IP Whitelist -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-network-wired me-2"></i>IP Whitelist</h5>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addIpModal">
                            <i class="fas fa-plus me-1"></i>Add IP
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Restrict access to specific IP addresses</p>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="enable_ip_whitelist" name="enable_ip_whitelist">
                        <label class="form-check-label" for="enable_ip_whitelist">
                            Enable IP Whitelist
                        </label>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>IP Address</th>
                                    <th>Description</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>192.168.1.100</code></td>
                                    <td>Office Network</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><code>10.0.0.0/24</code></td>
                                    <td>VPN Network</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Blocked IPs -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-ban me-2"></i>Blocked IPs</h5>
                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#blockIpModal">
                            <i class="fas fa-ban me-1"></i>Block IP
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Prevent access from specific IP addresses</p>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>IP Address</th>
                                    <th>Reason</th>
                                    <th>Blocked On</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>203.0.113.45</code></td>
                                    <td>Multiple failed logins</td>
                                    <td>2 hours ago</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-unlock"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><code>198.51.100.78</code></td>
                                    <td>Suspicious activity</td>
                                    <td>1 day ago</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-unlock"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Audit Log Settings -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Audit Log Settings</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.security.audit-log') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="log_retention" class="form-label">Log Retention Period (days)</label>
                                <input type="number" class="form-control" id="log_retention" name="log_retention" value="365" min="30">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Events to Log</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="log_logins" name="log_logins" checked>
                                    <label class="form-check-label" for="log_logins">Login attempts</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="log_changes" name="log_changes" checked>
                                    <label class="form-check-label" for="log_changes">Data modifications</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="log_deletions" name="log_deletions" checked>
                                    <label class="form-check-label" for="log_deletions">Data deletions</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Audit Settings
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add IP Modal -->
<div class="modal fade" id="addIpModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add IP to Whitelist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="ip_address" class="form-label">IP Address</label>
                        <input type="text" class="form-control" id="ip_address" placeholder="e.g., 192.168.1.1 or 10.0.0.0/24">
                    </div>
                    <div class="mb-3">
                        <label for="ip_description" class="form-label">Description</label>
                        <input type="text" class="form-control" id="ip_description" placeholder="e.g., Office Network">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add to Whitelist</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Block IP Modal -->
<div class="modal fade" id="blockIpModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Block IP Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="block_ip_address" class="form-label">IP Address</label>
                        <input type="text" class="form-control" id="block_ip_address" placeholder="e.g., 192.168.1.1">
                    </div>
                    <div class="mb-3">
                        <label for="block_reason" class="form-label">Reason</label>
                        <textarea class="form-control" id="block_reason" rows="2" placeholder="Reason for blocking this IP"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Block IP</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection