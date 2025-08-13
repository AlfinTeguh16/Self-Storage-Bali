<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $paymentUrl;

    public function __construct($paymentUrl)
    {
        $this->paymentUrl = $paymentUrl;
    }

    public function build()
    {
        return $this->subject('Your Booking Payment Link')
                    ->view('emails.payment')
                    ->with([
                        'paymentUrl' => $this->paymentUrl,
                    ]);
    }

   
}
