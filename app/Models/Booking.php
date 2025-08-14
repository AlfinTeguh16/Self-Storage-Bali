<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'tb_bookings';

    protected $fillable = [
        'customer_id', 'storage_id', 'booking_ref', 'start_date', 'end_date', 'notes', 'is_deleted',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_deleted' => 'boolean',
    ];


    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function storage()
    {
        return $this->belongsTo(Storage::class, 'storage_id');
    }

    public function storageManagement()
    {
        return $this->hasMany(StorageManagement::class, 'booking_id');
    }

    public function scopeNotDeleted($q)
    {
        return $q->where('is_deleted', false);
    }

    public $timestamps = true;
}
