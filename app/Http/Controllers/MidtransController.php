<?php

namespace App\Http\Controllers;

use Midtrans\Midtrans;
use Midtrans\Transaction;
use Midtrans\Notification;
use App\Models\Booking;
use Illuminate\Http\Request;

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

        if ($status == 'capture' || $status == 'settlement') {
            $booking->update(['status' => 'success']);
        } elseif ($status == 'pending') {
            $booking->update(['status' => 'pending']);
        } else {
            $booking->update(['status' => 'failed']);
        }

        // Optionally send email to customer confirming the payment
        // Mail::to($customer->email)->queue(new PaymentEmail($paymentUrl));


        return response()->json(['status' => 'success']);
    }
}
