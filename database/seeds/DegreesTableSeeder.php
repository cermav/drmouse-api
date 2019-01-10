<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DegreesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('degrees')->insert([
            ['name' => 'Bc.', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'CSc.', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'Doc.', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'DrSc.', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'DSc.', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'Ing.', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'JUDr.', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'Mgr.', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'MUDr.', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'MVDr.', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'PhMr.', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'PharmDr.', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'Ph.D.', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'PhD.', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'RNDr.', 'created_at' => date("Y-m-d H:i:s")],
        ]);
    }
}
        