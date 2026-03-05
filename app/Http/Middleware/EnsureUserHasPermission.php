<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPermission
{
    public function handle(Request $request, Closure $next, string $permissions, string $mode = 'any'): Response
    {
        $user = Auth::guard('user')->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $requiredPermissions = collect(explode(',', $permissions))
            ->map(fn (string $permission) => trim($permission))
            ->filter()
            ->values();

        if ($requiredPermissions->isEmpty()) {
            return $next($request);
        }

        $isAllowed = strtolower($mode) === 'all'
            ? $requiredPermissions->every(fn (string $permission) => $user->hasPermission($permission))
            : $requiredPermissions->contains(fn (string $permission) => $user->hasPermission($permission));

        if (!$isAllowed) {
            abort(403, 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
