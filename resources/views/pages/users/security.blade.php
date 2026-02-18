@extends('layouts.app')
@section('title', 'Security Settings')

@section('content')
<div class="content-area">
    <div class="mb-4">
        <h1 class="h3 mb-1 fw-bold">Security Settings</h1>
        <p class="text-muted mb-0">Configure authentication and network access policies.</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3"><h5 class="mb-0">Password Policy</h5></div>
                <div class="card-body">
                    <form action="{{ route('users.security.password-policy') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Minimum Password Length</label>
                            <input type="number" name="min_length" class="form-control" min="6" max="32" value="{{ $passwordPolicy['min_length'] }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block">Requirements</label>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="require_uppercase" id="require_uppercase" {{ $passwordPolicy['require_uppercase'] ? 'checked' : '' }}><label class="form-check-label" for="require_uppercase">Uppercase letters</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="require_lowercase" id="require_lowercase" {{ $passwordPolicy['require_lowercase'] ? 'checked' : '' }}><label class="form-check-label" for="require_lowercase">Lowercase letters</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="require_numbers" id="require_numbers" {{ $passwordPolicy['require_numbers'] ? 'checked' : '' }}><label class="form-check-label" for="require_numbers">Numbers</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="require_special" id="require_special" {{ $passwordPolicy['require_special'] ? 'checked' : '' }}><label class="form-check-label" for="require_special">Special characters</label></div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Password Expiry (days)</label>
                                <input type="number" name="password_expiry" class="form-control" min="0" value="{{ $passwordPolicy['password_expiry'] }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password History</label>
                                <input type="number" name="password_history" class="form-control" min="0" value="{{ $passwordPolicy['password_history'] }}" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Password Policy</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3"><h5 class="mb-0">Authentication</h5></div>
                <div class="card-body">
                    <form action="{{ route('users.security.authentication') }}" method="POST">
                        @csrf
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="enable_2fa" name="enable_2fa" {{ $authSettings['enable_2fa'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="enable_2fa">Enable 2FA</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="force_2fa_admins" name="force_2fa_admins" {{ $authSettings['force_2fa_admins'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="force_2fa_admins">Force 2FA for admins</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="enable_ip_whitelist" name="enable_ip_whitelist" {{ $authSettings['enable_ip_whitelist'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="enable_ip_whitelist">Enforce IP whitelist</label>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Session Timeout (min)</label>
                                <input type="number" name="session_timeout" min="5" class="form-control" value="{{ $authSettings['session_timeout'] }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Max Login Attempts</label>
                                <input type="number" name="max_login_attempts" min="1" class="form-control" value="{{ $authSettings['max_login_attempts'] }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Lockout (min)</label>
                                <input type="number" name="lockout_duration" min="5" class="form-control" value="{{ $authSettings['lockout_duration'] }}" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Authentication Settings</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">IP Whitelist</h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addIpModal">Add IP</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr><th>IP</th><th>Description</th><th>Added</th><th class="text-end pe-3">Action</th></tr>
                            </thead>
                            <tbody>
                                @forelse($whitelist as $index => $entry)
                                    <tr>
                                        <td><code>{{ $entry['ip'] }}</code></td>
                                        <td>{{ $entry['description'] ?? '-' }}</td>
                                        <td>{{ $entry['added_at'] ?? '-' }}</td>
                                        <td class="text-end pe-3">
                                            <form action="{{ route('security.ip-whitelist.remove', $index) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" type="submit">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-3">No whitelisted IPs</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Blocked IPs</h5>
                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#blockIpModal">Block IP</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr><th>IP</th><th>Reason</th><th>Blocked</th><th class="text-end pe-3">Action</th></tr>
                            </thead>
                            <tbody>
                                @forelse($blacklist as $index => $entry)
                                    <tr>
                                        <td><code>{{ $entry['ip'] }}</code></td>
                                        <td>{{ $entry['reason'] ?? '-' }}</td>
                                        <td>{{ $entry['blocked_at'] ?? '-' }}</td>
                                        <td class="text-end pe-3">
                                            <form action="{{ route('security.ip-blacklist.remove', $index) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-success" type="submit">Unblock</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-3">No blocked IPs</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3"><h5 class="mb-0">Audit Logging</h5></div>
                <div class="card-body">
                    <form action="{{ route('users.security.audit-log') }}" method="POST" class="row g-3 align-items-end">
                        @csrf
                        <div class="col-md-3">
                            <label class="form-label">Retention (days)</label>
                            <input type="number" name="log_retention" class="form-control" min="30" value="{{ $auditLogSettings['log_retention'] }}" required>
                        </div>
                        <div class="col-md-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="log_logins" id="log_logins" {{ $auditLogSettings['log_logins'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="log_logins">Log logins</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="log_changes" id="log_changes" {{ $auditLogSettings['log_changes'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="log_changes">Log changes</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="log_deletions" id="log_deletions" {{ $auditLogSettings['log_deletions'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="log_deletions">Log deletions</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary" type="submit">Save Audit Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addIpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add IP to Whitelist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('security.ip-whitelist.add') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">IP Address or CIDR</label>
                        <input type="text" name="ip_address" class="form-control" placeholder="192.168.1.10 or 10.0.0.0/24" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" placeholder="Office network">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add IP</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="blockIpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Block IP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('security.ip-blacklist.add') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">IP Address or CIDR</label>
                        <input type="text" name="ip_address" class="form-control" placeholder="203.0.113.45" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Reason</label>
                        <input type="text" name="reason" class="form-control" placeholder="Multiple failed logins">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Block IP</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
