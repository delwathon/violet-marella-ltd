<?php

namespace App\Http\Middleware;

use App\Support\AccessControl;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasBusinessAccess
{
    public function handle(Request $request, Closure $next, string $business): Response
    {
        $user = Auth::guard('user')->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $businessSlug = AccessControl::resolveBusinessSlug($business);

        if ($businessSlug === null || !$user->hasBusinessAccess($businessSlug)) {
            abort(403, 'You do not have access to this business.');
        }

        return $next($request);
    }
}
