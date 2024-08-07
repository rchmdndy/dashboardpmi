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
            $table->id();
            $table->string('user_email');
            $table->string('order_id', 255)->unique();
            $table->string('snap_token')->nullable();
            $table->date('transaction_date');
            $table->integer('amount');
            $table->decimal('total_price', 0, 0);
            $table->enum('transaction_status', ['failed', 'pending', 'success']);
            $table->timestamps();

            $table->foreign('user_email')->references('email')->on('users');
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('user_email');
            $table->unsignedBigInteger('user_transaction_id');
            $table->unsignedBigInteger('room_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();

            $table->foreign('room_id')->references('id')->on('rooms');
            $table->foreign('user_transaction_id')->references('id')->on('user_transactions');
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
