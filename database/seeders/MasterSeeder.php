<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class MasterSeeder extends Seeder
{
    /**
     * Remove keys from $row that don't exist as columns in $table.
     */
    protected function sanitizeRow(string $table, array $row): array
    {
        $columns = Schema::getColumnListing($table);
        return array_intersect_key($row, array_flip($columns));
    }

    public function run(): void
    {
        $now = Carbon::now()->toDateTimeString();

        // Non-aktifkan cek foreign key sementara agar insert urutan tidak bermasalah
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // ----------------------------
        // tb_customers (sesuai SQL)
        // ----------------------------
        $customers = [
            [
                'id' => 1,
                'name' => 'PT. Satu Contoh',
                'address' => 'Jl. Merdeka No.1, Jakarta',
                'email' => 'contact@satucontoh.co.id',
                'phone' => '+628123456789',
                'credential' => json_encode(['ktp' => '1234567890']),
                'is_deleted' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Budi Santoso',
                'address' => 'Jl. Mawar 2, Bandung',
                'email' => 'budi@example.com',
                'phone' => '+628987654321',
                'credential' => null,
                'is_deleted' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        $customers = array_map(fn($r) => $this->sanitizeRow('tb_customers', $r), $customers);
        DB::table('tb_customers')->insert($customers);

        // ----------------------------
        // tb_storages (tidak ada price_per_day di SQL)
        // ----------------------------
        $storages = [
            [
                'id' => 1,
                'size' => 'Small (1 m³)',
                'price' => 100000,
                'description' => 'Container kecil, cocok untuk barang rumah tangga.',
                'is_deleted' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'size' => 'Medium (3 m³)',
                'price' => 250000,
                'description' => 'Container medium untuk perabotan sedang.',
                'is_deleted' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        $storages = array_map(fn($r) => $this->sanitizeRow('tb_storages', $r), $storages);
        DB::table('tb_storages')->insert($storages);

        // ----------------------------
        // tb_users (SQL: tidak ada is_deleted)
        // ----------------------------
        $users = [
            [
                'id' => 1,
                'username' => 'admin',
                'password' => Hash::make('Admin@123'),
                'role' => 'admin',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'username' => 'staff01',
                'password' => Hash::make('Staff@123'),
                'role' => 'staff',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        $users = array_map(fn($r) => $this->sanitizeRow('tb_users', $r), $users);
        DB::table('tb_users')->insert($users);

        // ----------------------------
        // tb_bookings
        // ----------------------------
        $start1 = Carbon::now()->subDays(2)->toDateString();
        $end1 = Carbon::now()->addDays(5)->toDateString();
        $totalDate1 = Carbon::parse($start1)->diffInDays(Carbon::parse($end1));
        $totalPrice1 = $totalDate1 * 100000 / 10; // contoh: berdasarkan price di storages (10000 jika diinginkan)

        $bookings = [
            [
                'id' => 1,
                'customer_id' => 1,
                'storage_id' => 1,
                'booking_ref' => 'BK' . Carbon::now()->format('Ymd') . '-001',
                'start_date' => $start1,
                'end_date' => $end1,
                'total_date' => $totalDate1,
                'total_price' => $totalPrice1,
                'notes' => 'Pengiriman akan dilakukan setiap Senin.',
                'status' => 'pending',
                'is_deleted' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'customer_id' => 2,
                'storage_id' => 2,
                'booking_ref' => 'BK' . Carbon::now()->format('Ymd') . '-002',
                'start_date' => Carbon::now()->toDateString(),
                'end_date' => Carbon::now()->addDays(3)->toDateString(),
                'total_date' => 3,
                'total_price' => 3 * 250000 / 10,
                'notes' => 'Butuh akses lift.',
                'status' => 'success',
                'is_deleted' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        $bookings = array_map(fn($r) => $this->sanitizeRow('tb_bookings', $r), $bookings);
        DB::table('tb_bookings')->insert($bookings);

        // ----------------------------
        // tb_payments
        // ----------------------------
        $payments = [
            [
                'id' => 1,
                'customer_id' => 1,
                'booking_id' => 1,
                'method' => 'bank_transfer',
                'status' => 'pending',
                'transaction_file' => null,
                'payment_url' => null,
                'midtrans_order_id' => null,
                'is_deleted' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'customer_id' => 2,
                'booking_id' => 2,
                'method' => 'midtrans',
                'status' => 'success',
                'transaction_file' => null,
                'payment_url' => 'https://payment.example/abcd',
                'midtrans_order_id' => 'ORDER-' . Carbon::now()->format('YmdHis'),
                'is_deleted' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        $payments = array_map(fn($r) => $this->sanitizeRow('tb_payments', $r), $payments);
        DB::table('tb_payments')->insert($payments);

        // ----------------------------
        // tb_storage_management
        // ----------------------------
        $storageManagement = [
            [
                'id' => 1,
                'storage_id' => 1,
                'booking_id' => 1,
                'status' => 'booked',
                'last_clean' => null,
                'is_deleted' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'storage_id' => 2,
                'booking_id' => 2,
                'status' => 'booked',
                'last_clean' => Carbon::now()->subDays(1)->toDateString(),
                'is_deleted' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        $storageManagement = array_map(fn($r) => $this->sanitizeRow('tb_storage_management', $r), $storageManagement);
        DB::table('tb_storage_management')->insert($storageManagement);

        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
