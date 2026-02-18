<?php

namespace App\Http\Controllers\PhotoStudio;

use App\Http\Controllers\Controller;
use App\Models\StudioCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudioCustomerController extends Controller
{
    /**
     * Display a listing of customers
     */
    public function index(Request $request)
    {
        $user = Auth::guard('user')->user();
        $search = $request->get('search');
        $filter = $request->get('filter'); // all, active, blacklisted, vip, new, recent
        
        $customers = StudioCustomer::query()
            ->when($search, function($query, $search) {
                $query->search($search);
            })
            ->when($filter === 'active', function($query) {
                $query->active();
            })
            ->when($filter === 'blacklisted', function($query) {
                $query->blacklisted();
            })
            ->when($filter === 'vip', function($query) {
                $query->vip(100000);
            })
            ->when($filter === 'new', function($query) {
                $query->where('total_sessions', '<', 3);
            })
            ->when($filter === 'recent', function($query) {
                $query->recent();
            })
            ->orderBy('total_spent', 'desc')
            ->paginate(20);
        
        return view('pages.photo-studio.customers.index', compact('user', 'customers', 'search', 'filter'));
    }

    /**
     * Show the form for creating a new customer
     */
    public function create()
    {
        $user = Auth::guard('user')->user();
        return view('pages.photo-studio.customers.create', compact('user'));
    }

    /**
     * Store a newly created customer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:studio_customers,email',
            'phone' => 'required|string|max:20|unique:studio_customers,phone',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'notes' => 'nullable|string',
            'preferences' => 'nullable|array',
        ]);

        $customer = StudioCustomer::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'customer' => $customer
            ]);
        }

        return redirect()
            ->route('photo-studio.customers.index')
            ->with('success', 'Customer created successfully');
    }

    /**
     * Display the specified customer
     */
    public function show($id)
    {
        $user = Auth::guard('user')->user();
        $customer = StudioCustomer::with(['sessions' => function($query) {
            $query->orderBy('check_in_time', 'desc')->limit(10);
        }])->findOrFail($id);
        
        // Get statistics
        $totalRevenue = $customer->completedSessions()
                                 ->where('payment_status', 'paid')
                                 ->sum('total_amount');
        
        $completedSessions = $customer->completedSessions()->count();
        
        $totalMinutes = $customer->completedSessions()
                                 ->sum('actual_duration');
        
        $averageSessionDuration = $customer->averageSessionDuration();
        $averageSpending = $customer->averageSpending();
        $favoriteCategory = $customer->favoriteCategory();
        $daysSinceLastVisit = $customer->daysSinceLastVisit();
        
        return view('pages.photo-studio.customers.show', compact(
            'user', 
            'customer', 
            'totalRevenue', 
            'completedSessions', 
            'totalMinutes',
            'averageSessionDuration',
            'averageSpending',
            'favoriteCategory',
            'daysSinceLastVisit'
        ));
    }

    /**
     * Show the form for editing the specified customer (AJAX)
     */
    public function edit($id)
    {
        $customer = StudioCustomer::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'customer' => $customer
        ]);
    }

    /**
     * Update the specified customer
     */
    public function update(Request $request, $id)
    {
        $customer = StudioCustomer::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:studio_customers,email,' . $id,
            'phone' => 'required|string|max:20|unique:studio_customers,phone,' . $id,
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'notes' => 'nullable|string',
            'preferences' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $customer->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully',
                'customer' => $customer
            ]);
        }

        return redirect()
            ->route('photo-studio.customers.show', $id)
            ->with('success', 'Customer updated successfully');
    }

    /**
     * Remove the specified customer
     */
    public function destroy($id)
    {
        $customer = StudioCustomer::findOrFail($id);
        
        // Check if customer has active session
        if ($customer->hasActiveSession()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete customer with active session'
            ], 422);
        }
        
        // Check if customer has sessions
        if ($customer->sessions()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete customer with existing sessions. Deactivate instead.'
            ], 422);
        }
        
        $customer->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully'
        ]);
    }

    /**
     * Blacklist customer
     */
    public function blacklist(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $customer = StudioCustomer::findOrFail($id);
        
        if ($customer->blacklist($validated['reason'])) {
            return response()->json([
                'success' => true,
                'message' => 'Customer blacklisted successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to blacklist customer',
        ], 500);
    }

    /**
     * Remove from blacklist
     */
    public function removeFromBlacklist($id)
    {
        $customer = StudioCustomer::findOrFail($id);
        
        if ($customer->removeFromBlacklist()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer removed from blacklist successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to remove customer from blacklist',
        ], 500);
    }

    /**
     * Update customer statistics manually
     */
    public function updateStatistics($id)
    {
        $customer = StudioCustomer::findOrFail($id);
        $customer->updateStatistics();

        return response()->json([
            'success' => true,
            'message' => 'Customer statistics updated successfully',
            'customer' => $customer->fresh(),
        ]);
    }

    /**
     * Export customers to CSV
     */
    public function export()
    {
        $customers = StudioCustomer::orderBy('total_spent', 'desc')->get();
        
        $filename = 'studio-customers-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'ID',
                'Name',
                'Phone',
                'Email',
                'Address',
                'Date of Birth',
                'Total Sessions',
                'Total Spent',
                'Average Spending',
                'Last Visit',
                'Days Since Last Visit',
                'Tier',
                'Status',
            ]);
            
            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->id,
                    $customer->name,
                    $customer->phone,
                    $customer->email,
                    $customer->address,
                    $customer->date_of_birth ? $customer->date_of_birth->format('Y-m-d') : '',
                    $customer->total_sessions,
                    $customer->total_spent,
                    $customer->averageSpending(),
                    $customer->last_visit ? $customer->last_visit->format('Y-m-d H:i:s') : '',
                    $customer->daysSinceLastVisit() ?? 'Never',
                    $customer->tier,
                    $customer->status_label,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}