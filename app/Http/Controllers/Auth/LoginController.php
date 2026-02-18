<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:user')->except('logout');
        $this->middleware('auth:user')->only('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Handle login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $authSettings = Cache::get('auth_settings', []);
        $maxLoginAttempts = max(1, (int)($authSettings['max_login_attempts'] ?? 5));
        $lockoutMinutes = max(1, (int)($authSettings['lockout_duration'] ?? 30));

        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, $maxLoginAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => ['Too many login attempts. Try again in ' . ceil($seconds / 60) . ' minute(s).'],
            ]);
        }

        $user = User::where('email', $request->email)
                     ->where('is_active', true)
                     ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, $lockoutMinutes * 60);

            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        RateLimiter::clear($throttleKey);

        Auth::guard('user')->login($user, $request->boolean('remember'));
        $user->forceFill(['last_login_at' => now()])->save();

        $request->session()->regenerate();
        $request->session()->put('last_activity_at', now()->timestamp);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Handle staff logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('user')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
