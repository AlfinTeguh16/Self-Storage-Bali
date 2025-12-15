<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_storage_management', function (Blueprint $table) {
            $table->id();
            $table->foreignId('storage_id')->constrained('tb_storages')->onDelete('cascade')->nullable();
            $table->foreignId('booking_id')->constrained('tb_bookings')->onDelete('cascade')->nullable();
            $table->enum('status', ['available', 'booked', 'maintenance', 'cleaning', 'overdue'])->default('available');
            $table->date('last_clean')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_storage_manajement');
    }
};
