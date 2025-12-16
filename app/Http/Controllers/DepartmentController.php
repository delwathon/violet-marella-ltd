<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments
     */
    public function index()
    {
        $user = Auth::guard('user')->user();
        
        // Sample departments (replace with actual database when table is created)
        $departments = [
            [
                'id' => 1,
                'name' => 'Management',
                'icon' => 'user-tie',
                'color' => 'primary',
                'head' => 'John Doe',
                'members' => 3,
                'description' => 'Executive leadership and strategic planning'
            ],
            [
                'id' => 2,
                'name' => 'Sales & Marketing',
                'icon' => 'chart-line',
                'color' => 'success',
                'head' => 'Jane Smith',
                'members' => 12,
                'description' => 'Sales operations and marketing campaigns'
            ],
            [
                'id' => 3,
                'name' => 'Operations',
                'icon' => 'cogs',
                'color' => 'info',
                'head' => 'Mike Johnson',
                'members' => 15,
                'description' => 'Day-to-day business operations'
            ],
            [
                'id' => 4,
                'name' => 'Finance',
                'icon' => 'dollar-sign',
                'color' => 'warning',
                'head' => 'Sarah Williams',
                'members' => 5,
                'description' => 'Financial planning and accounting'
            ],
            [
                'id' => 5,
                'name' => 'Human Resources',
                'icon' => 'users',
                'color' => 'danger',
                'head' => 'Emily Brown',
                'members' => 4,
                'description' => 'Employee relations and recruitment'
            ],
            [
                'id' => 6,
                'name' => 'IT & Technology',
                'icon' => 'laptop-code',
                'color' => 'dark',
                'head' => 'David Lee',
                'members' => 6,
                'description' => 'Technology infrastructure and support'
            ],
            [
                'id' => 7,
                'name' => 'Customer Service',
                'icon' => 'headset',
                'color' => 'secondary',
                'head' => 'Lisa Garcia',
                'members' => 8,
                'description' => 'Customer support and relations'
            ],
            [
                'id' => 8,
                'name' => 'Logistics',
                'icon' => 'truck',
                'color' => 'primary',
                'head' => 'Tom Anderson',
                'members' => 7,
                'description' => 'Supply chain and delivery management'
            ]
        ];
        
        // Statistics
        $totalDepartments = count($departments);
        $totalMembers = array_sum(array_column($departments, 'members'));
        $departmentHeads = count($departments);
        
        return view('pages.departments.index', compact(
            'user',
            'departments',
            'totalDepartments',
            'totalMembers',
            'departmentHeads'
        ));
    }

    /**
     * Store a newly created department
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'head_id' => 'nullable|exists:users,id',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20'
        ]);
        
        // TODO: Implement department creation when departments table is added
        
        return redirect()->route('departments.index')
            ->with('success', 'Department created successfully!');
    }

    /**
     * Display the specified department
     */
    public function show($id)
    {
        $user = Auth::guard('user')->user();
        
        // TODO: Fetch actual department when departments table exists
        
        return view('pages.departments.show', compact('user'));
    }

    /**
     * Update the specified department
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'head_id' => 'nullable|exists:users,id',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20'
        ]);
        
        // TODO: Implement department update when departments table is added
        
        return redirect()->route('departments.index')
            ->with('success', 'Department updated successfully!');
    }

    /**
     * Remove the specified department
     */
    public function destroy($id)
    {
        // TODO: Implement department deletion when departments table is added
        // Ensure no users are assigned to this department
        
        return redirect()->route('departments.index')
            ->with('success', 'Department deleted successfully!');
    }

    /**
     * Show department members
     */
    public function members($id)
    {
        $user = Auth::guard('user')->user();
        
        // TODO: Fetch department members when departments table exists
        
        return view('pages.departments.members', compact('user'));
    }

    /**
     * Add member to department
     */
    public function addMember(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);
        
        // TODO: Implement adding member to department
        
        return redirect()->route('departments.members', $id)
            ->with('success', 'Member added to department successfully!');
    }

    /**
     * Remove member from department
     */
    public function removeMember($departmentId, $userId)
    {
        // TODO: Implement removing member from department
        
        return redirect()->route('departments.members', $departmentId)
            ->with('success', 'Member removed from department successfully!');
    }
}