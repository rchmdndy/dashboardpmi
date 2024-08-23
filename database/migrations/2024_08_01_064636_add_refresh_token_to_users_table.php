<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefreshTokenToUsersTable extends Migration
{
    public function up() {}

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('refresh_token');
        });
    }
}
