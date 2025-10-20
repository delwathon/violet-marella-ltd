<?php

namespace App\Http\Controllers\PropRental;

use App\Http\Controllers\Controller;
use App\Models\RentalCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RentalCustomersController extends Controller
{
    /**
     * Show create customer form
     */
    public function create()
    {
        $user = Auth::guard('user')->user();
        return view('pages.prop-rental.customers.create', compact('user'));
    }

    /**
     * Store a new customer
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:rental_customers,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'id_number' => 'nullable|string|max:50',
        ]);

        try {
            RentalCustomer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'id_number' => $request->id_number,
                'status' => 'active',
            ]);

            return redirect()->route('prop-rental.index', ['tab' => 'customers'])
                ->with('success', 'Customer added successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add customer: ' . $e->getMessage());
        }
    }

    /**
     * Show customer details
     */
    public function show($id)
    {
        $user = Auth::guard('user')->user();
        $customer = RentalCustomer::with(['rentals' => function($query) {
            $query->with('prop')->latest()->limit(10);
        }])->findOrFail($id);

        return view('pages.prop-rental.customers.show', compact('user', 'customer'));
    }

    /**
     * Show edit customer form
     */
    public function edit($id)
    {
        $user = Auth::guard('user')->user();
        $customer = RentalCustomer::findOrFail($id);
        
        return view('pages.prop-rental.customers.edit', compact('user', 'customer'));
    }

    /**
     * Update customer
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:rental_customers,email,' . $id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'id_number' => 'nullable|string|max:50',
        ]);

        try {
            $customer = RentalCustomer::findOrFail($id);
            
            $customer->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'id_number' => $request->id_number,
            ]);

            return redirect()->route('prop-rental.index', ['tab' => 'customers'])
                ->with('success', 'Customer updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update customer: ' . $e->getMessage());
        }
    }

    /**
     * Show deactivate confirmation
     */
    public function showDeactivate($id)
    {
        $user = Auth::guard('user')->user();
        $customer = RentalCustomer::findOrFail($id);
        
        if ($customer->current_rentals > 0) {
            return redirect()->route('prop-rental.index', ['tab' => 'customers'])
                ->with('error', 'Cannot deactivate customer with active rentals.');
        }
        
        return view('pages.prop-rental.customers.deactivate', compact('user', 'customer'));
    }

    /**
     * Deactivate customer
     */
    public function deactivate($id)
    {
        try {
            $customer = RentalCustomer::findOrFail($id);

            if ($customer->current_rentals > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot deactivate customer with active rentals.');
            }

            $customer->update(['status' => 'inactive']);

            return redirect()->route('prop-rental.index', ['tab' => 'customers'])
                ->with('success', 'Customer deactivated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to deactivate customer: ' . $e->getMessage());
        }
    }

    /**
     * Activate customer
     */
    public function activate($id)
    {
        try {
            $customer = RentalCustomer::findOrFail($id);
            $customer->update(['status' => 'active']);

            return redirect()->route('prop-rental.index', ['tab' => 'customers'])
                ->with('success', 'Customer activated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to activate customer: ' . $e->getMessage());
        }
    }
}