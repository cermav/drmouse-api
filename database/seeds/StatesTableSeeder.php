<?php

use Illuminate\Database\Seeder;

class StatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('states')->insert([
            ['name' => 'Draft', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'Published', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'Deleted', 'created_at' => date("Y-m-d H:i:s")]
        ]);
    }
}
