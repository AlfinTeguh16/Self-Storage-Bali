<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk merepresentasikan data pengeluaran operasional.
 */
class OperationalExpense extends Model
{
    // Nama tabel di database
    protected $table = 'tb_operational_expenses';

    // Kolom yang boleh diisi secara massal (mass assignment)
    protected $fillable = [
        'amount',
        'category',
        'date',
        'description',
        'booking_id',
        'storage_id',
    ];

    // Konversi tipe data otomatis
    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    /**
     * Relasi ke model Booking (opsional, jika pengeluaran terkait booking).
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    /**
     * Relasi ke model Storage (opsional, jika pengeluaran terkait unit storage).
     */
    public function storage()
    {
        return $this->belongsTo(Storage::class, 'storage_id');
    }
}
