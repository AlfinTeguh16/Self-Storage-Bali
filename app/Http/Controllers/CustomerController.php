<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Validation\Rule;
use App\Models\Storage;
use Illuminate\Support\Facades\Log;


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
        $customers = Customer::where('is_deleted', false)->orderBy('name')->get();
        $storageUnits = Storage::where('is_deleted', false)->orderBy('size')->get();
        return view('module.customer.create', compact('customers', 'storageUnits'));
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request)
    {
        try {
            // Validasi file
            $validated = $request->validate([
                'name' => 'required|string|max:150',
                'address' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:150',
                'phone' => 'nullable|string|max:20',
                'credential' => 'nullable|string',
            ]);

            if ($request->hasFile('credential')) {
                $file = $request->file('credential');
                $originalName = $file->getClientOriginalName();

                // Cek apakah file dengan nama asli ini sudah ada dan belum dihapus
                $exists = Customer::where('is_deleted', 0)
                    ->where('credential', 'like', "%/{$originalName}")
                    ->exists();

                // Simpan file
                $path = $file->storeAs('credential', $originalName, 'public');
                $validated['credential'] = $path;
            } else {
                $validated['credential'] = null;
            }

            // Simpan ke database
            Customer::create($validated);

            return redirect()->back()->with('success', 'Customer successfully added.');
        } catch (ValidationException $e) {
            if ($e->validator->errors()->has('credential')) {
                $message = $e->validator->errors()->first('credential');
                return redirect()->back()->with('error', 'Upload failed: ' . $message)->withInput();
            }

            return redirect()->back()->with('error', 'Validasi failed.')->withInput();
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan customer: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong, please try again.');
        }
    }

    /**
     * Display the specified customer.
     */
    public function show($id)
    {
        $customer = Customer::where('is_deleted', false)->findOrFail($id);
        $customer->load(['bookings', 'payments']);

        // return response()->json([
        //     'customer' => $customer
        // ]);
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
            // Validasi file
            $validated = $request->validate([
                'name' => 'required|string|max:150',
                'address' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:150',
                'phone' => 'nullable|string|max:20',
                'credential' => 'nullable|string',
            ]);

            if ($request->hasFile('credential')) {
                $file = $request->file('credential');
                $originalName = $file->getClientOriginalName();

                // Cek apakah file dengan nama asli ini sudah ada dan belum dihapus
                $exists = Customer::where('is_deleted', 0)
                    ->where('credential', 'like', "%/{$originalName}")
                    ->exists();

                // Simpan file
                $path = $file->storeAs('credential', $originalName, 'public');
                $validated['credential'] = $path;
            } else {
                $validated['credential'] = null;
            }

            // Simpan ke database
            $customer = Customer::where('is_deleted', false)->findOrFail($id);
            $customer->update($validated);

            return redirect()->back()->with('success', 'Customer successfully updated.');
        } catch (ValidationException $e) {
            if ($e->validator->errors()->has('credential')) {
                $message = $e->validator->errors()->first('credential');
                return redirect()->back()->with('error', 'Upload failed: ' . $message)->withInput();
            }

            return redirect()->back()->with('error', 'Validasi failed.')->withInput();
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan customer: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong, please try again.');
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
