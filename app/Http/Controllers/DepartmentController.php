<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $user = Auth::guard('user')->user();

        $departments = Department::query()
            ->with('head:id,first_name,last_name')
            ->withCount('users')
            ->orderBy('name')
            ->get();

        $totalDepartments = $departments->count();
        $totalMembers = User::whereNotNull('department_id')->count();
        $departmentHeads = $departments->whereNotNull('head_id')->count();
        $availableHeads = User::active()->orderBy('first_name')->get();

        return view('pages.departments.index', compact(
            'user',
            'departments',
            'totalDepartments',
            'totalMembers',
            'departmentHeads',
            'availableHeads'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string',
            'head_id' => 'nullable|exists:users,id',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        Department::create([
            'name' => $validated['name'],
            'slug' => $this->makeUniqueSlug(Str::slug($validated['name'])),
            'description' => $validated['description'] ?? null,
            'head_id' => $validated['head_id'] ?? null,
            'icon' => $validated['icon'] ?? 'users',
            'color' => $validated['color'] ?? 'primary',
            'is_active' => array_key_exists('is_active', $validated) ? (bool) $validated['is_active'] : true,
        ]);

        return redirect()->route('departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function show(int $id): View
    {
        $user = Auth::guard('user')->user();

        $department = Department::with(['head:id,first_name,last_name,email', 'users' => function ($query) {
            $query->orderBy('first_name');
        }])->findOrFail($id);

        return view('pages.departments.show', compact('user', 'department'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $department = Department::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable|string',
            'head_id' => 'nullable|exists:users,id',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        $department->update([
            'name' => $validated['name'],
            'slug' => $this->makeUniqueSlug(Str::slug($validated['name']), $department->id),
            'description' => $validated['description'] ?? null,
            'head_id' => $validated['head_id'] ?? null,
            'icon' => $validated['icon'] ?? 'users',
            'color' => $validated['color'] ?? 'primary',
            'is_active' => array_key_exists('is_active', $validated) ? (bool) $validated['is_active'] : $department->is_active,
        ]);

        return redirect()->route('departments.show', $department->id)
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $department = Department::withCount('users')->findOrFail($id);

        if ($department->users_count > 0) {
            return redirect()->route('departments.index')
                ->with('error', 'This department still has members. Remove them before deletion.');
        }

        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    public function members(int $id): View
    {
        $user = Auth::guard('user')->user();

        $department = Department::with('head:id,first_name,last_name')->findOrFail($id);
        $members = User::where('department_id', $department->id)
            ->orderBy('first_name')
            ->paginate(20);

        $availableUsers = User::where(function ($query) use ($department) {
            $query->whereNull('department_id')
                ->orWhere('department_id', '!=', $department->id);
        })->orderBy('first_name')->get();

        return view('pages.departments.members', compact('user', 'department', 'members', 'availableUsers'));
    }

    public function addMember(Request $request, int $id): RedirectResponse
    {
        $department = Department::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        User::where('id', $validated['user_id'])->update([
            'department_id' => $department->id,
        ]);

        return redirect()->route('departments.members', $department->id)
            ->with('success', 'Member added to department successfully.');
    }

    public function removeMember(int $departmentId, int $userId): RedirectResponse
    {
        $department = Department::findOrFail($departmentId);

        User::where('id', $userId)
            ->where('department_id', $department->id)
            ->update(['department_id' => null]);

        return redirect()->route('departments.members', $department->id)
            ->with('success', 'Member removed from department successfully.');
    }

    private function makeUniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $baseSlug = $slug !== '' ? $slug : 'department';
        $candidate = $baseSlug;
        $index = 2;

        while (Department::where('slug', $candidate)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $candidate = $baseSlug . '-' . $index;
            $index++;
        }

        return $candidate;
    }
}
