<?php

use Illuminate\Database\Seeder;

class OpeningHoursStatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('opening_hours_states')->delete();
        
        \DB::table('opening_hours_states')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Otevřeno',
                'created_at' => '2019-04-29 13:41:00',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Zavřeno',
                'created_at' => '2019-04-29 13:41:00',
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Nonstop',
                'created_at' => '2019-04-29 13:41:00',
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}