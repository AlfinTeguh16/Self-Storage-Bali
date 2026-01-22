<?php

namespace App\Http\Controllers;

use Midtrans\Transaction;
use Midtrans\Notification;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function notification(Request $request)
    {
        $notif = new Notification();

        // Get the transaction status
        $status = $notif->transaction_status;
        $order_id = $notif->order_id;

        // Find the booking by order_id
        $booking = Booking::where('booking_ref', $order_id)->first();

        // Check if booking exists before updating
        if (!$booking) {
            Log::warning('Midtrans notification: Booking not found', ['order_id' => $order_id]);
            return response()->json(['status' => 'error', 'message' => 'Booking not found'], 404);
        }

        if ($status == 'capture' || $status == 'settlement') {
            $booking->update(['status' => 'success']);
        } elseif ($status == 'pending') {
            $booking->update(['status' => 'pending']);
        } else {
            $booking->update(['status' => 'failed']);
        }

        Log::info('Midtrans notification processed', [
            'order_id' => $order_id,
            'status' => $status,
            'booking_id' => $booking->id
        ]);

        return response()->json(['status' => 'success']);
    }
}
