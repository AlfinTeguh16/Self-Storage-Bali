<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateOverdueBookings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Buat job ini tidak retry jika gagal (hindari duplikasi update)
     */
    public $tries = 1;

    /**
     * Waktu timeout dalam detik
     */
    public $timeout = 300;

    /**
     * Eksekusi job: update booking & storage management yang sudah melewati end_date
     *
     * Kriteria:
     * - Booking status = 'success' (sudah dibayar)
     * - end_date < now()
     * - Belum di-mark sebagai 'ended' atau 'overdue'
     */
    public function handle()
    {
        $now = now();

        try {
            DB::beginTransaction();

            // 1. Ambil booking yang sudah lewat end_date, belum di-overdue-kan
            $affectedBookings = DB::table('tb_bookings as b')
                ->join('tb_storage_management as sm', 'sm.booking_id', '=', 'b.id')
                ->where('b.is_deleted', 0)
                ->where('b.status', 'success')
                ->where('b.end_date', '<', $now->toDateString()) // end_date < hari ini
                ->whereNotIn('sm.status', ['overdue', 'available']) // hindari duplikat
                ->update([
                    'b.status' => 'overdue',
                    'b.updated_at' => $now,
                ]);

            // 2. Update storage management terkait → status = 'overdue'
            $affectedSm = DB::table('tb_storage_management as sm')
                ->join('tb_bookings as b', 'b.id', '=', 'sm.booking_id')
                ->where('b.is_deleted', 0)
                ->where('b.status', 'overdue') // booking baru saja di-set overdue
                ->where('b.end_date', '<', $now->toDateString())
                ->whereNotIn('sm.status', ['overdue', 'available'])
                ->update([
                    'sm.status' => 'overdue',
                    'sm.updated_at' => $now,
                ]);

            DB::commit();

            if ($affectedBookings > 0 || $affectedSm > 0) {
                Log::info('UpdateOverdueBookings: berhasil', [
                    'bookings_updated' => $affectedBookings,
                    'sm_updated'       => $affectedSm,
                    'timestamp'        => $now->toDateTimeString(),
                ]);
            } else {
                Log::debug('UpdateOverdueBookings: tidak ada data yang diperbarui');
            }

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('UpdateOverdueBookings gagal', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Opsional: kirim notifikasi ke admin jika job gagal
            // \Notification::route('mail', 'admin@example.com')->notify(new JobFailedNotification($e));
        }
    }
}