<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


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
            Log::info('Start storing payment', ['request' => $request->all()]);

            $validated = $request->validate([
                'customer_id' => 'required|exists:tb_customers,id',
                'method' => 'required|string|max:50',
                'transaction_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);
            Log::info('Validation passed', ['validated' => $validated]);

            $image = $request->file('transaction_file');
            Log::info('Transaction file check', ['has_file' => $image !== null]);

            if ($image) {
                $fileName = time() . '-' . Str::slug($request->customer_id);
                $resultFile = $image->storeAs(
                    'payment/transaction',
                    "{$fileName}.{$image->extension()}",
                    'public'
                );
                Log::info('File stored successfully', ['path' => $resultFile]);

                $validated['transaction_file'] = Storage::url($resultFile);
            }

            $payment = Payment::create($validated);
            Log::info('Payment created', ['payment_id' => $payment->id]);

            return redirect()->route('data-payment.index')->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            Log::error('Error storing payment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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
                'transaction_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            // Cek apakah ada file baru
            if ($request->hasFile('transaction_file')) {
                // Hapus file lama jika ada
                if ($payment->transaction_file && Storage::exists(str_replace('/storage', 'public', $payment->transaction_file))) {
                    Storage::delete(str_replace('/storage', 'public', $payment->transaction_file));
                }

                // Simpan file baru
                $fileName = time() . '-' . Str::slug($request->customer_id);
                $path = $request->file('transaction_file')
                                ->storeAs('payment/transaction', "{$fileName}.{$request->file('transaction_file')->extension()}", 'public');

                $validated['transaction_file'] = Storage::url($path);
            } else {
                // Kalau tidak upload file baru, pertahankan file lama
                $validated['transaction_file'] = $payment->transaction_file;
            }

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
