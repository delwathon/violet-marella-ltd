<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
        $query = User::query()->with(['department', 'roleRecord']);

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

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $departments = Department::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $pendingUsers = User::where('is_active', false)->count();
        $adminUsers = User::where('role', 'admin')->count();

        $currentUser = Auth::guard('user')->user();

        return view('pages.users.index', compact(
            'users',
            'currentUser',
            'departments',
            'roles',
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

        return view('pages.users.create-edit', [
            'currentUser' => $currentUser,
            'user' => $currentUser,
            'targetUser' => null,
            'departments' => $departments,
            'roles' => $roles,
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
            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = ($request->status ?? 'active') === 'active';
        $validated['permissions'] = array_values(array_unique($validated['permissions'] ?? []));

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(int $id): View
    {
        $currentUser = Auth::guard('user')->user();
        $targetUser = User::with(['department', 'roleRecord'])->findOrFail($id);

        return view('pages.users.show', compact('currentUser', 'targetUser') + ['user' => $currentUser]);
    }

    public function edit(int $id): View
    {
        $currentUser = Auth::guard('user')->user();
        $targetUser = User::findOrFail($id);
        $departments = Department::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('pages.users.create-edit', compact('currentUser', 'targetUser', 'departments', 'roles') + ['user' => $currentUser]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $targetUser = User::findOrFail($id);
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
            'status' => 'nullable|in:active,inactive',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = ($request->status ?? ($targetUser->is_active ? 'active' : 'inactive')) === 'active';
        $validated['permissions'] = array_values(array_unique($validated['permissions'] ?? []));

        if ($request->hasFile('profile_photo')) {
            if ($targetUser->profile_photo) {
                Storage::disk('public')->delete($targetUser->profile_photo);
            }

            $validated['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $targetUser->update($validated);

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
        $activities = collect([]);

        return view('pages.users.activity', compact('currentUser', 'activities') + ['user' => $currentUser]);
    }

    public function security(): View
    {
        $currentUser = Auth::guard('user')->user();
        $passwordPolicy = Cache::get('password_policy', [
            'min_length' => 8,
            'require_uppercase' => true,
            'require_lowercase' => true,
            'require_numbers' => true,
            'require_special' => false,
            'password_expiry' => 90,
            'password_history' => 5,
        ]);
        $authSettings = Cache::get('auth_settings', [
            'enable_2fa' => false,
            'force_2fa_admins' => false,
            'session_timeout' => 120,
            'max_login_attempts' => 5,
            'lockout_duration' => 30,
            'enable_ip_whitelist' => false,
        ]);
        $auditLogSettings = Cache::get('audit_log_settings', [
            'log_retention' => 365,
            'log_logins' => true,
            'log_changes' => true,
            'log_deletions' => true,
        ]);
        $whitelist = Cache::get('ip_whitelist', []);
        $blacklist = Cache::get('ip_blacklist', []);

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
                $updated++;
                continue;
            }

            $payload['email'] = $data['email'];
            $payload['password'] = Hash::make($data['password'] ?: Str::random(12));
            $payload['permissions'] = [];

            User::create($payload);
            $created++;
        }

        fclose($file);

        return redirect()->route('users.index')
            ->with('success', "Import complete: {$created} created, {$updated} updated, {$skipped} skipped ({$processed} rows processed).");
    }

    public function export(Request $request)
    {
        $users = User::with('department')->get();
        $filename = 'users_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = static function () use ($users) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Role', 'Department', 'Active', 'Hire Date']);

            foreach ($users as $exportUser) {
                fputcsv($file, [
                    $exportUser->id,
                    $exportUser->first_name,
                    $exportUser->last_name,
                    $exportUser->email,
                    $exportUser->phone,
                    $exportUser->role,
                    $exportUser->department?->name,
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

            fputcsv($file, ['first_name', 'last_name', 'email', 'phone', 'role', 'department_id', 'hire_date', 'hourly_rate', 'address', 'emergency_contact', 'emergency_phone', 'is_active']);
            fputcsv($file, ['John', 'Doe', 'john@example.com', '+2348000000000', 'cashier', '1', '2025-01-01', '50.00', '123 Main St', 'Jane Doe', '+2348000000001', 'true']);

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

        User::whereIn('id', $request->user_ids)->update(['role' => $request->role]);

        return redirect()->route('users.index')
            ->with('success', count($request->user_ids) . ' user(s) role updated.');
    }

    public function permissions(int $id): View
    {
        $currentUser = Auth::guard('user')->user();
        $targetUser = User::findOrFail($id);

        return view('pages.users.permissions', compact('currentUser', 'targetUser') + ['user' => $currentUser]);
    }

    public function updatePermissions(Request $request, int $id): RedirectResponse
    {
        $targetUser = User::findOrFail($id);

        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|max:100',
        ]);

        $targetUser->update([
            'permissions' => array_values(array_unique($validated['permissions'] ?? [])),
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

    private function allowedRoleSlugs(): array
    {
        $slugs = Role::query()->pluck('slug')->all();

        if ($slugs === []) {
            return ['admin', 'manager', 'cashier', 'stock_keeper'];
        }

        return $slugs;
    }
}
