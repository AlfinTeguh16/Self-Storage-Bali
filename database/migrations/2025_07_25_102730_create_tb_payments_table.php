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
        Schema::create('tb_payments', function (Blueprint $table) {
            $table->id();

                $table->foreignId('customer_id')
                    ->constrained('tb_customers')
                    ->onDelete('cascade');


                $table->foreignId('booking_id')
                    ->nullable()
                    ->constrained('tb_bookings')
                    ->onDelete('cascade');

                $table->string('method')->nullable();
                $table->string('status', 20)->default('pending');
                $table->text('transaction_file')->nullable();
                $table->text('payment_url')->nullable();
                $table->string('midtrans_order_id')->nullable();

                $table->boolean('is_deleted')->default(false);
                $table->timestamps();
            });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
