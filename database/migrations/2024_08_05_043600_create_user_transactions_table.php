<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_user_transactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_transactions', function (Blueprint $table) {
            $table->string('user_email');
            $table->string('order_id', 255);
            $table->unsignedBigInteger('booking_id');
            $table->date('transaction_date');
            $table->enum('transaction_status', ['failed', 'pending', 'success']);
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('bookings');
            $table->foreign('user_email')->references('email')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_transactions');
    }
};
