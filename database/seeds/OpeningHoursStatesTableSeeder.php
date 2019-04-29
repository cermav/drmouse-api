<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OpeningHoursStatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('opening_hours_states')->delete();
        DB::table('opening_hours_states')->insert([
            ['name' => 'Otevřeno', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'Zavřeno', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'Nonstop', 'created_at' => date("Y-m-d H:i:s")],
        ]);
    }
}
