<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'tb_bookings';

    protected $fillable = [
        'customer_id', 'booking_ref', 'start_date', 'end_date', 'notes', 'is_deleted',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function storageManagement()
    {
        return $this->hasMany(StorageManagement::class, 'booking_id');
    }

    public $timestamps = true;
}
