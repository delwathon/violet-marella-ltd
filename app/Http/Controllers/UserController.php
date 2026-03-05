<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Business;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Support\AccessControl;
use App\Support\SecuritySettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->with(['department', 'roleRecord', 'businesses']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('business')) {
            $businessSlug = AccessControl::resolveBusinessSlug((string) $request->business);

            if ($businessSlug !== null) {
                $query->whereHas('businesses', function ($businessQuery) use ($businessSlug) {
                    $businessQuery->where('slug', $businessSlug);
                });
            }
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $departments = Department::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $businesses = Business::query()->where('is_active', true)->orderBy('name')->get();

        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $pendingUsers = User::where('is_active', false)->count();
        $adminUsers = User::whereIn('role', ['superadmin', 'admin'])->count();

        $currentUser = Auth::guard('user')->user();

        return view('pages.users.index', compact(
            'users',
            'currentUser',
            'departments',
            'roles',
            'businesses',
            'totalUsers',
            'activeUsers',
            'pendingUsers',
            'adminUsers'
        ) + ['user' => $currentUser]);
    }

    public function create(): View
    {
        $currentUser = Auth::guard('user')->user();
        $departments = Department::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $businesses = Business::query()->where('is_active', true)->orderBy('name')->get();
        $permissionGroups = AccessControl::permissionGroups();

        return view('pages.users.create-edit', [
            'currentUser' => $currentUser,
            'user' => $currentUser,
            'targetUser' => null,
            'departments' => $departments,
            'roles' => $roles,
            'businesses' => $businesses,
            'permissionGroups' => $permissionGroups,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $roleSlugs = $this->allowedRoleSlugs();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', Rule::in($roleSlugs)],
            'department_id' => 'nullable|exists:departments,id',
            'hire_date' => 'required|date',
            'hourly_rate' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'profile_photo' => 'nullable|image|max:2048',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|max:100',
            'businesses' => 'required_unless:role,admin,superadmin|array',
            'businesses.*' => 'string|exists:businesses,slug',
            'status' => 'nullable|in:active,inactive',
        ]);

        $businessSlugs = $validated['businesses'] ?? [];
        unset($validated['businesses']);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = ($request->status ?? 'active') === 'active';
        $validated['permissions'] = $this->sanitizePermissions($validated['permissions'] ?? []);

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user = User::create($validated);
        $this->syncUserBusinesses($user, $businessSlugs);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(int $id): View
    {
        $currentUser = Auth::guard('user')->user();
        $targetUser = User::with(['department', 'roleRecord', 'businesses'])->findOrFail($id);

        return view('pages.users.show', compact('currentUser', 'targetUser') + ['user' => $currentUser]);
    }

    public function edit(int $id): View
    {
        $currentUser = Auth::guard('user')->user();
        $targetUser = User::with('businesses')->findOrFail($id);
        $departments = Department::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $businesses = Business::query()->where('is_active', true)->orderBy('name')->get();
        $permissionGroups = AccessControl::permissionGroups();

        return view('pages.users.create-edit', compact(
            'currentUser',
            'targetUser',
            'departments',
            'roles',
            'businesses',
            'permissionGroups'
        ) + ['user' => $currentUser]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $targetUser = User::with('businesses')->findOrFail($id);
        $roleSlugs = $this->allowedRoleSlugs();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $targetUser->id,
            'phone' => 'nullable|string|max:20',
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role' => ['required', Rule::in($roleSlugs)],
            'department_id' => 'nullable|exists:departments,id',
            'hire_date' => 'required|date',
            'hourly_rate' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'profile_photo' => 'nullable|image|max:2048',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|max:100',
            'businesses' => 'required_unless:role,admin,superadmin|array',
            'businesses.*' => 'string|exists:businesses,slug',
            'status' => 'nullable|in:active,inactive',
        ]);

        $businessSlugs = $validated['businesses'] ?? [];
        unset($validated['businesses']);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = ($request->status ?? ($targetUser->is_active ? 'active' : 'inactive')) === 'active';
        $validated['permissions'] = $this->sanitizePermissions($validated['permissions'] ?? []);

        if ($request->hasFile('profile_photo')) {
            if ($targetUser->profile_photo) {
                Storage::disk('public')->delete($targetUser->profile_photo);
            }

            $validated['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $targetUser->update($validated);
        $this->syncUserBusinesses($targetUser, $businessSlugs);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $targetUser = User::findOrFail($id);

        if ((int) Auth::guard('user')->id() === $targetUser->id) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account while logged in.');
        }

        $userName = $targetUser->full_name;

        if ($targetUser->profile_photo) {
            Storage::disk('public')->delete($targetUser->profile_photo);
        }

        $targetUser->delete();

        return redirect()->route('users.index')
            ->with('success', "User {$userName} deleted successfully.");
    }

    public function activity(Request $request): View
    {
        $currentUser = Auth::guard('user')->user();
        $activityQuery = $this->buildActivityQuery($request);

        $activities = $activityQuery
            ->with('user:id,first_name,last_name,email')
            ->latest('created_at')
            ->paginate(30)
            ->withQueryString();

        $totalActivities = ActivityLog::count();
        $todayActivities = ActivityLog::whereDate('created_at', now())->count();
        $securityAlerts = ActivityLog::where(function ($query) {
            $query->where('module', 'security')
                ->orWhere('status_code', '>=', 400);
        })->count();
        $activeUsersToday = ActivityLog::whereDate('created_at', now())
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');

        $usersForFilter = User::orderBy('first_name')->get(['id', 'first_name', 'last_name']);
        $modules = ActivityLog::select('module')
            ->whereNotNull('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        return view('pages.users.activity', compact(
            'currentUser',
            'activities',
            'totalActivities',
            'todayActivities',
            'securityAlerts',
            'activeUsersToday',
            'usersForFilter',
            'modules'
        ) + ['user' => $currentUser]);
    }

    public function exportActivity(Request $request)
    {
        $activities = $this->buildActivityQuery($request)
            ->with('user:id,first_name,last_name,email')
            ->latest('created_at')
            ->limit(5000)
            ->get();

        $filename = 'activity_logs_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = static function () use ($activities) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Timestamp', 'User', 'Action', 'Module', 'Method', 'URL', 'IP', 'Status']);

            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity->created_at,
                    $activity->user?->full_name ?? 'System',
                    $activity->action,
                    $activity->module,
                    $activity->method,
                    $activity->url,
                    $activity->ip_address,
                    $activity->status_code,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function security(): View
    {
        $currentUser = Auth::guard('user')->user();
        $passwordPolicy = SecuritySettings::passwordPolicy();
        $authSettings = SecuritySettings::authSettings();
        $auditLogSettings = SecuritySettings::auditLogSettings();
        $whitelist = SecuritySettings::ipWhitelist();
        $blacklist = SecuritySettings::ipBlacklist();

        return view('pages.users.security', compact(
            'currentUser',
            'passwordPolicy',
            'authSettings',
            'auditLogSettings',
            'whitelist',
            'blacklist'
        ) + ['user' => $currentUser]);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $roleSlugs = $this->allowedRoleSlugs();
        $file = fopen($request->file('file')->getRealPath(), 'r');

        if ($file === false) {
            return redirect()->route('users.index')
                ->with('error', 'Unable to read uploaded file.');
        }

        $header = fgetcsv($file);
        if ($header === false) {
            fclose($file);
            return redirect()->route('users.index')
                ->with('error', 'CSV file is empty.');
        }

        $header = array_map(fn ($value) => strtolower(trim((string) $value)), $header);
        $hasBusinessesColumn = in_array('businesses', $header, true);

        $required = ['first_name', 'last_name', 'email', 'role', 'hire_date'];
        foreach ($required as $column) {
            if (!in_array($column, $header, true)) {
                fclose($file);
                return redirect()->route('users.index')
                    ->with('error', "Missing required column: {$column}");
            }
        }

        $processed = 0;
        $created = 0;
        $updated = 0;
        $skipped = 0;

        while (($row = fgetcsv($file)) !== false) {
            $processed++;
            $data = [];

            foreach ($header as $index => $column) {
                $data[$column] = isset($row[$index]) ? trim((string) $row[$index]) : null;
            }

            if (!filter_var($data['email'] ?? null, FILTER_VALIDATE_EMAIL)) {
                $skipped++;
                continue;
            }

            if (!in_array($data['role'], $roleSlugs, true)) {
                $skipped++;
                continue;
            }

            $departmentId = null;
            if (!empty($data['department_id']) && is_numeric($data['department_id'])) {
                $departmentId = Department::where('id', (int) $data['department_id'])->value('id');
            }

            $businessSlugs = $hasBusinessesColumn
                ? $this->parseBusinessSlugs($data['businesses'] ?? null)
                : [];

            $payload = [
                'first_name' => $data['first_name'] ?: 'N/A',
                'last_name' => $data['last_name'] ?: 'N/A',
                'phone' => $data['phone'] ?: null,
                'role' => $data['role'],
                'department_id' => $departmentId,
                'hire_date' => $this->safeDate($data['hire_date']),
                'hourly_rate' => is_numeric($data['hourly_rate'] ?? null) ? $data['hourly_rate'] : null,
                'address' => $data['address'] ?: null,
                'emergency_contact' => $data['emergency_contact'] ?: null,
                'emergency_phone' => $data['emergency_phone'] ?: null,
                'is_active' => !isset($data['is_active']) || in_array(strtolower((string) $data['is_active']), ['1', 'true', 'yes', 'active'], true),
            ];

            $existingUser = User::where('email', $data['email'])->first();

            if ($existingUser) {
                $existingUser->update($payload);
                $this->syncUserBusinesses($existingUser, $businessSlugs, !$hasBusinessesColumn);
                $updated++;
                continue;
            }

            $payload['email'] = $data['email'];
            $payload['password'] = Hash::make($data['password'] ?: Str::random(12));
            $payload['permissions'] = [];

            $user = User::create($payload);
            $this->syncUserBusinesses($user, $businessSlugs, false);
            $created++;
        }

        fclose($file);

        return redirect()->route('users.index')
            ->with('success', "Import complete: {$created} created, {$updated} updated, {$skipped} skipped ({$processed} rows processed).");
    }

    public function export(Request $request)
    {
        $users = User::with(['department', 'businesses'])->get();
        $filename = 'users_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = static function () use ($users) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Role', 'Department', 'Businesses', 'Active', 'Hire Date']);

            foreach ($users as $exportUser) {
                fputcsv($file, [
                    $exportUser->id,
                    $exportUser->first_name,
                    $exportUser->last_name,
                    $exportUser->email,
                    $exportUser->phone,
                    $exportUser->role,
                    $exportUser->department?->name,
                    $exportUser->businesses->pluck('slug')->implode(','),
                    $exportUser->is_active ? 'Yes' : 'No',
                    $exportUser->hire_date,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadTemplate()
    {
        $filename = 'user_import_template.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = static function () {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'first_name',
                'last_name',
                'email',
                'phone',
                'role',
                'department_id',
                'hire_date',
                'hourly_rate',
                'address',
                'emergency_contact',
                'emergency_phone',
                'businesses',
                'is_active',
            ]);
            fputcsv($file, [
                'John',
                'Doe',
                'john@example.com',
                '+2348000000000',
                'sales_representative',
                '1',
                '2025-01-01',
                '50.00',
                '123 Main St',
                'Jane Doe',
                '+2348000000001',
                'lounge,gift_store',
                'true',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bulkActivate(Request $request): RedirectResponse
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        User::whereIn('id', $request->user_ids)->update(['is_active' => true]);

        return redirect()->route('users.index')
            ->with('success', count($request->user_ids) . ' user(s) activated.');
    }

    public function bulkSuspend(Request $request): RedirectResponse
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        User::whereIn('id', $request->user_ids)->update(['is_active' => false]);

        return redirect()->route('users.index')
            ->with('success', count($request->user_ids) . ' user(s) suspended.');
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $currentUserId = Auth::guard('user')->id();
        $ids = collect($request->user_ids)->reject(fn ($id) => (int) $id === (int) $currentUserId)->values();

        User::whereIn('id', $ids)->delete();

        return redirect()->route('users.index')
            ->with('success', $ids->count() . ' user(s) deleted.');
    }

    public function bulkAssignRole(Request $request): RedirectResponse
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role' => ['required', Rule::in($this->allowedRoleSlugs())],
        ]);

        $users = User::whereIn('id', $request->user_ids)->get();

        foreach ($users as $user) {
            $user->update(['role' => $request->role]);
            $this->syncUserBusinesses($user, $user->businesses->pluck('slug')->all());
        }

        return redirect()->route('users.index')
            ->with('success', count($request->user_ids) . ' user(s) role updated.');
    }

    public function permissions(int $id): View
    {
        $currentUser = Auth::guard('user')->user();
        $targetUser = User::findOrFail($id);
        $permissionGroups = AccessControl::permissionGroups();

        return view('pages.users.permissions', compact('currentUser', 'targetUser', 'permissionGroups') + ['user' => $currentUser]);
    }

    public function updatePermissions(Request $request, int $id): RedirectResponse
    {
        $targetUser = User::findOrFail($id);

        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|max:100',
        ]);

        $targetUser->update([
            'permissions' => $this->sanitizePermissions($validated['permissions'] ?? []),
        ]);

        return redirect()->route('users.permissions', $targetUser->id)
            ->with('success', 'User permissions updated.');
    }

    private function safeDate(?string $value): string
    {
        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable $e) {
            return now()->toDateString();
        }
    }

    /**
     * @return array<int, string>
     */
    private function allowedRoleSlugs(): array
    {
        $slugs = Role::query()->pluck('slug')->all();

        if ($slugs === []) {
            return ['superadmin', 'admin', 'manager', 'sales_representative', 'receptionist', 'cashier', 'stock_keeper'];
        }

        return $slugs;
    }

    /**
     * @param array<int, mixed> $permissions
     * @return array<int, string>
     */
    private function sanitizePermissions(array $permissions): array
    {
        return collect($permissions)
            ->map(fn ($permission) => trim((string) $permission))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param array<int, string> $businessSlugs
     */
    private function syncUserBusinesses(User $user, array $businessSlugs, bool $preserveWhenEmpty = false): void
    {
        if ($user->isAdmin()) {
            $allBusinessIds = Business::query()->pluck('id')->all();
            $user->businesses()->sync($allBusinessIds);
            return;
        }

        $resolvedSlugs = collect($businessSlugs)
            ->map(fn ($slug) => AccessControl::resolveBusinessSlug((string) $slug))
            ->filter()
            ->values()
            ->all();

        if ($resolvedSlugs === [] && $preserveWhenEmpty) {
            return;
        }

        $businessIds = Business::query()
            ->whereIn('slug', $resolvedSlugs)
            ->pluck('id')
            ->all();

        $user->businesses()->sync($businessIds);
    }

    /**
     * @return array<int, string>
     */
    private function parseBusinessSlugs(?string $value): array
    {
        if ($value === null || trim($value) === '') {
            return [];
        }

        return collect(preg_split('/[,\|;]+/', strtolower($value)) ?: [])
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->map(fn (string $slug) => AccessControl::resolveBusinessSlug($slug))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function buildActivityQuery(Request $request)
    {
        $query = ActivityLog::query();

        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        return $query;
    }
}
