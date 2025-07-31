<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index()
    {
        $customers = Customer::where('is_deleted', false)->latest()->get();
        return view('module.customer.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('module.customer.create');
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:150',
                'address' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:150',
                'phone' => 'nullable|string|max:20',
                'credential' => 'nullable|string',
            ]);

            Customer::create($validated);

            return redirect()->route('data-customer.index')->with('success', 'Customer created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified customer.
     */
    public function show($id)
    {
        $customer = Customer::where('is_deleted', false)->findOrFail($id);
        return view('module.customer.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit($id)
    {
        $customer = Customer::where('is_deleted', false)->findOrFail($id);
        return view('module.customer.edit', compact('customer'));
    }

    /**
     * Update the specified customer.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:150',
                'address' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:150',
                'phone' => 'nullable|string|max:20',
                'credential' => 'nullable|string',
            ]);

            $customer = Customer::where('is_deleted', false)->findOrFail($id);
            $customer->update($validated);

            return redirect()->route('data-customer.index')->with('success', 'Customer updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Soft delete the specified customer.
     */
    public function destroy($id)
    {
        try {
            $customer = Customer::where('is_deleted', false)->findOrFail($id);
            $customer->update(['is_deleted' => true]);

            return redirect()->route('data-customer.index')->with('success', 'Customer deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
