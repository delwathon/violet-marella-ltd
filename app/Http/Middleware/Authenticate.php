<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        // Use the specified guard if provided, or default to 'user'
        $guard = $guards[0] ?? 'user';

        if (!Auth::guard($guard)->check()) {
            return redirect()->route('login');
        }

        $user = Auth::guard($guard)->user();

        // Check if user is active
        if (!$user->is_active) {
            Auth::guard($guard)->logout();
            return redirect()
                ->route('login')
                ->with('error', 'Your account has been deactivated.');
        }

        // Optional: role check if a role is passed as a second guard argument
        if (isset($guards[1]) && !$user->hasRole($guards[1])) {
            abort(403, 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
