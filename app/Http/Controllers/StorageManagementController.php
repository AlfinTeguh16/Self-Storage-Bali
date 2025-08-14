<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StorageManagement;
use App\Models\Storage;
use App\Models\Booking;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class StorageManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $storages = \App\Models\Storage::with([
            'storageManagement' => function ($query) {
                $query->orderBy('id', 'desc'); // Ambil yang terbaru di index 0
            },
            'storageManagement.booking'
        ])
            ->where('is_deleted', false)
            ->orderBy('size')
            ->orderBy('id')
            ->get()
            ->map(function ($storage) {
                $storage->description = \Illuminate\Support\Str::limit($storage->description, 100); // kira-kira 2 baris
                return $storage;
            })
            ->groupBy('size');


            // return response()->json([
            //     'status' => 'success',
            //     'data' => $storages
            // ]);

        return view('module.storage-management.index', compact('storages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $storages = Storage::where('is_deleted', false)->get();
        $bookings = Booking::where('is_deleted', false)->get();

        return view('module.storage_management.create', compact('storages', 'bookings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'storage_id' => 'required|exists:tb_storages,id',
                'booking_id' => 'required|exists:tb_bookings,id',
                'status' => 'required|in:available,booked,maintenance,cleaning',
                'last_clean' => 'nullable|date',
            ]);

            StorageManagement::create($validated);

            return redirect()->route('storage-management.index')->with('success', 'Storage allocation created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $management = StorageManagement::with(['storage', 'booking'])
            ->where('is_deleted', false)
            ->findOrFail($id);

        return view('module.storage_management.show', compact('management'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $management = StorageManagement::where('is_deleted', false)->findOrFail($id);
        $storages = Storage::where('is_deleted', false)->get();
        $bookings = Booking::where('is_deleted', false)->get();

        return view('module.storage_management.edit', compact('management', 'storages', 'bookings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $management = StorageManagement::where('is_deleted', false)->findOrFail($id);

            $validated = $request->validate([
                'storage_id' => 'required|exists:tb_storages,id',
                'booking_id' => 'required|exists:tb_bookings,id',
                'status' => 'required|in:available,booked,maintenance,cleaning',
            ]);

            $management->update($validated);

            return redirect()->route('storage-management.index')->with('success', 'Storage allocation updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Soft delete the specified resource.
     */
    public function destroy($id)
    {
        try {
            $management = StorageManagement::where('is_deleted', false)->findOrFail($id);
            $management->update(['is_deleted' => true]);

            return redirect()->route('storage-management.index')->with('success', 'Storage allocation deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function lastClean(Request $request, $id)
    {
        try {
            $management = StorageManagement::where('is_deleted', false)->findOrFail($id);

            $validated = $request->validate([
                'last_clean' => 'required|date',
            ]);
            // dd($validated);

            $management->update($validated);

            return redirect()->route('storage-management.index')->with('success', 'Last clean date updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function autoUpdateStatusStorage()
    {
        try {
            $today = now()->toDateString();

            $affected = DB::table('tb_storage_management as sm')
                ->join('tb_bookings as b', 'b.id', '=', 'sm.booking_id')
                ->where('sm.is_deleted', 0)
                ->where('sm.status', 'booked')
                ->where('b.is_deleted', 0)
                ->whereDate('b.end_date', '<', $today) // sudah lewat dari hari ini
                ->update([
                    'sm.status'     => 'available',
                    'sm.booking_id' => null,
                    'sm.updated_at' => now(),
                ]);

            Log::info('Auto update storage: done', ['affected' => $affected, 'date' => $today]);

            return redirect()
                ->route('storage-management.index')
                ->with('success', "Storage status updated: {$affected} item(s).");
        } catch (\Throwable $e) {
            Log::error('Auto update storage: failed', ['error' => $e->getMessage()]);
            return back()->withErrors($e->getMessage());
        }
    }
}
