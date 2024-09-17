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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string("user_email");
            $table->foreignId("user_transaction_id")->constrained()->cascadeOnDelete();
            $table->longText("review");
            $table->smallInteger("score");
            $table->timestamps();
            $table->foreign("user_email")->references("email")->on("users");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review');
    }
};
