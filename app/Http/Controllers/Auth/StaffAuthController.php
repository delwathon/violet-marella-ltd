<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StaffAuthController extends Controller
{
    /**
     * Show the staff login form.
     */
    public function showLoginForm()
    {
        return view('auth.staff-login');
    }

    /**
     * Handle staff login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $staff = Staff::where('email', $request->email)
                     ->where('is_active', true)
                     ->first();

        if (!$staff || !Hash::check($request->password, $staff->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        Auth::guard('staff')->login($staff, $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->intended(route('supermarket.dashboard'));
    }

    /**
     * Handle staff logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('staff.login');
    }

    /**
     * Show staff dashboard.
     */
    public function dashboard()
    {
        $staff = Auth::guard('staff')->user();

        // Get today's sales data
        $todaySales = \App\Models\Sale::whereDate('sale_date', today())
                                    ->where('staff_id', $staff->id)
                                    ->sum('total_amount');

        $todayTransactions = \App\Models\Sale::whereDate('sale_date', today())
                                          ->where('staff_id', $staff->id)
                                          ->count();

        // Get low stock products
        $lowStockProducts = \App\Models\Product::lowStock()->take(5)->get();

        // Get recent transactions
        $recentTransactions = \App\Models\Sale::with(['customer', 'saleItems.product'])
                                            ->where('staff_id', $staff->id)
                                            ->latest()
                                            ->take(10)
                                            ->get();

        return view('supermarket.dashboard', compact(
            'todaySales',
            'todayTransactions',
            'lowStockProducts',
            'recentTransactions'
        ));
    }
}
