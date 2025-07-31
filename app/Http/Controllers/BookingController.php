<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of the bookings.
     */
    public function index()
    {
        $bookings = Booking::with('customer')
            ->where('is_deleted', false)
            ->latest()
            ->get();

        return view('module.booking.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new booking.
     */
    public function create()
    {
        $customers = Customer::where('is_deleted', false)->get();
        return view('module.booking.create', compact('customers'));
    }

    /**
     * Store a newly created booking.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|exists:tb_customers,id',
                'booking_ref' => 'required|string|unique:tb_bookings,booking_ref',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'notes' => 'nullable|string',
            ]);

            Booking::create($validated);

            return redirect()->route('data-booking.index')->with('success', 'Booking created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified booking.
     */
    public function show($id)
    {
        $booking = Booking::with('customer')
            ->where('is_deleted', false)
            ->findOrFail($id);

        return view('module.booking.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified booking.
     */
    public function edit($id)
    {
        $booking = Booking::where('is_deleted', false)->findOrFail($id);
        $customers = Customer::where('is_deleted', false)->get();

        return view('module.booking.edit', compact('booking', 'customers'));
    }

    /**
     * Update the specified booking.
     */
    public function update(Request $request, $id)
    {
        try {
            $booking = Booking::where('is_deleted', false)->findOrFail($id);

            $validated = $request->validate([
                'customer_id' => 'required|exists:tb_customers,id',
                'booking_ref' => 'required|string|unique:tb_bookings,booking_ref,' . $booking->id,
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'notes' => 'nullable|string',
            ]);

            $booking->update($validated);

            return redirect()->route('data-booking.index')->with('success', 'Booking updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Soft delete the specified booking.
     */
    public function destroy($id)
    {
        try {
            $booking = Booking::where('is_deleted', false)->findOrFail($id);
            $booking->update(['is_deleted' => true]);

            return redirect()->route('data-booking.index')->with('success', 'Booking deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
