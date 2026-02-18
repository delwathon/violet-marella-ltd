<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $user = Auth::guard('user')->user();

        $roles = Role::query()
            ->withCount('users')
            ->orderByDesc('is_system')
            ->orderBy('name')
            ->get();

        $totalRoles = $roles->count();
        $totalPermissions = $roles
            ->flatMap(fn (Role $role) => $role->permissions ?? [])
            ->unique()
            ->count();
        $customRoles = $roles->where('is_system', false)->count();
        $permissionGroups = $roles
            ->flatMap(function (Role $role) {
                return collect($role->permissions ?? [])->map(function (string $permission) {
                    return Str::contains($permission, '.')
                        ? Str::before($permission, '.')
                        : $permission;
                });
            })
            ->unique()
            ->count();

        return view('pages.roles.index', compact(
            'user',
            'roles',
            'totalRoles',
            'totalPermissions',
            'customRoles',
            'permissionGroups'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:roles,slug',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'template' => 'nullable|exists:roles,slug',
        ]);

        $templateRole = null;
        if (!empty($validated['template'])) {
            $templateRole = Role::where('slug', $validated['template'])->first();
        }

        $slug = $validated['slug'] ?: Str::slug($validated['name']);
        $slug = $this->makeUniqueSlug($slug);

        Role::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'color' => $validated['color'] ?? 'primary',
            'permissions' => $templateRole?->permissions ?? [],
            'is_system' => false,
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function show(int $id): View
    {
        $user = Auth::guard('user')->user();
        $role = Role::withCount('users')->findOrFail($id);
        $users = User::where('role', $role->slug)
            ->orderBy('first_name')
            ->paginate(20);

        return view('pages.roles.show', compact('user', 'role', 'users'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'slug' => 'nullable|string|max:255|unique:roles,slug,' . $role->id,
        ]);

        $payload = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'color' => $validated['color'] ?? $role->color,
        ];

        if (!$role->is_system) {
            $requestedSlug = $validated['slug'] ?? Str::slug($validated['name']);
            $payload['slug'] = $this->makeUniqueSlug($requestedSlug, $role->id);
        }

        $role->update($payload);

        return redirect()->route('roles.show', $role->id)
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $role = Role::withCount('users')->findOrFail($id);

        if ($role->is_system) {
            return redirect()->route('roles.index')
                ->with('error', 'System roles cannot be deleted.');
        }

        if ($role->users_count > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'This role has assigned users and cannot be deleted.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    public function updatePermissions(Request $request, int $id): RedirectResponse
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|max:100',
        ]);

        $permissions = collect($validated['permissions'] ?? [])
            ->map(fn (string $permission) => trim($permission))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $role->update([
            'permissions' => $permissions,
        ]);

        return redirect()->route('roles.show', $role->id)
            ->with('success', 'Role permissions updated successfully.');
    }

    public function duplicate(int $id): RedirectResponse
    {
        $role = Role::findOrFail($id);

        $baseName = $role->name . ' Copy';
        $copyName = $baseName;
        $suffix = 2;

        while (Role::where('name', $copyName)->exists()) {
            $copyName = $baseName . ' ' . $suffix;
            $suffix++;
        }

        $copySlug = $this->makeUniqueSlug(Str::slug($copyName));

        Role::create([
            'name' => $copyName,
            'slug' => $copySlug,
            'description' => $role->description,
            'color' => $role->color,
            'permissions' => $role->permissions,
            'is_system' => false,
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role duplicated successfully.');
    }

    private function makeUniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($slug) ?: 'role';
        $candidate = $baseSlug;
        $index = 2;

        while (Role::where('slug', $candidate)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $candidate = $baseSlug . '-' . $index;
            $index++;
        }

        return $candidate;
    }
}
