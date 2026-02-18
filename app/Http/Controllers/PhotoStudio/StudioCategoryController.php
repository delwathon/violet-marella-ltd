<?php

namespace App\Http\Controllers\PhotoStudio;

use App\Http\Controllers\Controller;
use App\Models\StudioCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StudioCategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index()
    {
        $user = Auth::guard('user')->user();
        $categories = StudioCategory::withCount(['rooms', 'activeSessions'])
                                    ->ordered()
                                    ->get();
        
        return view('pages.photo-studio.categories.index', compact('user', 'categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        $user = Auth::guard('user')->user();
        return view('pages.photo-studio.categories.create', compact('user'));
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:studio_categories,slug',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'base_time' => 'required|integer|min:10|max:240',
            'base_price' => 'required|numeric|min:0',
            'max_occupants' => 'required|integer|min:1|max:50',
            'max_concurrent_sessions' => 'required|integer|min:1|max:100',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $category = StudioCategory::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'category' => $category,
            ]);
        }

        return redirect()
            ->route('photo-studio.categories.index')
            ->with('success', 'Category created successfully');
    }

    /**
     * Display the specified category
     */
    public function show($id)
    {
        $user = Auth::guard('user')->user();
        $category = StudioCategory::withCount(['rooms', 'sessions', 'activeSessions'])
                                  ->findOrFail($id);
        
        // Get statistics
        $todayStats = $category->todayStats();
        $occupancyRate = $category->todayOccupancyRate();
        
        // Get recent sessions
        $recentSessions = $category->sessions()
                                   ->with(['customer'])
                                   ->orderBy('check_in_time', 'desc')
                                   ->limit(10)
                                   ->get();
        
        return view('pages.photo-studio.categories.show', compact(
            'user',
            'category',
            'todayStats',
            'occupancyRate',
            'recentSessions'
        ));
    }

    /**
     * Show the form for editing the specified category (AJAX)
     */
    public function edit($id)
    {
        $category = StudioCategory::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'category' => $category,
        ]);
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, $id)
    {
        $category = StudioCategory::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:studio_categories,slug,' . $id,
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'base_time' => 'required|integer|min:10|max:240',
            'base_price' => 'required|numeric|min:0',
            'max_occupants' => 'required|integer|min:1|max:50',
            'max_concurrent_sessions' => 'required|integer|min:1|max:100',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'category' => $category->fresh(),
            ]);
        }

        return redirect()
            ->route('photo-studio.categories.show', $id)
            ->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified category
     */
    public function destroy($id)
    {
        $category = StudioCategory::withCount('sessions')->findOrFail($id);
        
        // Check if category has sessions
        if ($category->sessions_count > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with existing sessions. Deactivate it instead.',
            ], 422);
        }
        
        $category->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
        ]);
    }

    /**
     * Get available categories (AJAX)
     */
    public function getAvailableCategories()
    {
        $categories = StudioCategory::active()
                                    ->ordered()
                                    ->get()
                                    ->map(function($category) {
                                        return [
                                            'id' => $category->id,
                                            'name' => $category->name,
                                            'slug' => $category->slug,
                                            'base_price' => $category->base_price,
                                            'formatted_base_price' => $category->formatted_base_price,
                                            'base_time' => $category->base_time,
                                            'max_occupants' => $category->max_occupants,
                                            'available_slots' => $category->availableSlots(),
                                            'can_accept_more' => $category->canAcceptMoreSessions(),
                                        ];
                                    });
        
        return response()->json([
            'success' => true,
            'categories' => $categories,
        ]);
    }

    /**
     * Get category details (AJAX)
     */
    public function getDetails($id)
    {
        $category = StudioCategory::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'base_time' => $category->base_time,
                'base_price' => $category->base_price,
                'per_minute_rate' => $category->per_minute_rate,
                'hourly_rate' => $category->hourly_rate,
                'max_occupants' => $category->max_occupants,
                'max_concurrent_sessions' => $category->max_concurrent_sessions,
                'available_slots' => $category->availableSlots(),
                'can_accept_more' => $category->canAcceptMoreSessions(),
                'pricing_breakdown' => $category->pricingBreakdown(),
            ],
        ]);
    }

    /**
     * Calculate price for duration (AJAX)
     */
    public function calculatePrice(Request $request, $id)
    {
        $validated = $request->validate([
            'duration' => 'required|integer|min:1',
        ]);

        $category = StudioCategory::findOrFail($id);
        $price = $category->calculatePrice($validated['duration']);

        return response()->json([
            'success' => true,
            'duration' => $validated['duration'],
            'price' => $price,
            'formatted_price' => '₦' . number_format($price, 2),
        ]);
    }

    /**
     * Get category statistics (AJAX)
     */
    public function getStatistics($id)
    {
        $category = StudioCategory::findOrFail($id);
        
        $todayStats = $category->todayStats();
        $occupancyRate = $category->todayOccupancyRate();

        return response()->json([
            'success' => true,
            'statistics' => [
                'today' => $todayStats,
                'occupancy_rate' => $occupancyRate,
                'active_rooms' => $category->activeRoomsCount(),
                'available_rooms' => $category->availableRoomsCount(),
                'has_rooms' => $category->hasRooms(),
            ],
        ]);
    }

    /**
     * Toggle category active status
     */
    public function toggleActive($id)
    {
        $category = StudioCategory::findOrFail($id);
        
        $category->is_active = !$category->is_active;
        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Category ' . ($category->is_active ? 'activated' : 'deactivated') . ' successfully',
            'is_active' => $category->is_active,
        ]);
    }

    /**
     * Update sort order (AJAX - for drag and drop)
     */
    public function updateSortOrder(Request $request)
    {
        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:studio_categories,id',
            'categories.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['categories'] as $categoryData) {
            StudioCategory::where('id', $categoryData['id'])
                         ->update(['sort_order' => $categoryData['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Category order updated successfully',
        ]);
    }

    /**
     * Duplicate a category
     */
    public function duplicate($id)
    {
        $original = StudioCategory::findOrFail($id);
        
        $duplicate = $original->replicate();
        $duplicate->name = $original->name . ' (Copy)';
        $duplicate->slug = Str::slug($duplicate->name);
        $duplicate->is_active = false;
        $duplicate->save();

        return response()->json([
            'success' => true,
            'message' => 'Category duplicated successfully',
            'category' => $duplicate,
        ]);
    }
}