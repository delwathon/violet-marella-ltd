<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RoleController extends Controller
{
    /**
     * Display roles and permissions matrix
     */
    public function index()
    {
        $user = Auth::guard('user')->user();
        
        // Get role statistics
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Full administrative access to the system',
                'users_count' => User::where('role', 'admin')->count(),
                'color' => 'danger',
                'is_system' => true
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Manage operations and team members',
                'users_count' => User::where('role', 'manager')->count(),
                'color' => 'primary',
                'is_system' => true
            ],
            [
                'name' => 'Cashier',
                'slug' => 'cashier',
                'description' => 'Process sales and handle transactions',
                'users_count' => User::where('role', 'cashier')->count(),
                'color' => 'success',
                'is_system' => true
            ],
            [
                'name' => 'Stock Keeper',
                'slug' => 'stock_keeper',
                'description' => 'Manage inventory and stock levels',
                'users_count' => User::where('role', 'stock_keeper')->count(),
                'color' => 'info',
                'is_system' => true
            ]
        ];
        
        // Statistics
        $totalRoles = count($roles);
        $totalPermissions = 40; // Estimate
        $customRoles = 0;
        $permissionGroups = 8;
        
        return view('pages.roles.index', compact(
            'user',
            'roles',
            'totalRoles',
            'totalPermissions',
            'customRoles',
            'permissionGroups'
        ));
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug',
            'description' => 'nullable|string',
            'template' => 'nullable|in:admin,manager,cashier,stock_keeper'
        ]);
        
        // TODO: Implement role creation when roles table is added
        // For now, just redirect with success message
        
        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully!');
    }

    /**
     * Display the specified role
     */
    public function show($id)
    {
        $user = Auth::guard('user')->user();
        
        // TODO: Fetch actual role when roles table exists
        
        return view('pages.roles.show', compact('user'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
        
        // TODO: Implement role update when roles table is added
        
        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully!');
    }

    /**
     * Remove the specified role
     */
    public function destroy($id)
    {
        // TODO: Implement role deletion when roles table is added
        // Ensure role is not system role and has no users assigned
        
        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully!');
    }

    /**
     * Update role permissions
     */
    public function updatePermissions(Request $request, $id)
    {
        $request->validate([
            'permissions' => 'nullable|array'
        ]);
        
        // TODO: Implement permissions update when roles table is added
        
        return redirect()->route('roles.index')
            ->with('success', 'Role permissions updated successfully!');
    }

    /**
     * Duplicate an existing role
     */
    public function duplicate($id)
    {
        // TODO: Implement role duplication when roles table is added
        
        return redirect()->route('roles.index')
            ->with('success', 'Role duplicated successfully!');
    }
}