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
        // Menambahkan variasi booking untuk testing laporan yang lebih baik
        // ----------------------------
        
        // Booking 1: Booking aktif saat ini (untuk kalkulasi listrik bulan ini)
        $start1 = Carbon::now()->subDays(10)->toDateString();
        $end1 = Carbon::now()->addDays(20)->toDateString();
        $totalDate1 = Carbon::parse($start1)->diffInDays(Carbon::parse($end1)) + 1;
        $totalPrice1 = $totalDate1 * 100000;

        // Booking 2: Booking sukses bulan ini
        $start2 = Carbon::now()->startOfMonth()->toDateString();
        $end2 = Carbon::now()->addDays(5)->toDateString();
        $totalDate2 = Carbon::parse($start2)->diffInDays(Carbon::parse($end2)) + 1;
        $totalPrice2 = $totalDate2 * 250000;
        
        // Booking 3: Booking dari bulan lalu (untuk test filter)
        $start3 = Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $end3 = Carbon::now()->subMonth()->addDays(15)->toDateString();
        $totalDate3 = Carbon::parse($start3)->diffInDays(Carbon::parse($end3)) + 1;
        $totalPrice3 = $totalDate3 * 100000;
        
        // Booking 4: Booking aktif lintas bulan (dari bulan lalu sampai bulan ini)
        $start4 = Carbon::now()->subMonth()->addDays(20)->toDateString();
        $end4 = Carbon::now()->addDays(10)->toDateString();
        $totalDate4 = Carbon::parse($start4)->diffInDays(Carbon::parse($end4)) + 1;
        $totalPrice4 = $totalDate4 * 250000;

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
                'notes' => 'Office furniture storage for renovation',
                'status' => 'success',
                'is_deleted' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'customer_id' => 2,
                'storage_id' => 2,
                'booking_ref' => 'BK' . Carbon::now()->format('Ymd') . '-002',
                'start_date' => $start2,
                'end_date' => $end2,
                'total_date' => $totalDate2,
                'total_price' => $totalPrice2,
                'notes' => 'Moving items storage',
                'status' => 'success',
                'is_deleted' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'customer_id' => 1,
                'storage_id' => 1,
                'booking_ref' => 'BK' . Carbon::now()->subMonth()->format('Ymd') . '-003',
                'start_date' => $start3,
                'end_date' => $end3,
                'total_date' => $totalDate3,
                'total_price' => $totalPrice3,
                'notes' => 'Company document archive storage',
                'status' => 'success',
                'is_deleted' => 0,
                'created_at' => Carbon::now()->subMonth()->toDateTimeString(),
                'updated_at' => Carbon::now()->subMonth()->toDateTimeString(),
            ],
            [
                'id' => 4,
                'customer_id' => 2,
                'storage_id' => 2,
                'booking_ref' => 'BK' . Carbon::now()->subMonth()->format('Ymd') . '-004',
                'start_date' => $start4,
                'end_date' => $end4,
                'total_date' => $totalDate4,
                'total_price' => $totalPrice4,
                'notes' => 'Store inventory during renovation',
                'status' => 'success',
                'is_deleted' => 0,
                'created_at' => Carbon::now()->subMonth()->toDateTimeString(),
                'updated_at' => Carbon::now()->subMonth()->toDateTimeString(),
            ],
            [
                'id' => 5,
                'customer_id' => 1,
                'storage_id' => 1,
                'booking_ref' => 'BK' . Carbon::now()->format('Ymd') . '-005',
                'start_date' => Carbon::now()->toDateString(),
                'end_date' => Carbon::now()->addDays(7)->toDateString(),
                'total_date' => 8,
                'total_price' => 800000,
                'notes' => 'Event equipment storage',
                'status' => 'pending',
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
                'method' => 'midtrans',
                'status' => 'success',
                'transaction_file' => null,
                'payment_url' => 'https://payment.example/abc1',
                'midtrans_order_id' => 'ORDER-' . Carbon::now()->format('YmdHis') . '-001',
                'is_deleted' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'customer_id' => 2,
                'booking_id' => 2,
                'method' => 'bank_transfer',
                'status' => 'success',
                'transaction_file' => null,
                'payment_url' => null,
                'midtrans_order_id' => null,
                'is_deleted' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'customer_id' => 1,
                'booking_id' => 3,
                'method' => 'midtrans',
                'status' => 'success',
                'transaction_file' => null,
                'payment_url' => 'https://payment.example/abc3',
                'midtrans_order_id' => 'ORDER-' . Carbon::now()->subMonth()->format('YmdHis') . '-003',
                'is_deleted' => 0,
                'created_at' => Carbon::now()->subMonth()->toDateTimeString(),
                'updated_at' => Carbon::now()->subMonth()->toDateTimeString(),
            ],
            [
                'id' => 4,
                'customer_id' => 2,
                'booking_id' => 4,
                'method' => 'bank_transfer',
                'status' => 'success',
                'transaction_file' => null,
                'payment_url' => null,
                'midtrans_order_id' => null,
                'is_deleted' => 0,
                'created_at' => Carbon::now()->subMonth()->toDateTimeString(),
                'updated_at' => Carbon::now()->subMonth()->toDateTimeString(),
            ],
            [
                'id' => 5,
                'customer_id' => 1,
                'booking_id' => 5,
                'method' => 'bank_transfer',
                'status' => 'pending',
                'transaction_file' => null,
                'payment_url' => null,
                'midtrans_order_id' => null,
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
                'last_clean' => Carbon::now()->subDays(5)->toDateString(),
                'is_deleted' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'storage_id' => 2,
                'booking_id' => 2,
                'status' => 'booked',
                'last_clean' => Carbon::now()->subDays(3)->toDateString(),
                'is_deleted' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'storage_id' => 1,
                'booking_id' => 3,
                'status' => 'booked',
                'last_clean' => Carbon::now()->subMonth()->addDays(10)->toDateString(),
                'is_deleted' => 0,
                'created_at' => Carbon::now()->subMonth()->toDateTimeString(),
                'updated_at' => Carbon::now()->subMonth()->toDateTimeString(),
            ],
            [
                'id' => 4,
                'storage_id' => 2,
                'booking_id' => 4,
                'status' => 'booked',
                'last_clean' => Carbon::now()->subMonth()->addDays(20)->toDateString(),
                'is_deleted' => 0,
                'created_at' => Carbon::now()->subMonth()->toDateTimeString(),
                'updated_at' => Carbon::now()->subMonth()->toDateTimeString(),
            ],
            [
                'id' => 5,
                'storage_id' => 1,
                'booking_id' => 5,
                'status' => 'booked',
                'last_clean' => null,
                'is_deleted' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        $storageManagement = array_map(fn($r) => $this->sanitizeRow('tb_storage_management', $r), $storageManagement);
        DB::table('tb_storage_management')->insert($storageManagement);

        // ----------------------------
        // tb_operational_expenses
        // Menambahkan data pengeluaran operasional untuk berbagai kategori
        // ----------------------------
        $expenses = [];
        
        // Bulan ini - Gaji Karyawan
        $expenses[] = [
            'amount' => 5000000,
            'category' => 'Salary',
            'date' => Carbon::now()->startOfMonth()->toDateString(),
            'description' => 'Admin Salary for ' . Carbon::now()->format('F Y'),
            'booking_id' => null,
            'storage_id' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        
        $expenses[] = [
            'amount' => 4500000,
            'category' => 'Salary',
            'date' => Carbon::now()->startOfMonth()->toDateString(),
            'description' => 'Warehouse Staff Salary for ' . Carbon::now()->format('F Y'),
            'booking_id' => null,
            'storage_id' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        
        // Bulan ini - Kebersihan
        $expenses[] = [
            'amount' => 350000,
            'category' => 'Cleaning',
            'date' => Carbon::now()->subDays(5)->toDateString(),
            'description' => 'Cleaning Storage Unit #1',
            'booking_id' => 1,
            'storage_id' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        
        $expenses[] = [
            'amount' => 275000,
            'category' => 'Cleaning',
            'date' => Carbon::now()->subDays(3)->toDateString(),
            'description' => 'Routine Public Area Cleaning',
            'booking_id' => null,
            'storage_id' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        
        // Bulan ini - Lainnya
        $expenses[] = [
            'amount' => 1500000,
            'category' => 'Others',
            'date' => Carbon::now()->subDays(10)->toDateString(),
            'description' => 'AC and Ventilation Maintenance',
            'booking_id' => null,
            'storage_id' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        
        $expenses[] = [
            'amount' => 850000,
            'category' => 'Others',
            'date' => Carbon::now()->subDays(15)->toDateString(),
            'description' => 'Security System Maintenance',
            'booking_id' => null,
            'storage_id' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        
        // Bulan lalu - untuk testing filter
        $lastMonth = Carbon::now()->subMonth();
        
        $expenses[] = [
            'amount' => 5000000,
            'category' => 'Salary',
            'date' => $lastMonth->copy()->startOfMonth()->toDateString(),
            'description' => 'Admin Salary for ' . $lastMonth->format('F Y'),
            'booking_id' => null,
            'storage_id' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        
        $expenses[] = [
            'amount' => 4500000,
            'category' => 'Salary',
            'date' => $lastMonth->copy()->startOfMonth()->toDateString(),
            'description' => 'Warehouse Staff Salary for ' . $lastMonth->format('F Y'),
            'booking_id' => null,
            'storage_id' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        
        $expenses[] = [
            'amount' => 425000,
            'category' => 'Cleaning',
            'date' => $lastMonth->copy()->addDays(10)->toDateString(),
            'description' => 'Cleaning Storage Unit #2',
            'booking_id' => 2,
            'storage_id' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        
        $expenses[] = [
            'amount' => 2200000,
            'category' => 'Others',
            'date' => $lastMonth->copy()->addDays(5)->toDateString(),
            'description' => 'Minor Storage Area Renovation',
            'booking_id' => null,
            'storage_id' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        
        // 2 bulan lalu - untuk memastikan ada data historis
        $twoMonthsAgo = Carbon::now()->subMonths(2);
        
        $expenses[] = [
            'amount' => 5000000,
            'category' => 'Salary',
            'date' => $twoMonthsAgo->copy()->startOfMonth()->toDateString(),
            'description' => 'Admin Salary for ' . $twoMonthsAgo->format('F Y'),
            'booking_id' => null,
            'storage_id' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        
        $expenses[] = [
            'amount' => 4500000,
            'category' => 'Salary',
            'date' => $twoMonthsAgo->copy()->startOfMonth()->toDateString(),
            'description' => 'Warehouse Staff Salary for ' . $twoMonthsAgo->format('F Y'),
            'booking_id' => null,
            'storage_id' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        
        $expenses[] = [
            'amount' => 600000,
            'category' => 'Cleaning',
            'date' => $twoMonthsAgo->copy()->addDays(12)->toDateString(),
            'description' => 'Thorough Cleaning of All Units',
            'booking_id' => null,
            'storage_id' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        
        $expenses[] = [
            'amount' => 1800000,
            'category' => 'Others',
            'date' => $twoMonthsAgo->copy()->addDays(8)->toDateString(),
            'description' => 'Alarm System Upgrade',
            'booking_id' => null,
            'storage_id' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        
        $expenses = array_map(fn($r) => $this->sanitizeRow('tb_operational_expenses', $r), $expenses);
        DB::table('tb_operational_expenses')->insert($expenses);

        // ----------------------------
        // tb_app_settings (untuk electricity rate)
        // ----------------------------
        $appSettings = [
            [
                'key' => 'electricity_daily_rate',
                'value' => '2500',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        $appSettings = array_map(fn($r) => $this->sanitizeRow('tb_app_settings', $r), $appSettings);
        DB::table('tb_app_settings')->insert($appSettings);

        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
