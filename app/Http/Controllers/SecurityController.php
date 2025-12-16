<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

class SecurityController extends Controller
{
    /**
     * Update password policy settings
     */
    public function updatePasswordPolicy(Request $request)
    {
        $request->validate([
            'min_length' => 'required|integer|min:6|max:32',
            'require_uppercase' => 'nullable|boolean',
            'require_lowercase' => 'nullable|boolean',
            'require_numbers' => 'nullable|boolean',
            'require_special' => 'nullable|boolean',
            'password_expiry' => 'required|integer|min:0',
            'password_history' => 'required|integer|min:0'
        ]);
        
        // Store in cache or database
        $settings = [
            'min_length' => $request->min_length,
            'require_uppercase' => $request->has('require_uppercase'),
            'require_lowercase' => $request->has('require_lowercase'),
            'require_numbers' => $request->has('require_numbers'),
            'require_special' => $request->has('require_special'),
            'password_expiry' => $request->password_expiry,
            'password_history' => $request->password_history
        ];
        
        Cache::put('password_policy', $settings, now()->addYears(1));
        
        return redirect()->route('users.security')
            ->with('success', 'Password policy updated successfully!');
    }

    /**
     * Update authentication settings
     */
    public function updateAuthentication(Request $request)
    {
        $request->validate([
            'enable_2fa' => 'nullable|boolean',
            'force_2fa_admins' => 'nullable|boolean',
            'session_timeout' => 'required|integer|min:5',
            'max_login_attempts' => 'required|integer|min:1',
            'lockout_duration' => 'required|integer|min:5'
        ]);
        
        $settings = [
            'enable_2fa' => $request->has('enable_2fa'),
            'force_2fa_admins' => $request->has('force_2fa_admins'),
            'session_timeout' => $request->session_timeout,
            'max_login_attempts' => $request->max_login_attempts,
            'lockout_duration' => $request->lockout_duration
        ];
        
        Cache::put('auth_settings', $settings, now()->addYears(1));
        
        return redirect()->route('users.security')
            ->with('success', 'Authentication settings updated successfully!');
    }

    /**
     * Update audit log settings
     */
    public function updateAuditLog(Request $request)
    {
        $request->validate([
            'log_retention' => 'required|integer|min:30',
            'log_logins' => 'nullable|boolean',
            'log_changes' => 'nullable|boolean',
            'log_deletions' => 'nullable|boolean'
        ]);
        
        $settings = [
            'log_retention' => $request->log_retention,
            'log_logins' => $request->has('log_logins'),
            'log_changes' => $request->has('log_changes'),
            'log_deletions' => $request->has('log_deletions')
        ];
        
        Cache::put('audit_log_settings', $settings, now()->addYears(1));
        
        return redirect()->route('users.security')
            ->with('success', 'Audit log settings updated successfully!');
    }

    /**
     * Add IP to whitelist
     */
    public function addIpWhitelist(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|string',
            'description' => 'nullable|string|max:255'
        ]);
        
        $whitelist = Cache::get('ip_whitelist', []);
        $whitelist[] = [
            'ip' => $request->ip_address,
            'description' => $request->description,
            'added_at' => now()->toDateTimeString()
        ];
        
        Cache::put('ip_whitelist', $whitelist, now()->addYears(1));
        
        return redirect()->route('users.security')
            ->with('success', 'IP added to whitelist successfully!');
    }

    /**
     * Remove IP from whitelist
     */
    public function removeIpWhitelist($index)
    {
        $whitelist = Cache::get('ip_whitelist', []);
        
        if (isset($whitelist[$index])) {
            unset($whitelist[$index]);
            $whitelist = array_values($whitelist);
            Cache::put('ip_whitelist', $whitelist, now()->addYears(1));
        }
        
        return redirect()->route('users.security')
            ->with('success', 'IP removed from whitelist successfully!');
    }

    /**
     * Add IP to blacklist
     */
    public function addIpBlacklist(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|string',
            'reason' => 'nullable|string'
        ]);
        
        $blacklist = Cache::get('ip_blacklist', []);
        $blacklist[] = [
            'ip' => $request->ip_address,
            'reason' => $request->reason,
            'blocked_at' => now()->toDateTimeString(),
            'blocked_by' => Auth::guard('user')->user()->id
        ];
        
        Cache::put('ip_blacklist', $blacklist, now()->addYears(1));
        
        return redirect()->route('users.security')
            ->with('success', 'IP blocked successfully!');
    }

    /**
     * Remove IP from blacklist (unblock)
     */
    public function removeIpBlacklist($index)
    {
        $blacklist = Cache::get('ip_blacklist', []);
        
        if (isset($blacklist[$index])) {
            unset($blacklist[$index]);
            $blacklist = array_values($blacklist);
            Cache::put('ip_blacklist', $blacklist, now()->addYears(1));
        }
        
        return redirect()->route('users.security')
            ->with('success', 'IP unblocked successfully!');
    }
}