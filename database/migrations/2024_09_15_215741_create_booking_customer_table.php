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
        Schema::create('booking_customer', function (Blueprint $table) {
            $table->id();
            $table->string("customer_nik");
            $table->unsignedBigInteger("booking_id");
            $table->foreign("customer_nik")->references("nik")->on('customer')->cascadeOnDelete();
            $table->foreign("booking_id")->references("id")->on('bookings')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_customer');
    }
};
