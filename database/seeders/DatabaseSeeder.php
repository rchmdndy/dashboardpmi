<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call(UserSeeder::class);

        DB::table('room_types')->insert([
            ['id' => 1, 'room_type' => 'Kamar Standard', 'capacity' => 2, 'price' => 200000.00, 'description' => 'Kamar dengan kapasitas 2 orang'],
            ['id' => 2, 'room_type' => 'Kamar Family', 'capacity' => 4, 'price' => 250000.00, 'description' => 'Kamar dengan kapasitas 4 orang'],
            ['id' => 3, 'room_type' => 'Meeting Room Kecil', 'capacity' => 25, 'price' => 100000.00, 'description' => 'Ruang pertemuan dengan kapasitas 15 orang'],
            ['id' => 4, 'room_type' => 'Meeting Room Sedang', 'capacity' => 50, 'price' => 250000.00, 'description' => 'Ruang pertemuan dengan kapasitas 35 orang'],
            ['id' => 5, 'room_type' => 'Meeting Room Besar', 'capacity' => 100, 'price' => 250000.00, 'description' => 'Ruang pertemuan dengan kapasitas 100 orang'],
            ['id' => 6, 'room_type' => 'Meeting Room Ekstra Besar', 'capacity' => 150, 'price' => 250000.00, 'description' => 'Ruang pertemuan dengan kapasitas 150 orang'],
        ]);

        DB::table('rooms')->insert([
            ['id' => 1, 'room_type_id' => 1, 'room_name' => '201', 'parent_id' => null],
            ['id' => 2, 'room_type_id' => 1, 'room_name' => '202', 'parent_id' => null],
            ['id' => 3, 'room_type_id' => 2, 'room_name' => '203', 'parent_id' => null],
            ['id' => 4, 'room_type_id' => 2, 'room_name' => '204', 'parent_id' => null],
            ['id' => 5, 'room_type_id' => 2, 'room_name' => '205', 'parent_id' => null],
            ['id' => 6, 'room_type_id' => 2, 'room_name' => '206', 'parent_id' => null],
            ['id' => 7, 'room_type_id' => 2, 'room_name' => '207', 'parent_id' => null],
            ['id' => 8, 'room_type_id' => 2, 'room_name' => '208', 'parent_id' => null],
            ['id' => 9, 'room_type_id' => 2, 'room_name' => '209', 'parent_id' => null],
            ['id' => 10, 'room_type_id' => 2, 'room_name' => '210', 'parent_id' => null],
            ['id' => 11, 'room_type_id' => 2, 'room_name' => '211', 'parent_id' => null],
            ['id' => 12, 'room_type_id' => 2, 'room_name' => '212', 'parent_id' => null],
            ['id' => 13, 'room_type_id' => 2, 'room_name' => '213', 'parent_id' => null],
            ['id' => 14, 'room_type_id' => 2, 'room_name' => '214', 'parent_id' => null],
            ['id' => 15, 'room_type_id' => 2, 'room_name' => '215', 'parent_id' => null],
            ['id' => 16, 'room_type_id' => 2, 'room_name' => '216', 'parent_id' => null],
            ['id' => 17, 'room_type_id' => 2, 'room_name' => '217', 'parent_id' => null],
            ['id' => 18, 'room_type_id' => 2, 'room_name' => '218', 'parent_id' => null],
            ['id' => 19, 'room_type_id' => 2, 'room_name' => '219', 'parent_id' => null],
            ['id' => 20, 'room_type_id' => 2, 'room_name' => '220', 'parent_id' => null],
            ['id' => 21, 'room_type_id' => 2, 'room_name' => '221', 'parent_id' => null],
            ['id' => 22, 'room_type_id' => 5, 'room_name' => 'Merapi Lt 1', 'parent_id' => null],
            ['id' => 23, 'room_type_id' => 3, 'room_name' => 'Telomoyo Lt 1', 'parent_id' => null],
            ['id' => 24, 'room_type_id' => 5, 'room_name' => 'Merapi Lt 3', 'parent_id' => 27],
            ['id' => 25, 'room_type_id' => 4, 'room_name' => 'Sindoro Lt 1', 'parent_id' => 22],
            ['id' => 26, 'room_type_id' => 4, 'room_name' => 'Sumbing Lt 1', 'parent_id' => 22],
            ['id' => 27, 'room_type_id' => 6, 'room_name' => 'Hall Besar Lt 3', 'parent_id' => null],
            ['id' => 28, 'room_type_id' => 5, 'room_name' => 'Merbabu Lt 3', 'parent_id' => 27],
        ]);

        DB::table('packages')->insert([
            ['name' => 'Paket Meeting', 'price_per_person' => 200000.00, 'min_person_quantity' => 20, 'hasLodgeRoom' => false, 'hasMeetingRoom' => true, 'image' => 'package_room_1.jpeg'],
            ['name' => 'Paket Full Board', 'price_per_person' => 250000.00, 'min_person_quantity' => 20, 'hasLodgeRoom' => true, 'hasMeetingRoom' => true, 'image' => 'package_room_2.jpeg'],
        ]);
    }
}
