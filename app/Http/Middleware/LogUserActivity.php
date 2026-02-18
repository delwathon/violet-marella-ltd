<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $auditSettings = Cache::get('audit_log_settings', [
            'log_retention' => 365,
            'log_logins' => true,
            'log_changes' => true,
            'log_deletions' => true,
        ]);

        $routeName = $request->route()?->getName();
        $method = strtoupper($request->method());

        $action = null;
        $module = $this->resolveModule($request);
        $shouldLog = false;

        $isLoginRoute = in_array($routeName, ['login', 'logout'], true) || str_starts_with($request->path(), 'auth/');

        if ($isLoginRoute) {
            $shouldLog = (bool) ($auditSettings['log_logins'] ?? true);
            $action = $routeName === 'logout' ? 'logout' : 'login';
            $module = 'auth';
        } elseif ($method === 'DELETE') {
            $shouldLog = (bool) ($auditSettings['log_deletions'] ?? true);
            $action = 'delete';
        } elseif (in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
            $shouldLog = (bool) ($auditSettings['log_changes'] ?? true);
            $action = $method === 'POST' ? 'create' : 'update';
        }

        if (!$shouldLog) {
            return $response;
        }

        try {
            ActivityLog::create([
                'user_id' => Auth::guard('user')->id(),
                'module' => $module,
                'action' => $action,
                'method' => $method,
                'url' => $request->path(),
                'ip_address' => $request->ip(),
                'status_code' => $response->getStatusCode(),
                'metadata' => [
                    'route_name' => $routeName,
                    'user_agent' => substr((string) $request->userAgent(), 0, 255),
                ],
            ]);

            if (random_int(1, 100) === 1) {
                $retentionDays = max(30, (int) ($auditSettings['log_retention'] ?? 365));
                ActivityLog::where('created_at', '<', now()->subDays($retentionDays))->delete();
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $response;
    }

    private function resolveModule(Request $request): string
    {
        $segments = $request->segments();

        if (($segments[0] ?? null) !== 'app') {
            return $segments[0] ?? 'system';
        }

        return $segments[1] ?? 'dashboard';
    }
}
