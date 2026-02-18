<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = Auth::guard('user')->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $allowedRoles = array_filter(array_map('trim', explode(',', $roles)));

        if ($allowedRoles === []) {
            return $next($request);
        }

        if (!in_array($user->role, $allowedRoles, true)) {
            abort(403, 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
