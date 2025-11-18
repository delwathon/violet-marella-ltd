<?php

namespace App\Http\Controllers\PhotoStudio;

use App\Http\Controllers\Controller;
use App\Models\Studio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudioManagementController extends Controller
{
    /**
     * Display a listing of studios
     */
    public function index()
    {
        $user = Auth::guard('user')->user();
        $studios = Studio::with(['activeSession'])->get();
        
        return view('pages.photo-studio.studios.index', compact('user', 'studios'));
    }

    /**
     * Show the form for creating a new studio
     */
    public function create()
    {
        $user = Auth::guard('user')->user();
        return view('pages.photo-studio.studios.create', compact('user'));
    }

    /**
     * Store a newly created studio
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:studios,code',
            'description' => 'nullable|string',
            'studio_rate_id' => 'required|exists:studio_rates,id',
            'capacity' => 'required|integer|min:1',
            'equipment' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        // Process equipment array
        if (isset($validated['equipment'])) {
            $validated['equipment'] = array_filter($validated['equipment'], function($item) {
                return !empty($item);
            });
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['status'] = 'available';

        $studio = Studio::create($validated);

        return redirect()
            ->route('photo-studio.studios.index')
            ->with('success', 'Studio created successfully');
    }

    /**
     * Show the form for editing the specified studio
     */
    public function edit($id)
    {
        $studio = Studio::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'studio' => $studio
        ]);
    }

    /**
     * Update the specified studio
     */
    public function update(Request $request, $id)
    {
        $studio = Studio::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:studios,code,' . $id,
            'description' => 'nullable|string',
            'studio_rate_id' => 'required|exists:studio_rates,id',
            'capacity' => 'required|integer|min:1',
            'equipment' => 'nullable|array',
            'is_active' => 'nullable',
        ]);

        // Process equipment array
        if (isset($validated['equipment'])) {
            $validated['equipment'] = array_filter($validated['equipment'], function($item) {
                return !empty($item);
            });
        }

        // Handle is_active checkbox
        if ($request->has('is_active')) {
            $validated['is_active'] = $request->is_active ? true : false;
        } else {
            $validated['is_active'] = false;
        }

        $studio->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Studio updated successfully',
                'studio' => $studio
            ]);
        }

        return redirect()
            ->route('photo-studio.studios.index')
            ->with('success', 'Studio updated successfully');
    }

    /**
     * Update studio status
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,occupied,maintenance',
        ]);

        $studio = Studio::findOrFail($id);
        
        // Don't allow setting to occupied if there's no active session
        if ($validated['status'] === 'occupied' && !$studio->activeSession) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot set studio to occupied without an active session'
            ], 422);
        }

        $studio->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Studio status updated successfully',
            'studio' => $studio
        ]);
    }

    /**
     * Remove the specified studio
     */
    public function destroy($id)
    {
        $studio = Studio::findOrFail($id);
        
        // Check if studio has active sessions
        if ($studio->activeSession) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete studio with active session'
            ], 422);
        }

        $studio->delete();

        return response()->json([
            'success' => true,
            'message' => 'Studio deleted successfully'
        ]);
    }
}