<?php
declare(strict_types=1);

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
        $this->deleteAll();

        // load doctors from old database
        $rows = DB::select("
          SELECT d.*, a.street, a.zip_code, a.city, a.country, inf.doctors_count, inf.nurses_count, inf.others_count
          FROM drmouse_old.doctor_doctors AS d
          LEFT JOIN drmouse_old.doctor_address AS a ON d.id = a.doctor_id
          LEFT JOIN drmouse_old.doctor_staff_info AS inf ON d.id = inf.doc_id
          WHERE d.parent_doctor_id = 0
        ");
        foreach ($rows as $row) {

            // create user
            $user = new App\User();
            $user->password = Hash::make('Furier8');
            $user->email = empty($row->email) || User::where('email', '=', $row->email) ? $row->slug . '@drmouse.cz' : $row->email;
            $user->name = $row->name;
            $user->avatar = str_replace("https://www.drmouse.cz/new/wp-content/themes/DrMouse2/img/", "", $row->photo_url);
            $user->save();

            // create doctor record
            $doctor = $this->createDoctor($user->id, $row);

            // migrate services
            foreach (DB::select("SELECT service_id, price FROM drmouse_old.doctor_price WHERE doctor_id = " . $row->id) as $item) {
                \App\Models\DoctorsService::create([
                    'user_id' => $user->id,
                    'service_id' => $this->mapService($item->service_id),
                    'price' => $item->price
                ]);
            }

            // migrate properties
            foreach (DB::select("SELECT cat_val_id FROM drmouse_old.doctor_info WHERE doctor_id = " . $row->id) as $item) {
                \App\Models\DoctorsProperty::create([
                    'user_id' => $user->id,
                    'property_id' => $item->cat_val_id
                ]);
            }


//            dd($doctor);

        }

    }

    /***
     * Remove all necessary data
     */
    private function deleteAll()
    {
        DB::table('doctors_properties')->delete();
        DB::table('doctors_services')->delete();
        DB::table('doctors')->delete();
        DB::table('users')->delete();
    }

    /***
     * Create database record
     *
     * @param int $user_id
     * @param stdClass $data
     * @return \App\Doctor
     */
    private function createDoctor(int $user_id, stdClass $data) : \App\Doctor
    {
        return App\Doctor::create([
            'user_id' => $user_id,
            'state_id' => $data->status,
            'description' => $data->description,
            'slug' => $data->slug,
            'speaks_english' => $data->speaks_english,
            'phone' => $data->phone,
            'second_phone' => $data->phone2,
            'website' => $data->website,
            'street' => $data->street,
            'city' => $data->city,
            'country' => $data->country,
            'post_code' => $data->zip_code,
            'latitude' => $data->latitude,
            'longitude' => $data->longitude,
            'working_doctors_count' => $data->doctors_count,
            'working_doctors_names' => '',
            'nurses_count' => $data->nurses_count,
            'other_workers_count' => $data->others_count,
            'gdpr_agreed' => 0,
            'gdpr_agreed_date' => null,
            'profile_completedness' => $data->profile_completedness,
            'created_at' => $data->date_create,
            'updated_at' => $data->date_modified,
            'search_name' => $data->name
        ]);
    }

    /***
     * Convert old service ID to new service ID
     * @param int $serviceId
     * @return int
     */
    private function mapService(int $serviceId) : int
    {
        $mapping = [
            9 => 5, 10 => 6, 36 => 7, 37 => 8, 38 => 9,
            41 => 10, 45 => 11, 46 => 12, 47 => 13, 48 => 14,
            50 => 15, 51 => 16, 52 => 16, 53 => 12, 72 => 1
        ];
        return array_key_exists($serviceId, $mapping) ? $mapping[$serviceId] : $serviceId;
    }

}
