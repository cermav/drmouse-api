<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('property_categories')->insert([
            ['name' => 'Vybavení ordinace', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'Hlavní zaměření', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'Specializace', 'created_at' => date("Y-m-d H:i:s")]
        ]);
    }
}
