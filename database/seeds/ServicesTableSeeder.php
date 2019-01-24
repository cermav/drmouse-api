<?php

use Illuminate\Database\Seeder;

class ServicesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('services')->delete();
        
        \DB::table('services')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Úkon základního vyšetření',
                'show_on_registration' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'is_approved' => 0,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Základní vakcinace',
                'show_on_registration' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'is_approved' => 0,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Základní kastrace pes',
                'show_on_registration' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'is_approved' => 0,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Základní kastrace fena',
                'show_on_registration' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'is_approved' => 0,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Odčervení',
                'show_on_registration' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
                'is_approved' => 0,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Pitva',
                'show_on_registration' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
                'is_approved' => 0,
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Vakcinace pes',
                'show_on_registration' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
                'is_approved' => 0,
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Vakcinace kočka',
                'show_on_registration' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
                'is_approved' => 0,
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Čipování',
                'show_on_registration' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
                'is_approved' => 0,
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Čištění zubů ultrazvukem',
                'show_on_registration' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
                'is_approved' => 0,
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'Kastrace kočky laparoskopicky',
                'show_on_registration' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
                'is_approved' => 0,
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'Kastrace kocoura laparoskopicky',
                'show_on_registration' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
                'is_approved' => 0,
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'Kastrace feny laparoskopicky',
                'show_on_registration' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
                'is_approved' => 0,
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'Kastrace psa laparoskopicky',
                'show_on_registration' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
                'is_approved' => 0,
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'Preventivní péče o zvířecí seniory',
                'show_on_registration' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
                'is_approved' => 0,
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'Odstranění zubního kamene ultrazvukem',
                'show_on_registration' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
                'is_approved' => 0,
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'Kastrace kocoura laparoskopicky',
                'show_on_registration' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
                'is_approved' => 0,
            ),
        ));
        
        
    }
}