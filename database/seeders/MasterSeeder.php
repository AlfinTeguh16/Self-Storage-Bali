<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use App\Models\User;
use App\Models\Customer;
use App\Models\Storage;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\StorageManagement;

class MasterSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // 1. User
        User::create([
            'username' => 'admin',
            'password' => Hash::make('password'), // default password
            'role' => 'admin',
        ]);

        // 2. Customers
        $customers = [];
        for ($i = 1; $i <= 5; $i++) {
            $customers[] = Customer::create([
                'name' => $faker->name,
                'address' => $faker->address,
                'email' => $faker->safeEmail,
                'phone' => $faker->phoneNumber,
                'credential' => $faker->uuid,
                'is_deleted' => false,
            ]);
        }

        // 3. Storages
        $storages = [];
        foreach (['1x1', '2x2', '3x3'] as $size) {
            $storages[] = Storage::create([
                'size' => $size,
                'price' => $faker->numberBetween(50000, 150000),
                'description' => $faker->sentence,
                'is_deleted' => false,
            ]);
        }

        // 4. Bookings
        $bookings = [];
        foreach ($customers as $customer) {
            $start = $faker->dateTimeBetween('-10 days', '+10 days');
            $end = (clone $start)->modify('+7 days');

            $bookings[] = Booking::create([
                'customer_id' => $customer->id,
                'booking_ref' => strtoupper(Str::random(8)),
                'start_date' => $start->format('Y-m-d'),
                'end_date' => $end->format('Y-m-d'),
                'notes' => $faker->sentence,
                'is_deleted' => false,
            ]);
        }

        // 5. Payments
        foreach ($customers as $customer) {
            Payment::create([
                'customer_id' => $customer->id,
                'method' => $faker->randomElement(['transfer', 'qris', 'cash']),
                'transaction_file' => 'bukti_transfer_' . Str::random(6) . '.jpg',
                'is_deleted' => false,
            ]);
        }

        // 6. Storage Management
        foreach ($bookings as $booking) {
            $randomStorage = $faker->randomElement($storages);
            StorageManagement::create([
                'storage_id' => $randomStorage->id,
                'booking_id' => $booking->id,
                'status' => $faker->randomElement(['available', 'booked', 'maintenance']),
                'last_clean' => $faker->date(),
                'is_deleted' => false,
            ]);
        }
    }
}
