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
        Schema::create('tb_operational_expenses', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 12, 2);
            $table->string('category', 50); // Listrik, Kebersihan, Gaji, Lainnya
            $table->date('date');
            $table->text('description')->nullable();
            
            // Optional relations for context
            $table->foreignId('booking_id')->nullable()->constrained('tb_bookings')->nullOnDelete();
            $table->foreignId('storage_id')->nullable()->constrained('tb_storages')->nullOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_operational_expenses');
    }
};
