<?php

namespace App\Http\Controllers\PhotoStudio;

use App\Http\Controllers\Controller;
use App\Models\StudioSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudioSettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $user = Auth::guard('user')->user();
        $settings = StudioSetting::getAll();
        
        return view('pages.photo-studio.settings.index', compact('user', 'settings'));
    }

    /**
     * Update offset time setting
     */
    public function updateOffsetTime(Request $request)
    {
        $validated = $request->validate([
            'offset_time' => 'required|integer|min:0|max:60',
        ]);

        StudioSetting::set('offset_time', $validated['offset_time'], 'integer');

        return response()->json([
            'success' => true,
            'message' => 'Offset time updated successfully',
            'value' => $validated['offset_time'],
        ]);
    }

    /**
     * Update default base time
     */
    public function updateDefaultBaseTime(Request $request)
    {
        $validated = $request->validate([
            'default_base_time' => 'required|integer|min:10|max:240',
        ]);

        StudioSetting::set('default_base_time', $validated['default_base_time'], 'integer');

        return response()->json([
            'success' => true,
            'message' => 'Default base time updated successfully',
            'value' => $validated['default_base_time'],
        ]);
    }

    /**
     * Update default base price
     */
    public function updateDefaultBasePrice(Request $request)
    {
        $validated = $request->validate([
            'default_base_price' => 'required|numeric|min:0',
        ]);

        StudioSetting::set('default_base_price', $validated['default_base_price'], 'integer');

        return response()->json([
            'success' => true,
            'message' => 'Default base price updated successfully',
            'value' => $validated['default_base_price'],
        ]);
    }

    /**
     * Update allow overtime setting
     */
    public function updateAllowOvertime(Request $request)
    {
        $validated = $request->validate([
            'allow_overtime' => 'required|boolean',
        ]);

        StudioSetting::set('allow_overtime', $validated['allow_overtime'], 'boolean');

        return response()->json([
            'success' => true,
            'message' => 'Overtime setting updated successfully',
            'value' => $validated['allow_overtime'],
        ]);
    }

    /**
     * Update a specific setting
     */
    public function updateSetting(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string',
            'value' => 'required',
            'type' => 'required|in:string,integer,boolean,float,json,array',
        ]);

        StudioSetting::set(
            $validated['key'],
            $validated['value'],
            $validated['type']
        );

        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully',
        ]);
    }

    /**
     * Get a setting value (AJAX)
     */
    public function getSetting($key)
    {
        $setting = StudioSetting::where('key', $key)->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'key' => $setting->key,
            'value' => StudioSetting::get($key),
            'type' => $setting->type,
            'description' => $setting->description,
        ]);
    }

    /**
     * Get all settings (AJAX)
     */
    public function getAllSettings()
    {
        $settings = StudioSetting::getAll();

        return response()->json([
            'success' => true,
            'settings' => $settings,
        ]);
    }

    /**
     * Reset settings to default
     */
    public function resetToDefaults()
    {
        StudioSetting::set('offset_time', 10, 'integer');
        StudioSetting::set('default_base_time', 30, 'integer');
        StudioSetting::set('default_base_price', 30000, 'integer');
        StudioSetting::set('allow_overtime', true, 'boolean');
        StudioSetting::set('currency_symbol', '₦', 'string');

        return response()->json([
            'success' => true,
            'message' => 'Settings reset to defaults',
        ]);
    }

    /**
     * Clear settings cache
     */
    public function clearCache()
    {
        StudioSetting::clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Settings cache cleared successfully',
        ]);
    }
}