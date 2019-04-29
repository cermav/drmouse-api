<?php
/**
 * Created by PhpStorm.
 * User: petr
 * Date: 26.04.2019
 * Time: 16:33
 */

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // clear all tables related to doctor
        DB::table('doctors')->delete();
        DB::table('users')->delete();

        // load doctors from old database
        $rows = DB::select("
          SELECT p.* 
          FROM drmouse_old.wp_mouse_new_posts AS p
          LEFT JOIN drmouse_old.doctor_address AS a ON 
          WHERE p.post_type = 'ordinace'");
        foreach ($rows as $row) {

            // get doctor detail
            $postId = intval($row->ID);
            $metas = DB::select("SELECT * FROM drmouse_old.wp_mouse_new_postmeta WHERE post_id = {$postId}");
            $detail = [];
            foreach ($metas as $meta) {
                $detail[$meta->meta_key] = $meta->meta_value;
            }

            // create user
            $user = new App\User();
            $user->password = Hash::make('Furier8');
            $user->email = $detail['email'];
            $user->name = $row->post_title;
            $user->save();

            $doctor = [
                'user_id' => ,
                'state_id' => ,
                'description' => $detail[''],
                'slug' => $detail[''],
                'speaks_english' => $detail[''],
                'phone' => $detail['phone'],
                'second_phone' => $detail['phone2'],
                'website' => $detail['www'],
                'street' => $detail['address'],
                'city' => '',
                'country' => '',
                'post_code' => '',
                'latitude' => ,
                'longitude' => ,
                'working_doctors_count' => ,
                'working_doctors_names' => ,
                'nurses_count' => ,
                'other_workers_count' => ,
                'gdpr_agreed' => ,
                'gdpr_agreed_date' => ,
                'profile_completedness' => ,
                'created_at' => ,
                'updated_at' => ,
                'search_name' => ,
            ];


            dd($row);

        }




    }
}
