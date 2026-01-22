<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageManagement extends Model
{
    protected $table = 'tb_storage_management';

    protected $fillable = [
        'storage_id', 'booking_id', 'customer_id', 'status', 'start_date', 'end_date', 'last_clean', 'is_deleted',
    ];




    protected $casts = [
        'last_clean' => 'datetime',
        'is_deleted' => 'boolean',
    ];

    public function storage()
    {
        return $this->belongsTo(Storage::class, 'storage_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public $timestamps = true;


    // Scope methods for filtering
    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

    public function scopeBooked($query)
    {
        return $query->where('status', 'booked');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
