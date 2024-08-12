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

        DB::table('room_types')->insert([
            ['id' => 1, 'room_type' => 'kamar_standard', 'capacity' => 2, 'price' => 200000.00, 'description' => 'Kamar dengan kapasitas 2 orang'],
            ['id' => 2, 'room_type' => 'kamar_family', 'capacity' => 4, 'price' => 250000.00, 'description' => 'Kamar dengan kapasitas 4 orang'],
            ['id' => 3, 'room_type' => 'meeting_room_kecil', 'capacity' => 25, 'price' => 100000.00, 'description' => 'Ruang pertemuan dengan kapasitas 15 orang'],
            ['id' => 4, 'room_type' => 'meeting_room_sedang', 'capacity' => 50, 'price' => 250000.00, 'description' => 'Ruang pertemuan dengan kapasitas 35 orang'],
            ['id' => 5, 'room_type' => 'meeting_room_besar', 'capacity' => 100, 'price' => 250000.00, 'description' => 'Ruang pertemuan dengan kapasitas 100 orang'],
            ['id' => 6, 'room_type' => 'meeting_room_ekstra_besar', 'capacity' => 150, 'price' => 250000.00, 'description' => 'Ruang pertemuan dengan kapasitas 150 orang'],
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
            ['id' => 22, 'room_type_id' => 5, 'room_name' => 'A', 'parent_id' => null],
            ['id' => 23, 'room_type_id' => 3, 'room_name' => 'D', 'parent_id' => 22],
            ['id' => 24, 'room_type_id' => 5, 'room_name' => 'B', 'parent_id' => null],
            ['id' => 25, 'room_type_id' => 4, 'room_name' => 'E', 'parent_id' => 24],
            ['id' => 26, 'room_type_id' => 4, 'room_name' => 'F', 'parent_id' => 24],
            ['id' => 27, 'room_type_id' => 6, 'room_name' => 'C', 'parent_id' => null],
            ['id' => 28, 'room_type_id' => 5, 'room_name' => 'G', 'parent_id' => 27],
            ['id' => 29, 'room_type_id' => 5, 'room_name' => 'H', 'parent_id' => 27],
        ]);

        $roomTypes = [
            'kamar_standard',
            'kamar_family',
            'meeting_room_kecil',
            'meeting_room_sedang',
            'meeting_room_besar',
            'meeting_room_ekstra_besar'
        ];

        $roomImages = [];

        foreach ($roomTypes as $roomType) {
            for ($i = 1; $i <= 3; $i++) {
                $roomImages[] = [
                    'room_type_id' => DB::table('room_types')->where('room_type', $roomType)->value('id'),
                    'image_path' => "{$roomType}_{$i}.jpg"
                ];
            }
        }

        DB::table('room_images')->insert($roomImages);

        DB::table('packages')->insert([
           ['name' => 'Paket Meeting', 'price_per_person' => 200000.00, 'min_person_quantity' => 20, 'hasLodgeRoom' => false, 'hasMeetingRoom' => true],
           ['name' => 'Paket Full Board', 'price_per_person' => 250000.00, 'min_person_quantity' => 20, 'hasLodgeRoom' => true, 'hasMeetingRoom' => true]
        ]);
    }
}
