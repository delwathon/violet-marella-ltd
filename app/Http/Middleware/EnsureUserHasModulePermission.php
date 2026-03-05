<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasModulePermission
{
    /**
     * @var array<int, string>
     */
    private array $manageKeywords = [
        'create',
        'store',
        'edit',
        'update',
        'destroy',
        'delete',
        'checkout',
        'check-in',
        'start-timer',
        'extend',
        'cancel',
        'payment',
        'process',
        'mark',
        'toggle',
        'duplicate',
        'reset',
        'clear-cache',
        'scan',
        'import',
        'adjust',
        'bulk',
        'add',
        'remove',
        'export',
        'maintenance',
        'activate',
        'deactivate',
        'quick-store',
    ];

    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = Auth::guard('user')->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $permission = $this->resolvePermission($request, $module);

        if (!$user->hasPermission($permission)) {
            abort(403, 'You do not have permission to perform this action.');
        }

        return $next($request);
    }

    private function resolvePermission(Request $request, string $module): string
    {
        return $this->requiresManagePermission($request)
            ? "{$module}.manage"
            : "{$module}.view";
    }

    private function requiresManagePermission(Request $request): bool
    {
        if (!in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return true;
        }

        $routeName = strtolower((string) optional($request->route())->getName());
        $routeUri = strtolower((string) optional($request->route())->uri());
        $source = "{$routeName} {$routeUri}";

        foreach ($this->manageKeywords as $keyword) {
            if (Str::contains($source, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
