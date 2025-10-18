<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StaffAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role = null): Response
    {
        if (!Auth::guard('user')->check()) {
            return redirect()->route('login');
        }

        $user = Auth::guard('user')->user();

        // Check if user is active
        if (!$user->is_active) {
            Auth::guard('user')->logout();
            return redirect()->route('login')->with('error', 'Your account has been deactivated.');
        }

        // Check role if specified
        if ($role && !$user->hasRole($role)) {
            abort(403, 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
