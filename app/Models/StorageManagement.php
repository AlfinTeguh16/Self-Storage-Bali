<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageManagement extends Model
{
    protected $table = 'tb_storage_management';

    protected $fillable = [
        'storage_id', 'booking_id', 'status', 'last_clean', 'is_deleted',
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
}
