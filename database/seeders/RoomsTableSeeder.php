<?php
// database/seeders/RoomsTableSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rooms')->insert([
            ['id' => 1, 'room_type_id' => 1, 'room_name' => '201', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 2, 'room_type_id' => 1, 'room_name' => '202', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 3, 'room_type_id' => 2, 'room_name' => '203', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 4, 'room_type_id' => 2, 'room_name' => '204', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 5, 'room_type_id' => 2, 'room_name' => '205', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 6, 'room_type_id' => 2, 'room_name' => '206', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 7, 'room_type_id' => 2, 'room_name' => '207', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 8, 'room_type_id' => 2, 'room_name' => '208', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 9, 'room_type_id' => 2, 'room_name' => '209', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 10, 'room_type_id' => 2, 'room_name' => '210', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 11, 'room_type_id' => 2, 'room_name' => '211', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 12, 'room_type_id' => 2, 'room_name' => '212', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 13, 'room_type_id' => 2, 'room_name' => '213', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 14, 'room_type_id' => 2, 'room_name' => '214', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 15, 'room_type_id' => 2, 'room_name' => '215', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 16, 'room_type_id' => 2, 'room_name' => '216', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 17, 'room_type_id' => 2, 'room_name' => '217', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 18, 'room_type_id' => 2, 'room_name' => '218', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 19, 'room_type_id' => 2, 'room_name' => '219', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 20, 'room_type_id' => 2, 'room_name' => '220', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 21, 'room_type_id' => 2, 'room_name' => '221', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 22, 'room_type_id' => 5, 'room_name' => 'A', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 23, 'room_type_id' => 3, 'room_name' => 'D', 'parent_id' => 22, 'isAvailable' => 1],
            ['id' => 24, 'room_type_id' => 5, 'room_name' => 'B', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 25, 'room_type_id' => 4, 'room_name' => 'E', 'parent_id' => 24, 'isAvailable' => 1],
            ['id' => 26, 'room_type_id' => 4, 'room_name' => 'F', 'parent_id' => 24, 'isAvailable' => 1],
            ['id' => 27, 'room_type_id' => 6, 'room_name' => 'C', 'parent_id' => null, 'isAvailable' => 1],
            ['id' => 28, 'room_type_id' => 5, 'room_name' => 'G', 'parent_id' => 27, 'isAvailable' => 1],
            ['id' => 29, 'room_type_id' => 5, 'room_name' => 'H', 'parent_id' => 27, 'isAvailable' => 1],
        ]);
    }
}
