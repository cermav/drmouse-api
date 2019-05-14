<?php

use Illuminate\Database\Seeder;

class StatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('states')->delete();
        
        \DB::table('states')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'New',
                'created_at' => '2019-01-24 11:10:51',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Draft',
                'created_at' => '2019-01-24 11:10:51',
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Published',
                'created_at' => '2019-01-24 11:10:51',
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Unpublished',
                'created_at' => '2019-01-24 11:10:51',
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Deleted',
                'created_at' => '2019-01-24 11:10:51',
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}