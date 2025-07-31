<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Customer;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the payments.
     */
    public function index()
    {
        $payments = Payment::with('customer')
            ->where('is_deleted', false)
            ->latest()
            ->get();

        return view('module.payment.index', compact('payments'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create()
    {
        $customers = Customer::where('is_deleted', false)->get();
        return view('module.payment.create', compact('customers'));
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|exists:tb_customers,id',
                'method' => 'required|string|max:50',
                'transaction_file' => 'nullable|string', // URL atau base64 atau path
            ]);

            Payment::create($validated);

            return redirect()->route('data-payment.index')->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified payment.
     */
    public function show($id)
    {
        $payment = Payment::with('customer')
            ->where('is_deleted', false)
            ->findOrFail($id);

        return view('module.payment.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit($id)
    {
        $payment = Payment::where('is_deleted', false)->findOrFail($id);
        $customers = Customer::where('is_deleted', false)->get();
        return view('module.payment.edit', compact('payment', 'customers'));
    }

    /**
     * Update the specified payment in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $payment = Payment::where('is_deleted', false)->findOrFail($id);

            $validated = $request->validate([
                'customer_id' => 'required|exists:tb_customers,id',
                'method' => 'required|string|max:50',
                'transaction_file' => 'nullable|string',
            ]);

            $payment->update($validated);

            return redirect()->route('data-payment.index')->with('success', 'Payment updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Soft delete the specified payment.
     */
    public function destroy($id)
    {
        try {
            $payment = Payment::where('is_deleted', false)->findOrFail($id);
            $payment->update(['is_deleted' => true]);

            return redirect()->route('data-payment.index')->with('success', 'Payment deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
