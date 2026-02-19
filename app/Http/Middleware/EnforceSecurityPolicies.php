<?php

namespace App\Http\Middleware;

use App\Support\SecuritySettings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnforceSecurityPolicies
{
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        $blacklist = SecuritySettings::ipBlacklist();
        if ($this->matchesIpList($ip, $blacklist, 'ip')) {
            abort(403, 'Access denied from this IP address.');
        }

        $authSettings = SecuritySettings::authSettings();
        $whitelistEnabled = (bool) ($authSettings['enable_ip_whitelist'] ?? false)
            || (bool) Cache::get('ip_whitelist_enabled', false);
        $whitelist = SecuritySettings::ipWhitelist();
        if ($whitelistEnabled && $whitelist !== [] && !$this->matchesIpList($ip, $whitelist, 'ip')) {
            abort(403, 'Your IP address is not authorized.');
        }

        if (Auth::guard('user')->check()) {
            $timeoutMinutes = max(5, (int) ($authSettings['session_timeout'] ?? 120));

            $now = now()->timestamp;
            $lastActivity = (int) $request->session()->get('last_activity_at', $now);

            if (($now - $lastActivity) > ($timeoutMinutes * 60)) {
                Auth::guard('user')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Session expired due to inactivity. Please login again.');
            }

            $request->session()->put('last_activity_at', $now);
        }

        return $next($request);
    }

    private function matchesIpList(string $ip, array $list, string $key): bool
    {
        foreach ($list as $entry) {
            if (!is_array($entry) || !isset($entry[$key])) {
                continue;
            }

            $candidate = (string) $entry[$key];
            if ($candidate === '') {
                continue;
            }

            if ($this->ipMatches($ip, $candidate)) {
                return true;
            }
        }

        return false;
    }

    private function ipMatches(string $ip, string $candidate): bool
    {
        if ($candidate === $ip) {
            return true;
        }

        if (str_contains($candidate, '/')) {
            return $this->ipMatchesCidr($ip, $candidate);
        }

        return false;
    }

    private function ipMatchesCidr(string $ip, string $cidr): bool
    {
        [$subnet, $mask] = array_pad(explode('/', $cidr, 2), 2, null);

        if (!is_numeric($mask)) {
            return false;
        }

        $maskInt = (int) $mask;

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);

        if ($ipLong === false || $subnetLong === false) {
            return false;
        }

        if ($maskInt < 0 || $maskInt > 32) {
            return false;
        }

        $maskLong = $maskInt === 0 ? 0 : (-1 << (32 - $maskInt));
        $subnetLong &= $maskLong;

        return ($ipLong & $maskLong) === $subnetLong;
    }
}
