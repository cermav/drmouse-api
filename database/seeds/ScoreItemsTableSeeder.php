<?php

use Illuminate\Database\Seeder;

class ScoreItemsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('score_items')->delete();
        
        \DB::table('score_items')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => 'Čistota',
                'created_at' => '2019-01-24 11:10:51',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => 'Ochota personálu',
                'created_at' => '2019-01-24 11:10:51',
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'title' => 'Doba čekání na oštření',
                'created_at' => '2019-01-24 11:10:51',
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'title' => 'Prostředí a vybavení kliniky',
                'created_at' => '2019-01-24 11:10:51',
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'title' => 'Efektivita ošetření / léčby',
                'created_at' => '2019-01-24 11:10:51',
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}