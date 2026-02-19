<?php

namespace Tests\Feature;

use App\Http\Middleware\LogUserActivity;
use App\Models\User;
use App\Support\SecuritySettings;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SecurityAndAccessMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(LogUserActivity::class);

        Cache::forget('ip_blacklist');
        Cache::forget('ip_whitelist_enabled');
        Cache::forget('ip_whitelist');
        Cache::forget('auth_settings');
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_role_middleware_blocks_non_admin_user(): void
    {
        $user = $this->makeUser('manager');

        $this->actingAs($user, 'user')
            ->get(route('users.download-template'))
            ->assertForbidden();
    }

    public function test_role_middleware_allows_admin_user(): void
    {
        $user = $this->makeUser('admin');

        $this->actingAs($user, 'user')
            ->get(route('users.download-template'))
            ->assertOk()
            ->assertHeader('content-disposition', 'attachment; filename="user_import_template.csv"');
    }

    public function test_blacklisted_ip_is_denied_by_security_policy(): void
    {
        Cache::put('ip_blacklist', [
            ['ip' => '127.0.0.1', 'reason' => 'Test block'],
        ]);

        $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
            ->get(route('login'))
            ->assertForbidden();
    }

    public function test_whitelist_enforcement_blocks_non_whitelisted_ip(): void
    {
        Cache::put('auth_settings', array_merge(
            SecuritySettings::authSettingsDefaults(),
            ['enable_ip_whitelist' => true]
        ));
        Cache::put('ip_whitelist', [
            ['ip' => '10.0.0.1', 'description' => 'Allowed network'],
        ]);

        $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
            ->get(route('login'))
            ->assertForbidden();
    }

    public function test_session_timeout_logs_out_authenticated_user(): void
    {
        $user = $this->makeUser('admin');

        Cache::put('auth_settings', [
            'session_timeout' => 5,
        ]);

        $this->actingAs($user, 'user')
            ->withSession([
                'last_activity_at' => now()->subMinutes(10)->timestamp,
            ])
            ->get(route('users.download-template'))
            ->assertRedirect(route('login'));

        $this->assertGuest('user');
    }

    private function makeUser(string $role): User
    {
        $user = new User();
        $user->id = 999;
        $user->first_name = 'Test';
        $user->last_name = 'User';
        $user->email = 'test@example.com';
        $user->role = $role;
        $user->is_active = true;

        return $user;
    }
}
