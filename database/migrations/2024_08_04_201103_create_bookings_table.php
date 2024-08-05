<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_bookings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('user_email');
            $table->unsignedBigInteger('room_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('amount');
            $table->decimal('total_price', 0, 0);
            $table->timestamps();

            $table->foreign('room_id')->references('id')->on('rooms');
            $table->foreign('user_transaction_id')->references('id')->on('user_transactions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
