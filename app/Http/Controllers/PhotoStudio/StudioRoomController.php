<?php

namespace App\Http\Controllers\PhotoStudio;

use App\Http\Controllers\Controller;
use App\Models\StudioRoom;
use App\Models\StudioCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudioRoomController extends Controller
{
    /**
     * Display a listing of rooms
     */
    public function index(Request $request)
    {
        $user = Auth::guard('user')->user();
        
        $categoryId = $request->get('category_id');
        $status = $request->get('status');
        
        $rooms = StudioRoom::with('category')
                          ->when($categoryId, function($query, $categoryId) {
                              $query->where('category_id', $categoryId);
                          })
                          ->when($status, function($query, $status) {
                              $query->where('status', $status);
                          })
                          ->orderBy('category_id')
                          ->orderBy('name')
                          ->get();
        
        $categories = StudioCategory::active()->ordered()->get();
        
        return view('pages.photo-studio.rooms.index', compact(
            'user',
            'rooms',
            'categories',
            'categoryId',
            'status'
        ));
    }

    /**
     * Show the form for creating a new room
     */
    public function create()
    {
        $user = Auth::guard('user')->user();
        $categories = StudioCategory::active()->ordered()->get();
        
        return view('pages.photo-studio.rooms.create', compact('user', 'categories'));
    }

    /**
     * Store a newly created room
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:studio_categories,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:studio_rooms,code',
            'description' => 'nullable|string',
            'floor' => 'nullable|integer|min:0|max:100',
            'location' => 'nullable|string|max:255',
            'size_sqm' => 'nullable|integer|min:1',
            'equipment' => 'nullable|array',
            'equipment.*' => 'string|max:255',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'status' => 'nullable|in:available,maintenance,out_of_service',
            'is_active' => 'nullable|boolean',
        ]);

        // Filter out empty equipment and features
        if (isset($validated['equipment'])) {
            $validated['equipment'] = array_filter($validated['equipment'], function($item) {
                return !empty($item);
            });
            $validated['equipment'] = array_values($validated['equipment']);
        }

        if (isset($validated['features'])) {
            $validated['features'] = array_filter($validated['features'], function($item) {
                return !empty($item);
            });
            $validated['features'] = array_values($validated['features']);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['status'] = $validated['status'] ?? 'available';

        $room = StudioRoom::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Room created successfully',
                'room' => $room->load('category'),
            ]);
        }

        return redirect()
            ->route('photo-studio.rooms.index')
            ->with('success', 'Room created successfully');
    }

    /**
     * Display the specified room
     */
    public function show($id)
    {
        $user = Auth::guard('user')->user();
        $room = StudioRoom::with('category')->findOrFail($id);
        
        return view('pages.photo-studio.rooms.show', compact('user', 'room'));
    }

    /**
     * Show the form for editing the specified room (AJAX)
     */
    public function edit(Request $request, $id)
    {
        $room = StudioRoom::with('category')->findOrFail($id);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'room' => $room,
            ]);
        }

        $user = Auth::guard('user')->user();
        $categories = StudioCategory::active()->ordered()->get();
        return view('pages.photo-studio.rooms.edit', compact('user', 'room', 'categories'));
    }

    /**
     * Update the specified room
     */
    public function update(Request $request, $id)
    {
        $room = StudioRoom::findOrFail($id);
        
        $validated = $request->validate([
            'category_id' => 'required|exists:studio_categories,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:studio_rooms,code,' . $id,
            'description' => 'nullable|string',
            'floor' => 'nullable|integer|min:0|max:100',
            'location' => 'nullable|string|max:255',
            'size_sqm' => 'nullable|integer|min:1',
            'equipment' => 'nullable|array',
            'equipment.*' => 'string|max:255',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'status' => 'nullable|in:available,maintenance,out_of_service',
            'maintenance_notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        // Filter out empty equipment and features
        if (isset($validated['equipment'])) {
            $validated['equipment'] = array_filter($validated['equipment'], function($item) {
                return !empty($item);
            });
            $validated['equipment'] = array_values($validated['equipment']);
        }

        if (isset($validated['features'])) {
            $validated['features'] = array_filter($validated['features'], function($item) {
                return !empty($item);
            });
            $validated['features'] = array_values($validated['features']);
        }

        $validated['is_active'] = $request->has('is_active');

        $room->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Room updated successfully',
                'room' => $room->fresh()->load('category'),
            ]);
        }

        return redirect()
            ->route('photo-studio.rooms.show', $id)
            ->with('success', 'Room updated successfully');
    }

    /**
     * Remove the specified room
     */
    public function destroy($id)
    {
        $room = StudioRoom::findOrFail($id);
        $room->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Room deleted successfully',
        ]);
    }

    /**
     * Update room status
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,maintenance,out_of_service',
            'notes' => 'nullable|string',
        ]);

        $room = StudioRoom::findOrFail($id);
        
        $updateData = ['status' => $validated['status']];
        
        if ($validated['status'] === 'available') {
            $updateData['maintenance_notes'] = null;
        } else if (!empty($validated['notes'])) {
            $updateData['maintenance_notes'] = $validated['notes'];
        }

        $room->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Room status updated successfully',
            'room' => $room->fresh(),
        ]);
    }

    /**
     * Mark room for maintenance
     */
    public function markMaintenance(Request $request, $id)
    {
        $validated = $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $room = StudioRoom::findOrFail($id);
        $room->markMaintenance($validated['notes']);

        return response()->json([
            'success' => true,
            'message' => 'Room marked for maintenance',
            'room' => $room->fresh(),
        ]);
    }

    /**
     * Mark room as available
     */
    public function markAvailable($id)
    {
        $room = StudioRoom::findOrFail($id);
        $room->markAvailable();

        return response()->json([
            'success' => true,
            'message' => 'Room marked as available',
            'room' => $room->fresh(),
        ]);
    }

    /**
     * Mark room as out of service
     */
    public function markOutOfService(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $room = StudioRoom::findOrFail($id);
        $room->markOutOfService($validated['reason']);

        return response()->json([
            'success' => true,
            'message' => 'Room marked as out of service',
            'room' => $room->fresh(),
        ]);
    }

    /**
     * Get rooms by category (AJAX)
     */
    public function getByCategory($categoryId)
    {
        $rooms = StudioRoom::forCategory($categoryId)
                          ->active()
                          ->orderBy('name')
                          ->get();

        return response()->json([
            'success' => true,
            'rooms' => $rooms,
        ]);
    }

    /**
     * Get available rooms by category (AJAX)
     */
    public function getAvailableByCategory($categoryId)
    {
        $rooms = StudioRoom::forCategory($categoryId)
                          ->available()
                          ->orderBy('name')
                          ->get();

        return response()->json([
            'success' => true,
            'rooms' => $rooms,
        ]);
    }

    /**
     * Add equipment to room
     */
    public function addEquipment(Request $request, $id)
    {
        $validated = $request->validate([
            'equipment_name' => 'required|string|max:255',
        ]);

        $room = StudioRoom::findOrFail($id);
        $added = $room->addEquipment($validated['equipment_name']);

        if ($added) {
            return response()->json([
                'success' => true,
                'message' => 'Equipment added successfully',
                'equipment' => $room->fresh()->getEquipmentList(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Equipment already exists',
        ], 422);
    }

    /**
     * Remove equipment from room
     */
    public function removeEquipment(Request $request, $id)
    {
        $validated = $request->validate([
            'equipment_name' => 'required|string|max:255',
        ]);

        $room = StudioRoom::findOrFail($id);
        $removed = $room->removeEquipment($validated['equipment_name']);

        if ($removed) {
            return response()->json([
                'success' => true,
                'message' => 'Equipment removed successfully',
                'equipment' => $room->fresh()->getEquipmentList(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Equipment not found',
        ], 422);
    }

    /**
     * Export rooms to CSV
     */
    public function export()
    {
        $rooms = StudioRoom::with('category')->orderBy('category_id')->orderBy('name')->get();
        
        $filename = 'studio-rooms-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($rooms) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'ID',
                'Category',
                'Name',
                'Code',
                'Floor',
                'Location',
                'Size (sqm)',
                'Equipment Count',
                'Status',
                'Is Active',
            ]);
            
            foreach ($rooms as $room) {
                fputcsv($file, [
                    $room->id,
                    $room->category->name,
                    $room->name,
                    $room->code,
                    $room->floor_display ?? 'N/A',
                    $room->location ?? 'N/A',
                    $room->size_sqm ?? 'N/A',
                    count($room->getEquipmentList()),
                    $room->status_label,
                    $room->is_active ? 'Yes' : 'No',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
