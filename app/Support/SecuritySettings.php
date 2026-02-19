<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SecuritySettings
{
    public static function passwordPolicyDefaults(): array
    {
        return [
            'min_length' => 8,
            'require_uppercase' => true,
            'require_lowercase' => true,
            'require_numbers' => true,
            'require_special' => false,
            'password_expiry' => 90,
            'password_history' => 5,
        ];
    }

    public static function authSettingsDefaults(): array
    {
        return [
            'enable_2fa' => false,
            'force_2fa_admins' => false,
            'session_timeout' => 120,
            'max_login_attempts' => 5,
            'lockout_duration' => 30,
            'enable_ip_whitelist' => false,
        ];
    }

    public static function auditLogDefaults(): array
    {
        return [
            'log_retention' => 365,
            'log_logins' => true,
            'log_changes' => true,
            'log_deletions' => true,
        ];
    }

    public static function passwordPolicy(): array
    {
        return self::loadArraySetting(
            'security_password_policy',
            'password_policy',
            self::passwordPolicyDefaults()
        );
    }

    public static function authSettings(): array
    {
        return self::loadArraySetting(
            'security_auth_settings',
            'auth_settings',
            self::authSettingsDefaults()
        );
    }

    public static function auditLogSettings(): array
    {
        return self::loadArraySetting(
            'security_audit_log_settings',
            'audit_log_settings',
            self::auditLogDefaults()
        );
    }

    public static function ipWhitelist(): array
    {
        return self::loadArraySetting('security_ip_whitelist', 'ip_whitelist', []);
    }

    public static function ipBlacklist(): array
    {
        return self::loadArraySetting('security_ip_blacklist', 'ip_blacklist', []);
    }

    public static function persistPasswordPolicy(array $settings): void
    {
        self::persistArraySetting(
            'security_password_policy',
            'password_policy',
            $settings,
            'Password policy configuration'
        );
    }

    public static function persistAuthSettings(array $settings): void
    {
        self::persistArraySetting(
            'security_auth_settings',
            'auth_settings',
            $settings,
            'Authentication and login security settings'
        );

        Cache::put('ip_whitelist_enabled', (bool) ($settings['enable_ip_whitelist'] ?? false), now()->addYears(1));
    }

    public static function persistAuditLogSettings(array $settings): void
    {
        self::persistArraySetting(
            'security_audit_log_settings',
            'audit_log_settings',
            $settings,
            'Audit logging preferences'
        );
    }

    public static function persistIpWhitelist(array $entries): void
    {
        self::persistArraySetting('security_ip_whitelist', 'ip_whitelist', array_values($entries), 'Whitelisted IP entries');
    }

    public static function persistIpBlacklist(array $entries): void
    {
        self::persistArraySetting('security_ip_blacklist', 'ip_blacklist', array_values($entries), 'Blacklisted IP entries');
    }

    private static function loadArraySetting(string $settingKey, string $cacheKey, array $defaults): array
    {
        $cached = Cache::get($cacheKey);
        if (is_array($cached)) {
            return self::mergeWithDefaults($cached, $defaults);
        }

        try {
            $stored = Setting::get($settingKey);
            if (is_array($stored)) {
                $value = self::mergeWithDefaults($stored, $defaults);
                Cache::put($cacheKey, $value, now()->addYears(1));

                if ($cacheKey === 'auth_settings') {
                    Cache::put('ip_whitelist_enabled', (bool) ($value['enable_ip_whitelist'] ?? false), now()->addYears(1));
                }

                return $value;
            }
        } catch (\Throwable $e) {
            // Fall back to defaults when the settings table/connection is unavailable.
        }

        Cache::put($cacheKey, $defaults, now()->addYears(1));

        if ($cacheKey === 'auth_settings') {
            Cache::put('ip_whitelist_enabled', (bool) ($defaults['enable_ip_whitelist'] ?? false), now()->addYears(1));
        }

        return $defaults;
    }

    private static function persistArraySetting(string $settingKey, string $cacheKey, array $value, string $description): void
    {
        $normalized = array_values($value) === $value ? array_values($value) : $value;

        try {
            Setting::set(
                $settingKey,
                json_encode($normalized),
                'json',
                'security',
                $description,
                false
            );
        } catch (\Throwable $e) {
            // Keep runtime behavior intact even when persistence temporarily fails.
        }

        Cache::put($cacheKey, $normalized, now()->addYears(1));
    }

    private static function mergeWithDefaults(array $value, array $defaults): array
    {
        if ($defaults === []) {
            return $value;
        }

        return array_merge($defaults, $value);
    }
}
