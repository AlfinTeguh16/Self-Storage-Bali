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

        if (!Schema::hasTable('tb_customers')) {
            throw new \Exception('Table tb_customers must exist before creating tb_bookings');
        }

        Schema::create('tb_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('tb_customers')->onDelete('cascade');
            $table->foreignId('storage_id')->constrained('tb_storages')->onDelete('cascade');
            $table->string('booking_ref')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['success', 'pending', 'failed'])->default('pending');
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_bookings');
    }
};
