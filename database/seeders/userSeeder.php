<?php

namespace Database\Seeders;

use App\Models\User;
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

        User::factory()->create(
            [
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'phone' => '08123456789',
            'password' => 'adminadmin',
            'role_id' => 1,
            ]
    );

    User::factory()->create(
        [
        'name' => 'staff',
        'email' => 'staff@staff.com',
        'phone' => '089213138001',
        'password' => 'staffstaff',
        'role_id' => 3,
        ]
);

    User::factory()->create(
        [
        'name' => 'customer1',
        'email' => 'customer1@customer.com',
        'phone' => '08924324321',
        'password' => 'customer1',
        'role_id' => 4,
        ]
    );

    }
}
