<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'tb_payments';

    protected $fillable = [
        'customer_id', 'booking_id', 'method', 'status', 'transaction_file', 
        'payment_url', 'midtrans_order_id', 'is_deleted',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function booking()  {
        return $this->belongsTo(Booking::class, 'booking_id');
    } 

    public $timestamps = true;
}
