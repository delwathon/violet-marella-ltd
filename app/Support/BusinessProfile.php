<?php

namespace App\Support;

use App\Models\Business;
use App\Models\Setting;
use Throwable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class BusinessProfile
{
    private const COMPANY_CACHE_KEY = 'business_profile.company';
    private const DIRECTORY_CACHE_KEY = 'business_profile.directory';

    /**
     * @return array<string, string>
     */
    public static function company(): array
    {
        return Cache::remember(self::COMPANY_CACHE_KEY, 3600, function (): array {
            try {
                if (!Schema::hasTable('settings')) {
                    return self::defaultCompany();
                }
            } catch (Throwable $exception) {
                return self::defaultCompany();
            }

            $fallback = self::defaultCompany();

            return [
                'name' => (string) Setting::get('store_name', $fallback['name']),
                'legal_name' => (string) Setting::get('store_legal_name', $fallback['legal_name']),
                'phone' => (string) Setting::get('store_phone', $fallback['phone']),
                'email' => (string) Setting::get('store_email', $fallback['email']),
                'address' => (string) Setting::get('store_address', $fallback['address']),
                'rc_number' => (string) Setting::get('store_rc_number', $fallback['rc_number']),
                'website' => (string) Setting::get('store_website', $fallback['website']),
                'currency' => (string) Setting::get('store_currency', $fallback['currency']),
                'currency_symbol' => (string) Setting::get('store_currency_symbol', $fallback['currency_symbol']),
            ];
        });
    }

    public static function clearCache(): void
    {
        Cache::forget(self::COMPANY_CACHE_KEY);
        Cache::forget(self::DIRECTORY_CACHE_KEY);
    }

    /**
     * @return Collection<string, Business>
     */
    public static function businesses(): Collection
    {
        return Cache::remember(self::DIRECTORY_CACHE_KEY, 3600, function (): Collection {
            try {
                if (!Schema::hasTable('businesses')) {
                    return collect();
                }

                return Business::query()
                    ->orderBy('name')
                    ->get()
                    ->keyBy('slug');
            } catch (Throwable $exception) {
                return collect();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public static function forSlug(?string $slug): array
    {
        $company = self::company();
        $business = $slug ? self::businesses()->get($slug) : null;

        if (!$business) {
            return [
                'slug' => (string) ($slug ?? ''),
                'name' => $company['name'],
                'legal_name' => $company['legal_name'],
                'phone' => $company['phone'],
                'email' => $company['email'],
                'address' => $company['address'],
                'rc_number' => $company['rc_number'],
                'website' => $company['website'],
                'tax_id' => '',
                'contact_person' => '',
            ];
        }

        return [
            'slug' => (string) $business->slug,
            'name' => (string) ($business->name ?: $company['name']),
            'legal_name' => (string) ($business->legal_name ?: $company['legal_name']),
            'phone' => (string) ($business->phone ?: $company['phone']),
            'email' => (string) ($business->email ?: $company['email']),
            'address' => (string) ($business->address ?: $company['address']),
            'rc_number' => (string) ($business->rc_number ?: $company['rc_number']),
            'website' => (string) ($business->website ?: $company['website']),
            'tax_id' => (string) ($business->tax_id ?? ''),
            'contact_person' => (string) ($business->contact_person ?? ''),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function defaultCompany(): array
    {
        return [
            'name' => 'Violet Marella Limited',
            'legal_name' => 'Violet Marella Limited',
            'phone' => '',
            'email' => '',
            'address' => '',
            'rc_number' => '',
            'website' => '',
            'currency' => 'NGN',
            'currency_symbol' => '₦',
        ];
    }
}
