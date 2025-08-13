<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Storage;
use App\Models\StorageManagement;
use Illuminate\Support\Facades\Log;

class StorageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $storages = Storage::where('is_deleted', false)->get();
        return view('module.storage.index', compact('storages'));

    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('module.storage.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            Log::info('Start storing storage', ['request' => $request->all()]);

            $validated = $request->validate([
                'size'        => 'required|string|max:50',
                'description' => 'nullable|string',
                'price'       => 'required|integer|min:0',
            ]);
            Log::info('Validation passed for storage', ['validated' => $validated]);

            // Simpan storage
            $storage = Storage::create($validated);
            Log::info('Storage created', ['storage_id' => $storage->id]);

            // Simpan storage management default
            $sm = StorageManagement::create([
                'storage_id'  => $storage->id,
                'booking_id'  => null,
                'status'      => 'available',
                'last_clean'  => null,
                'is_deleted'  => 0,
            ]);

            Log::info('Storage management created', [
                'storage_management_id' => $sm->id,
                'storage_id' => $storage->id,
                'status' => $sm->status
            ]);
   

            // return response()->json([
            //     'status' => 'success',
            //     'data storage' => $storage,
            //     'storage management' => $sm,
            // ]);


            return redirect()
                ->route('data-storage.index')
                ->with('success', 'Data Storage created successfully');
        } catch (\Exception $e) {
            Log::error('Error storing storage', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withErrors($e->getMessage())
                ->withInput();
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $storage = Storage::where('is_deleted', false)->findOrFail($id);
        return view('module.storage.show', compact('storage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $storage = Storage::where('is_deleted', false)->findOrFail($id);
        return view('module.storage.edit', compact('storage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'size' => 'required|string|max:50',
                'description' => 'nullable|string',
                'price' => 'required|integer|min:0',
            ]);

            $storage = Storage::where('is_deleted', false)->findOrFail($id);
            $storage->update($validated);
            return redirect()->route('data-storage.index')->with('success', 'Data Box updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Soft delete the specified resource.
     */
    public function destroy(string $id)
    {
        try {
            $storage = Storage::where('is_deleted', false)->findOrFail($id);
            $storage->update(['is_deleted' => true]);
            return redirect()->route('data-storage.index')->with('success', 'Data Box deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }
}
