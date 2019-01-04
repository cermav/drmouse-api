<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScoreItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('score_items')->insert([
            ['title' => 'Čistota', 'created_at' => date("Y-m-d H:i:s")],
            ['title' => 'Ochota personálu', 'created_at' => date("Y-m-d H:i:s")],
            ['title' => 'Doba čekání na oštření', 'created_at' => date("Y-m-d H:i:s")],
            ['title' => 'Prostředí a vybavení kliniky', 'created_at' => date("Y-m-d H:i:s")],
            ['title' => 'Efektivita ošetření / léčby', 'created_at' => date("Y-m-d H:i:s")],
        ]);
    }
}