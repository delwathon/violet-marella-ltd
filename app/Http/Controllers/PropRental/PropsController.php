<?php

namespace App\Http\Controllers\PropRental;

use App\Http\Controllers\Controller;
use App\Models\Prop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PropsController extends Controller
{
    /**
     * Show create prop form
     */
    public function create()
    {
        $user = Auth::guard('user')->user();
        return view('pages.prop-rental.props.create', compact('user'));
    }

    /**
     * Store a new prop
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:guitars,keyboards,drums,brass,strings',
            'type' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'daily_rate' => 'required|numeric|min:0',
            'condition' => 'required|in:excellent,good,fair,poor',
            'description' => 'nullable|string',
            'serial_number' => 'required|string|unique:props,serial_number',
            'purchase_date' => 'nullable|date',
        ]);

        try {
            Prop::create([
                'name' => $request->name,
                'category' => $request->category,
                'type' => $request->type,
                'brand' => $request->brand,
                'model' => $request->model,
                'daily_rate' => $request->daily_rate,
                'condition' => $request->condition,
                'description' => $request->description,
                'serial_number' => $request->serial_number,
                'purchase_date' => $request->purchase_date,
                'image' => $this->getCategoryIcon($request->category),
                'status' => 'available',
            ]);

            return redirect()->route('prop-rental.index')
                ->with('success', 'Prop added successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add prop: ' . $e->getMessage());
        }
    }

    /**
     * Show edit prop form
     */
    public function edit($id)
    {
        $user = Auth::guard('user')->user();
        $prop = Prop::findOrFail($id);
        
        return view('pages.prop-rental.props.edit', compact('user', 'prop'));
    }

    /**
     * Update prop
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:guitars,keyboards,drums,brass,strings',
            'type' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'daily_rate' => 'required|numeric|min:0',
            'condition' => 'required|in:excellent,good,fair,poor',
            'description' => 'nullable|string',
            'serial_number' => 'required|string|unique:props,serial_number,' . $id,
        ]);

        try {
            $prop = Prop::findOrFail($id);
            
            $prop->update([
                'name' => $request->name,
                'category' => $request->category,
                'type' => $request->type,
                'brand' => $request->brand,
                'model' => $request->model,
                'daily_rate' => $request->daily_rate,
                'condition' => $request->condition,
                'description' => $request->description,
                'serial_number' => $request->serial_number,
                'image' => $this->getCategoryIcon($request->category),
            ]);

            return redirect()->route('prop-rental.index')
                ->with('success', 'Prop updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update prop: ' . $e->getMessage());
        }
    }

    /**
     * Show delete confirmation
     */
    public function showDelete($id)
    {
        $user = Auth::guard('user')->user();
        $prop = Prop::findOrFail($id);
        
        if ($prop->status === 'rented') {
            return redirect()->route('prop-rental.index')
                ->with('error', 'Cannot delete a prop that is currently rented.');
        }
        
        return view('pages.prop-rental.props.delete', compact('user', 'prop'));
    }

    /**
     * Delete prop
     */
    public function destroy($id)
    {
        try {
            $prop = Prop::findOrFail($id);

            if ($prop->status === 'rented') {
                return redirect()->back()
                    ->with('error', 'Cannot delete a prop that is currently rented.');
            }

            $prop->delete();

            return redirect()->route('prop-rental.index')
                ->with('success', 'Prop deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete prop: ' . $e->getMessage());
        }
    }

    /**
     * Show maintenance form
     */
    public function showMaintenance($id)
    {
        $user = Auth::guard('user')->user();
        $prop = Prop::findOrFail($id);
        
        if ($prop->status === 'rented') {
            return redirect()->route('prop-rental.index')
                ->with('error', 'Cannot mark a rented prop for maintenance.');
        }
        
        return view('pages.prop-rental.props.maintenance', compact('user', 'prop'));
    }

    /**
     * Mark prop for maintenance
     */
    public function markMaintenance($id)
    {
        try {
            $prop = Prop::findOrFail($id);

            if ($prop->status === 'rented') {
                return redirect()->back()
                    ->with('error', 'Cannot mark a rented prop for maintenance.');
            }

            $prop->update([
                'status' => 'maintenance',
                'last_maintenance' => now(),
            ]);

            return redirect()->route('prop-rental.index')
                ->with('success', 'Prop marked for maintenance.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to mark prop for maintenance: ' . $e->getMessage());
        }
    }

    /**
     * Complete maintenance
     */
    public function completeMaintenance($id)
    {
        try {
            $prop = Prop::findOrFail($id);

            $prop->update([
                'status' => 'available',
                'last_maintenance' => now(),
            ]);

            return redirect()->route('prop-rental.index')
                ->with('success', 'Maintenance completed. Prop is now available.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to complete maintenance: ' . $e->getMessage());
        }
    }

    /**
     * Get category icon
     */
    private function getCategoryIcon($category)
    {
        return match($category) {
            'guitars' => 'fas fa-guitar',
            'keyboards' => 'fas fa-piano',
            'drums' => 'fas fa-drum',
            'brass' => 'fas fa-trumpet',
            'strings' => 'fas fa-violin',
            default => 'fas fa-music',
        };
    }
}