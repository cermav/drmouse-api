<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WeekdaysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('weekdays')->delete();

        \DB::table('weekdays')->insert([
            ['name' => 'Pondělí', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'Úterý', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'Středa', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'Čtvrtek', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'Pátek', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'Sobota', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'Neděle', 'created_at' => date("Y-m-d H:i:s")]
        ]);
    }
}
