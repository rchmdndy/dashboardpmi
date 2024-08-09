<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class userSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('roles')->insert([
            ['name' => 'admin'],
            ['name' => 'manager'],
            ['name' => 'staff'],
            ['name' => 'customer'],
        ]);

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'phone' => '08123456789',
            'password' => 'adminadmin',
            'role_id' => 1
        ]);

    }
}
