<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of users with filters
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } else {
                $query->where('is_active', false);
            }
        }
        
        // Pagination
        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get departments for filter (empty for now)
        $departments = collect([]);
        
        // Calculate statistics
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $pendingUsers = User::where('is_active', false)->count();
        $adminUsers = User::where('role', 'admin')->count();
        
        // Get current authenticated user
        $user = Auth::guard('user')->user();
        
        return view('pages.users.index', compact(
            'users',
            'user',
            'departments',
            'totalUsers',
            'activeUsers',
            'pendingUsers',
            'adminUsers'
        ));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $user = Auth::guard('user')->user();
        $departments = collect([]);
        $roles = ['admin', 'manager', 'cashier', 'stock_keeper'];
        
        return view('pages.users.create', compact('user', 'departments', 'roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => 'required|in:admin,manager,cashier,stock_keeper',
            'hire_date' => 'required|date',
            'hourly_rate' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'profile_photo' => 'nullable|image|max:2048',
            'permissions' => 'nullable|array'
        ]);
        
        // Hash password
        $validated['password'] = Hash::make($validated['password']);
        
        // Set is_active
        $validated['is_active'] = $request->has('is_active') || ($request->status === 'active');
        
        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $validated['profile_photo'] = $path;
        }
        
        // Convert permissions array to JSON
        if (isset($validated['permissions'])) {
            $validated['permissions'] = json_encode($validated['permissions']);
        }
        
        // Create user
        User::create($validated);
        
        return redirect()->route('users.index')
            ->with('success', 'User created successfully!');
    }

    /**
     * Display the specified user
     */
    public function show($id)
    {
        $user = Auth::guard('user')->user();
        $targetUser = User::findOrFail($id);
        
        return view('pages.users.show', compact('user', 'targetUser'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit($id)
    {
        $user = Auth::guard('user')->user();
        $targetUser = User::findOrFail($id);
        $departments = collect([]);
        $roles = ['admin', 'manager', 'cashier', 'stock_keeper'];
        
        // Decode permissions if JSON
        if ($targetUser->permissions) {
            $targetUser->permissions = is_string($targetUser->permissions) 
                ? json_decode($targetUser->permissions, true) 
                : $targetUser->permissions;
        }
        
        return view('pages.users.edit', compact('user', 'targetUser', 'departments', 'roles'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $targetUser = User::findOrFail($id);
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $targetUser->id,
            'phone' => 'nullable|string|max:20',
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role' => 'required|in:admin,manager,cashier,stock_keeper',
            'hire_date' => 'required|date',
            'hourly_rate' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'profile_photo' => 'nullable|image|max:2048',
            'permissions' => 'nullable|array'
        ]);
        
        // Hash password if provided
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        
        // Set is_active
        $validated['is_active'] = $request->has('is_active') || ($request->status === 'active');
        
        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            if ($targetUser->profile_photo) {
                Storage::disk('public')->delete($targetUser->profile_photo);
            }
            
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $validated['profile_photo'] = $path;
        }
        
        // Convert permissions array to JSON
        if (isset($validated['permissions'])) {
            $validated['permissions'] = json_encode($validated['permissions']);
        }
        
        // Update user
        $targetUser->update($validated);
        
        return redirect()->route('users.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        $targetUser = User::findOrFail($id);
        $userName = $targetUser->first_name . ' ' . $targetUser->last_name;
        
        // Delete profile photo if exists
        if ($targetUser->profile_photo) {
            Storage::disk('public')->delete($targetUser->profile_photo);
        }
        
        $targetUser->delete();
        
        return redirect()->route('users.index')
            ->with('success', "User {$userName} deleted successfully!");
    }

    /**
     * Show user activity log
     */
    public function activity(Request $request)
    {
        $user = Auth::guard('user')->user();
        $activities = collect([]);
        
        return view('pages.users.activity', compact('user', 'activities'));
    }

    /**
     * Show security settings
     */
    public function security()
    {
        $user = Auth::guard('user')->user();
        
        return view('pages.users.security', compact('user'));
    }

    /**
     * Import users from CSV/Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:5120'
        ]);
        
        // TODO: Implement import logic
        
        return redirect()->route('users.index')
            ->with('success', 'Users imported successfully!');
    }

    /**
     * Export users to CSV
     */
    public function export(Request $request)
    {
        $users = User::all();
        $filename = 'users_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Role', 'Active', 'Hire Date']);
            
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->first_name,
                    $user->last_name,
                    $user->email,
                    $user->phone,
                    $user->role,
                    $user->is_active ? 'Yes' : 'No',
                    $user->hire_date
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download CSV template
     */
    public function downloadTemplate()
    {
        $filename = 'user_import_template.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['first_name', 'last_name', 'email', 'phone', 'role', 'hire_date', 'hourly_rate', 'address']);
            fputcsv($file, ['John', 'Doe', 'john@example.com', '1234567890', 'cashier', '2024-01-01', '50.00', '123 Main St']);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk operations
     */
    public function bulkActivate(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);
        
        User::whereIn('id', $request->user_ids)->update(['is_active' => true]);
        
        return redirect()->route('users.index')
            ->with('success', count($request->user_ids) . ' user(s) activated!');
    }

    public function bulkSuspend(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);
        
        User::whereIn('id', $request->user_ids)->update(['is_active' => false]);
        
        return redirect()->route('users.index')
            ->with('success', count($request->user_ids) . ' user(s) suspended!');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);
        
        User::whereIn('id', $request->user_ids)->delete();
        
        return redirect()->route('users.index')
            ->with('success', count($request->user_ids) . ' user(s) deleted!');
    }

    public function bulkAssignRole(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role' => 'required|in:admin,manager,cashier,stock_keeper'
        ]);
        
        User::whereIn('id', $request->user_ids)->update(['role' => $request->role]);
        
        return redirect()->route('users.index')
            ->with('success', count($request->user_ids) . ' user(s) role updated!');
    }

    /**
     * User permissions
     */
    public function permissions($id)
    {
        $user = Auth::guard('user')->user();
        $targetUser = User::findOrFail($id);
        
        if ($targetUser->permissions) {
            $targetUser->permissions = is_string($targetUser->permissions) 
                ? json_decode($targetUser->permissions, true) 
                : $targetUser->permissions;
        }
        
        return view('pages.users.permissions', compact('user', 'targetUser'));
    }

    public function updatePermissions(Request $request, $id)
    {
        $targetUser = User::findOrFail($id);
        
        $request->validate([
            'permissions' => 'nullable|array'
        ]);
        
        $targetUser->update([
            'permissions' => json_encode($request->permissions ?? [])
        ]);
        
        return redirect()->route('users.index')
            ->with('success', 'User permissions updated!');
    }
}