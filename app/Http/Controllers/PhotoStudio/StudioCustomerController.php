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
        
        $customers = StudioCustomer::query()
            ->when($search, function($query, $search) {
                $query->search($search);
            })
            ->orderBy('name')
            ->paginate(20);
        
        return view('pages.photo-studio.customers.index', compact('user', 'customers', 'search'));
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
        $totalRevenue = $customer->sessions()
            ->where('payment_status', 'paid')
            ->sum('total_amount');
        
        $completedSessions = $customer->sessions()
            ->where('status', 'completed')
            ->count();
        
        $totalMinutes = $customer->sessions()
            ->where('status', 'completed')
            ->sum('actual_duration');
        
        return view('pages.photo-studio.customers.show', compact(
            'user', 
            'customer', 
            'totalRevenue', 
            'completedSessions', 
            'totalMinutes'
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
            'is_active' => 'nullable|boolean',
        ]);

        // Handle is_active checkbox
        if ($request->has('is_active')) {
            $validated['is_active'] = $request->is_active ? true : false;
        }

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
        
        // Check if customer has active sessions
        if ($customer->activeSession) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete customer with active session'
            ], 422);
        }
        
        $customer->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully'
        ]);
    }

    /**
     * Export customers to CSV
     */
    public function export()
    {
        $customers = StudioCustomer::orderBy('name')->get();
        
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
                'Last Visit',
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
                    $customer->last_visit ? $customer->last_visit->format('Y-m-d H:i:s') : '',
                    $customer->is_active ? 'Active' : 'Inactive',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}