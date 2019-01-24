<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('permissions')->delete();
        
        \DB::table('permissions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'key' => 'browse_admin',
                'table_name' => NULL,
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            1 => 
            array (
                'id' => 2,
                'key' => 'browse_bread',
                'table_name' => NULL,
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            2 => 
            array (
                'id' => 3,
                'key' => 'browse_database',
                'table_name' => NULL,
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            3 => 
            array (
                'id' => 4,
                'key' => 'browse_media',
                'table_name' => NULL,
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            4 => 
            array (
                'id' => 5,
                'key' => 'browse_compass',
                'table_name' => NULL,
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            5 => 
            array (
                'id' => 6,
                'key' => 'browse_menus',
                'table_name' => 'menus',
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            6 => 
            array (
                'id' => 7,
                'key' => 'read_menus',
                'table_name' => 'menus',
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            7 => 
            array (
                'id' => 8,
                'key' => 'edit_menus',
                'table_name' => 'menus',
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            8 => 
            array (
                'id' => 9,
                'key' => 'add_menus',
                'table_name' => 'menus',
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            9 => 
            array (
                'id' => 10,
                'key' => 'delete_menus',
                'table_name' => 'menus',
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            10 => 
            array (
                'id' => 11,
                'key' => 'browse_roles',
                'table_name' => 'roles',
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            11 => 
            array (
                'id' => 12,
                'key' => 'read_roles',
                'table_name' => 'roles',
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            12 => 
            array (
                'id' => 13,
                'key' => 'edit_roles',
                'table_name' => 'roles',
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            13 => 
            array (
                'id' => 14,
                'key' => 'add_roles',
                'table_name' => 'roles',
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            14 => 
            array (
                'id' => 15,
                'key' => 'delete_roles',
                'table_name' => 'roles',
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            15 => 
            array (
                'id' => 16,
                'key' => 'browse_users',
                'table_name' => 'users',
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            16 => 
            array (
                'id' => 17,
                'key' => 'read_users',
                'table_name' => 'users',
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            17 => 
            array (
                'id' => 18,
                'key' => 'edit_users',
                'table_name' => 'users',
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            18 => 
            array (
                'id' => 19,
                'key' => 'add_users',
                'table_name' => 'users',
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            19 => 
            array (
                'id' => 20,
                'key' => 'delete_users',
                'table_name' => 'users',
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            20 => 
            array (
                'id' => 21,
                'key' => 'browse_settings',
                'table_name' => 'settings',
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            21 => 
            array (
                'id' => 22,
                'key' => 'read_settings',
                'table_name' => 'settings',
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            22 => 
            array (
                'id' => 23,
                'key' => 'edit_settings',
                'table_name' => 'settings',
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            23 => 
            array (
                'id' => 24,
                'key' => 'add_settings',
                'table_name' => 'settings',
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            24 => 
            array (
                'id' => 25,
                'key' => 'delete_settings',
                'table_name' => 'settings',
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            25 => 
            array (
                'id' => 26,
                'key' => 'browse_hooks',
                'table_name' => NULL,
                'created_at' => '2018-12-18 10:05:35',
                'updated_at' => '2018-12-18 10:05:35',
            ),
            26 => 
            array (
                'id' => 27,
                'key' => 'browse_doctors',
                'table_name' => 'doctors',
                'created_at' => '2019-01-24 13:23:41',
                'updated_at' => '2019-01-24 13:23:41',
            ),
            27 => 
            array (
                'id' => 28,
                'key' => 'read_doctors',
                'table_name' => 'doctors',
                'created_at' => '2019-01-24 13:23:41',
                'updated_at' => '2019-01-24 13:23:41',
            ),
            28 => 
            array (
                'id' => 29,
                'key' => 'edit_doctors',
                'table_name' => 'doctors',
                'created_at' => '2019-01-24 13:23:41',
                'updated_at' => '2019-01-24 13:23:41',
            ),
            29 => 
            array (
                'id' => 30,
                'key' => 'add_doctors',
                'table_name' => 'doctors',
                'created_at' => '2019-01-24 13:23:41',
                'updated_at' => '2019-01-24 13:23:41',
            ),
            30 => 
            array (
                'id' => 31,
                'key' => 'delete_doctors',
                'table_name' => 'doctors',
                'created_at' => '2019-01-24 13:23:41',
                'updated_at' => '2019-01-24 13:23:41',
            ),
        ));
        
        
    }
}