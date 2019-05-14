<?php

use Illuminate\Database\Seeder;

class WeekdaysTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('weekdays')->delete();
        
        \DB::table('weekdays')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Pondělí',
                'created_at' => '2019-01-24 11:10:51',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Úterý',
                'created_at' => '2019-01-24 11:10:51',
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Středa',
                'created_at' => '2019-01-24 11:10:51',
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Čtvrtek',
                'created_at' => '2019-01-24 11:10:51',
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Pátek',
                'created_at' => '2019-01-24 11:10:51',
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Sobota',
                'created_at' => '2019-01-24 11:10:51',
                'updated_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Neděle',
                'created_at' => '2019-01-24 11:10:51',
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}