<?php

namespace App\Http\Controllers\PhotoStudio;

use App\Http\Controllers\Controller;
use App\Models\StudioRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudioRateController extends Controller
{
    /**
     * Display a listing of rates
     */
    public function index()
    {
        $user = Auth::guard('user')->user();
        $rates = StudioRate::orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('pages.photo-studio.rates.index', compact('user', 'rates'));
    }

    /**
     * Get rates list for dropdown (AJAX)
     */
    public function getRatesList()
    {
        $rates = StudioRate::active()->orderBy('is_default', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'rates' => $rates
        ]);
    }

    /**
     * Show the form for creating a new rate
     */
    public function create()
    {
        $user = Auth::guard('user')->user();
        return view('pages.photo-studio.rates.create', compact('user'));
    }

    /**
     * Store a newly created rate
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'base_time' => 'required|integer|min:1',
            'base_amount' => 'required|numeric|min:0',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_default'] = $request->has('is_default');
        $validated['is_active'] = $request->has('is_active');

        $rate = StudioRate::create($validated);

        return redirect()
            ->route('photo-studio.rates.index')
            ->with('success', 'Rate created successfully');
    }

    /**
     * Show the form for editing the specified rate
     */
    public function edit($id)
    {
        $rate = StudioRate::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'rate' => $rate
        ]);
    }

    /**
     * Update the specified rate
     */
    public function update(Request $request, $id)
    {
        $rate = StudioRate::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'base_time' => 'required|integer|min:1',
            'base_amount' => 'required|numeric|min:0',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_default'] = $request->has('is_default');
        $validated['is_active'] = $request->has('is_active');

        $rate->update($validated);

        return redirect()
            ->route('photo-studio.rates.index')
            ->with('success', 'Rate updated successfully');
    }

    /**
     * Set rate as default
     */
    public function setDefault($id)
    {
        $rate = StudioRate::findOrFail($id);
        
        // Unset all default rates
        StudioRate::where('is_default', true)->update(['is_default' => false]);
        
        // Set this as default
        $rate->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Default rate updated successfully'
        ]);
    }

    /**
     * Remove the specified rate
     */
    public function destroy($id)
    {
        $rate = StudioRate::findOrFail($id);
        
        if ($rate->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete default rate'
            ], 422);
        }
        
        $rate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rate deleted successfully'
        ]);
    }
}