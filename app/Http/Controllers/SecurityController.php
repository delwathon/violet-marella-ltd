<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SecurityController extends Controller
{
    public function updatePasswordPolicy(Request $request): RedirectResponse
    {
        $request->validate([
            'min_length' => 'required|integer|min:6|max:32',
            'require_uppercase' => 'nullable|boolean',
            'require_lowercase' => 'nullable|boolean',
            'require_numbers' => 'nullable|boolean',
            'require_special' => 'nullable|boolean',
            'password_expiry' => 'required|integer|min:0',
            'password_history' => 'required|integer|min:0',
        ]);

        $settings = [
            'min_length' => (int) $request->min_length,
            'require_uppercase' => $request->boolean('require_uppercase'),
            'require_lowercase' => $request->boolean('require_lowercase'),
            'require_numbers' => $request->boolean('require_numbers'),
            'require_special' => $request->boolean('require_special'),
            'password_expiry' => (int) $request->password_expiry,
            'password_history' => (int) $request->password_history,
        ];

        Cache::put('password_policy', $settings, now()->addYears(1));

        return redirect()->route('users.security')
            ->with('success', 'Password policy updated successfully.');
    }

    public function updateAuthentication(Request $request): RedirectResponse
    {
        $request->validate([
            'enable_2fa' => 'nullable|boolean',
            'force_2fa_admins' => 'nullable|boolean',
            'session_timeout' => 'required|integer|min:5',
            'max_login_attempts' => 'required|integer|min:1',
            'lockout_duration' => 'required|integer|min:5',
            'enable_ip_whitelist' => 'nullable|boolean',
        ]);

        $settings = [
            'enable_2fa' => $request->boolean('enable_2fa'),
            'force_2fa_admins' => $request->boolean('force_2fa_admins'),
            'session_timeout' => (int) $request->session_timeout,
            'max_login_attempts' => (int) $request->max_login_attempts,
            'lockout_duration' => (int) $request->lockout_duration,
            'enable_ip_whitelist' => $request->boolean('enable_ip_whitelist'),
        ];

        Cache::put('auth_settings', $settings, now()->addYears(1));
        Cache::put('ip_whitelist_enabled', $settings['enable_ip_whitelist'], now()->addYears(1));

        return redirect()->route('users.security')
            ->with('success', 'Authentication settings updated successfully.');
    }

    public function updateAuditLog(Request $request): RedirectResponse
    {
        $request->validate([
            'log_retention' => 'required|integer|min:30',
            'log_logins' => 'nullable|boolean',
            'log_changes' => 'nullable|boolean',
            'log_deletions' => 'nullable|boolean',
        ]);

        $settings = [
            'log_retention' => (int) $request->log_retention,
            'log_logins' => $request->boolean('log_logins'),
            'log_changes' => $request->boolean('log_changes'),
            'log_deletions' => $request->boolean('log_deletions'),
        ];

        Cache::put('audit_log_settings', $settings, now()->addYears(1));

        return redirect()->route('users.security')
            ->with('success', 'Audit log settings updated successfully.');
    }

    public function addIpWhitelist(Request $request): RedirectResponse
    {
        $request->validate([
            'ip_address' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        if (!$this->isValidIpOrCidr($request->ip_address)) {
            return redirect()->route('users.security')
                ->with('error', 'Invalid IP address or CIDR format.');
        }

        $ip = trim($request->ip_address);
        $whitelist = Cache::get('ip_whitelist', []);

        foreach ($whitelist as $entry) {
            if (($entry['ip'] ?? null) === $ip) {
                return redirect()->route('users.security')
                    ->with('error', 'That IP is already whitelisted.');
            }
        }

        $whitelist[] = [
            'ip' => $ip,
            'description' => $request->description,
            'added_at' => now()->toDateTimeString(),
        ];

        Cache::put('ip_whitelist', $whitelist, now()->addYears(1));

        return redirect()->route('users.security')
            ->with('success', 'IP added to whitelist successfully.');
    }

    public function removeIpWhitelist(int $index): RedirectResponse
    {
        $whitelist = Cache::get('ip_whitelist', []);

        if (isset($whitelist[$index])) {
            unset($whitelist[$index]);
            Cache::put('ip_whitelist', array_values($whitelist), now()->addYears(1));
        }

        return redirect()->route('users.security')
            ->with('success', 'IP removed from whitelist successfully.');
    }

    public function addIpBlacklist(Request $request): RedirectResponse
    {
        $request->validate([
            'ip_address' => 'required|string|max:50',
            'reason' => 'nullable|string|max:255',
        ]);

        if (!$this->isValidIpOrCidr($request->ip_address)) {
            return redirect()->route('users.security')
                ->with('error', 'Invalid IP address or CIDR format.');
        }

        $ip = trim($request->ip_address);
        $blacklist = Cache::get('ip_blacklist', []);

        foreach ($blacklist as $entry) {
            if (($entry['ip'] ?? null) === $ip) {
                return redirect()->route('users.security')
                    ->with('error', 'That IP is already blocked.');
            }
        }

        $blacklist[] = [
            'ip' => $ip,
            'reason' => $request->reason,
            'blocked_at' => now()->toDateTimeString(),
            'blocked_by' => Auth::guard('user')->id(),
        ];

        Cache::put('ip_blacklist', $blacklist, now()->addYears(1));

        return redirect()->route('users.security')
            ->with('success', 'IP blocked successfully.');
    }

    public function removeIpBlacklist(int $index): RedirectResponse
    {
        $blacklist = Cache::get('ip_blacklist', []);

        if (isset($blacklist[$index])) {
            unset($blacklist[$index]);
            Cache::put('ip_blacklist', array_values($blacklist), now()->addYears(1));
        }

        return redirect()->route('users.security')
            ->with('success', 'IP unblocked successfully.');
    }

    private function isValidIpOrCidr(string $value): bool
    {
        $value = trim($value);

        if (filter_var($value, FILTER_VALIDATE_IP) !== false) {
            return true;
        }

        if (!str_contains($value, '/')) {
            return false;
        }

        [$ip, $mask] = array_pad(explode('/', $value, 2), 2, null);

        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return false;
        }

        if (!is_numeric($mask)) {
            return false;
        }

        $maskInt = (int) $mask;

        return $maskInt >= 0 && $maskInt <= 32;
    }
}
