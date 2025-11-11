<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Storage extends Model
{
    protected $table = 'tb_storages';

    protected $fillable = [
        'size', 'price', 'description', 'is_deleted',
    ];

    public $timestamps = true;

    // ✅ RELASI WAJIB — tambahkan ini!
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'storage_id');
    }

    // Relasi lain (tetap dipertahankan)
    public function storageManagement()
    {
        return $this->hasMany(StorageManagement::class, 'storage_id');
    }

    public function latestManagement()
    {
        return $this->hasOne(StorageManagement::class, 'storage_id')->latest();
    }


    public function isAvailableBetween($from, $to): bool
    {

        $from = Carbon::parse($from)->toDateString();
        $to   = Carbon::parse($to)->toDateString();

        // Cek overlap booking aktif
        return ! $this->bookings()
            ->where('is_deleted', false)
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($from, $to) {
                // Overlap: booking.start ≤ to AND booking.end ≥ from
                $q->whereDate('start_date', '<=', $to)
                ->whereDate('end_date', '>=', $from);
            })
            ->exists();
    }

    public function scopeAvailableForDisplay($query)
    {
        return $query->where('is_deleted', false);
    }
}