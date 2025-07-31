<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'tb_customers';

    protected $fillable = [
        'name', 'address', 'email', 'phone', 'credential', 'is_deleted',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'customer_id');
    }

    public $timestamps = true;
}
