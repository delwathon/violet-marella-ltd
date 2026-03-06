<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Setting;
use App\Support\BusinessProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $user = Auth::guard('user')->user();
        $businesses = Business::query()->orderBy('name')->get();

        $settings = [
            'store_name' => Setting::get('store_name', 'Violet Marella Limited'),
            'store_legal_name' => Setting::get('store_legal_name', Setting::get('store_name', 'Violet Marella Limited')),
            'store_address' => Setting::get('store_address', ''),
            'store_phone' => Setting::get('store_phone', ''),
            'store_email' => Setting::get('store_email', ''),
            'store_rc_number' => Setting::get('store_rc_number', ''),
            'store_website' => Setting::get('store_website', ''),
            'store_currency' => Setting::get('store_currency', 'NGN'),
            'store_currency_symbol' => Setting::get('store_currency_symbol', '₦'),
            'default_tax_rate' => Setting::get('default_tax_rate', 7.5),
            'tax_inclusive_pricing' => Setting::get('tax_inclusive_pricing', false),
            'accepted_payment_methods' => Setting::get('accepted_payment_methods', ['cash', 'card', 'transfer']),
            'minimum_cash_drawer_amount' => Setting::get('minimum_cash_drawer_amount', 5000),
            'receipt_footer_message' => Setting::get('receipt_footer_message', 'Thank you for shopping with us!'),
            'receipt_show_tax_breakdown' => Setting::get('receipt_show_tax_breakdown', true),
            'low_stock_threshold' => Setting::get('low_stock_threshold', 10),
            'track_inventory' => Setting::get('track_inventory', true),
            'loyalty_points_enabled' => Setting::get('loyalty_points_enabled', true),
            'loyalty_points_rate' => Setting::get('loyalty_points_rate', 1),
            'loyalty_discount_rate' => Setting::get('loyalty_discount_rate', 5),
            'backup_enabled' => Setting::get('backup_enabled', true),
            'auto_logout_minutes' => Setting::get('auto_logout_minutes', 30),
        ];

        return view('pages.settings', compact('user', 'settings', 'businesses'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_legal_name' => 'nullable|string|max:255',
            'store_address' => 'nullable|string|max:500',
            'store_phone' => 'nullable|string|max:30',
            'store_email' => 'nullable|email|max:255',
            'store_rc_number' => 'nullable|string|max:120',
            'store_website' => 'nullable|string|max:255',
            'store_currency' => 'required|string|max:10',
            'store_currency_symbol' => 'required|string|max:10',
            'default_tax_rate' => 'required|numeric|min:0|max:100',
            'minimum_cash_drawer_amount' => 'required|numeric|min:0',
            'accepted_payment_methods' => 'nullable|array',
            'accepted_payment_methods.*' => 'in:cash,card,transfer,mobile_money,split',
            'receipt_footer_message' => 'nullable|string|max:500',
            'low_stock_threshold' => 'required|integer|min:0',
            'loyalty_points_rate' => 'required|numeric|min:0',
            'loyalty_discount_rate' => 'required|numeric|min:0|max:100',
            'auto_logout_minutes' => 'required|integer|min:5|max:1440',
            'business_profiles' => 'nullable|array',
            'business_profiles.*.id' => 'required|integer|exists:businesses,id',
            'business_profiles.*.name' => 'required|string|max:255',
            'business_profiles.*.legal_name' => 'nullable|string|max:255',
            'business_profiles.*.phone' => 'nullable|string|max:30',
            'business_profiles.*.email' => 'nullable|email|max:255',
            'business_profiles.*.address' => 'nullable|string|max:500',
            'business_profiles.*.rc_number' => 'nullable|string|max:120',
            'business_profiles.*.website' => 'nullable|string|max:255',
            'business_profiles.*.tax_id' => 'nullable|string|max:120',
            'business_profiles.*.contact_person' => 'nullable|string|max:255',
            'business_profiles.*.description' => 'nullable|string|max:1000',
        ]);

        Setting::set('store_name', $validated['store_name'], 'string', 'store', 'Name of the store', true);
        Setting::set('store_legal_name', $validated['store_legal_name'] ?? $validated['store_name'], 'string', 'store', 'Legal business name', true);
        Setting::set('store_address', $validated['store_address'] ?? '', 'string', 'store', 'Store address', true);
        Setting::set('store_phone', $validated['store_phone'] ?? '', 'string', 'store', 'Store phone number', true);
        Setting::set('store_email', $validated['store_email'] ?? '', 'string', 'store', 'Store email address', true);
        Setting::set('store_rc_number', $validated['store_rc_number'] ?? '', 'string', 'store', 'Company registration number', true);
        Setting::set('store_website', $validated['store_website'] ?? '', 'string', 'store', 'Company website', true);
        Setting::set('store_currency', $validated['store_currency'], 'string', 'store', 'Store currency code', true);
        Setting::set('store_currency_symbol', $validated['store_currency_symbol'], 'string', 'store', 'Store currency symbol', true);

        Setting::set('default_tax_rate', (string) $validated['default_tax_rate'], 'float', 'tax', 'Default tax rate percentage', false);
        Setting::set('tax_inclusive_pricing', $request->boolean('tax_inclusive_pricing') ? '1' : '0', 'boolean', 'tax', 'Whether prices include tax', false);

        Setting::set('accepted_payment_methods', json_encode($validated['accepted_payment_methods'] ?? []), 'json', 'payment', 'Accepted payment methods', false);
        Setting::set('minimum_cash_drawer_amount', (string) $validated['minimum_cash_drawer_amount'], 'float', 'payment', 'Minimum cash drawer amount', false);

        Setting::set('receipt_footer_message', $validated['receipt_footer_message'] ?? '', 'string', 'receipt', 'Footer message on receipts', true);
        Setting::set('receipt_show_tax_breakdown', $request->boolean('receipt_show_tax_breakdown') ? '1' : '0', 'boolean', 'receipt', 'Show tax breakdown on receipts', false);

        Setting::set('low_stock_threshold', (string) $validated['low_stock_threshold'], 'integer', 'inventory', 'Default low stock threshold', false);
        Setting::set('track_inventory', $request->boolean('track_inventory') ? '1' : '0', 'boolean', 'inventory', 'Enable inventory tracking', false);

        Setting::set('loyalty_points_enabled', $request->boolean('loyalty_points_enabled') ? '1' : '0', 'boolean', 'loyalty', 'Enable loyalty points system', false);
        Setting::set('loyalty_points_rate', (string) $validated['loyalty_points_rate'], 'float', 'loyalty', 'Points earned per currency unit spent', false);
        Setting::set('loyalty_discount_rate', (string) $validated['loyalty_discount_rate'], 'float', 'loyalty', 'Discount percentage for loyalty customers', false);

        Setting::set('backup_enabled', $request->boolean('backup_enabled') ? '1' : '0', 'boolean', 'system', 'Enable automatic backups', false);
        Setting::set('auto_logout_minutes', (string) $validated['auto_logout_minutes'], 'integer', 'system', 'Auto logout after inactivity (minutes)', false);

        foreach ($validated['business_profiles'] ?? [] as $profile) {
            $business = Business::query()->find($profile['id']);

            if (!$business) {
                continue;
            }

            $business->update([
                'name' => $profile['name'],
                'legal_name' => $profile['legal_name'] ?? null,
                'phone' => $profile['phone'] ?? null,
                'email' => $profile['email'] ?? null,
                'address' => $profile['address'] ?? null,
                'rc_number' => $profile['rc_number'] ?? null,
                'website' => $profile['website'] ?? null,
                'tax_id' => $profile['tax_id'] ?? null,
                'contact_person' => $profile['contact_person'] ?? null,
                'description' => $profile['description'] ?? $business->description,
            ]);
        }

        Setting::clearCache();
        BusinessProfile::clearCache();

        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}
