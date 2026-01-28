<?php

namespace App\Http\Controllers;

use Midtrans\Transaction;
use Midtrans\Notification;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

    /**
     * Process credit card payment
     */
    public function processCreditCard(Request $request, $bookingId)
    {
        $request->validate([
            'card_number' => 'required|string',
            'expiry_date' => 'required|string',
            'cvv' => 'required|string',
            'installment' => 'nullable|string',
        ]);

        $booking = Booking::with(['storage', 'customer'])->findOrFail($bookingId);

        // Get last 4 digits of card number
        $cardNumber = str_replace(' ', '', $request->card_number);
        $lastFourDigits = substr($cardNumber, -4);

        // Update booking status to success
        $booking->update([
            'status' => 'success',
            'payment_method' => 'credit_card',
            'paid_at' => now(),
        ]);

        // Send payment success email
        try {
            $customerEmail = $booking->customer->email ?? null;
            $customerName = $booking->customer->name ?? 'Customer';

            if ($customerEmail) {
                Mail::send('emails.payment-success', [
                    'booking' => $booking,
                    'lastFourDigits' => $lastFourDigits,
                ], function ($message) use ($customerEmail, $customerName) {
                    $message->to($customerEmail, $customerName)
                        ->subject('Payment Successful - Self Storage Bali');
                });

                Log::info('Payment success email sent', ['booking_id' => $booking->id, 'email' => $customerEmail]);
            } else {
                Log::warning('Cannot send email - customer email not found', ['booking_id' => $booking->id]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send payment email', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('receipt.show', ['bookingId' => $bookingId])
            ->with('success', 'Payment successful! A confirmation email has been sent to your email address.');
    }
}
