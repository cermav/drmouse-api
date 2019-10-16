<?php
declare(strict_types=1);

use App\Enum\OpeningHoursState;
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
          
          ORDER BY d.name          
          
            -- AND EXISTS(SELECT 1 FROM drmouse_old.doctor_info WHERE doctor_id = d.id)
            -- AND slug = 'mvdr-zdenek-andreas'
        ");
        foreach ($rows as $row) {

            // create user
            $user = new App\User();
            $user->role_id = 2;
            $user->password = Hash::make('Furier8');
            $user->email = (empty($row->email) || \App\User::where('email', '=', $row->email)->exists()) ? $row->slug . '_' . \Illuminate\Support\Str::random(8) . '@drmouse.cz' : $row->email;
            // $user->email = empty($row->email) ? $row->slug . '_' . \Illuminate\Support\Str::random(8) . '@drmouse.cz' : $row->email;
            $user->name = $row->name;
            $user->avatar = str_replace("https://www.drmouse.cz/new/wp-content/themes/DrMouse2/img/", "", $row->photo_url);
            $user->save();

            // create doctor record
            $doctor = $this->createDoctor($user->id, $row);

            // migrate services
            $this->createService($user->id, $row->id);

            // migrate properties
            $this->createProperties($user->id, $row->id);

            // migrate opening hours
            $this->createOpeningHours($user->id, $row->id);

            // migrate images
            $this->createPhotos($user->id, $row->id);


//            dd($doctor);

        }

    }

    /***
     * Remove all necessary data
     */
    private function deleteAll()
    {
        DB::table('photos')->delete();
        DB::table('opening_hours')->delete();
        DB::table('doctors_properties')->delete();
        DB::table('doctors_services')->delete();
        DB::table('doctors')->delete();
        DB::table('users')->where('role_id', '=', 2)->delete();
    }

    /***
     * Create doctor database record
     * @param int $user_id
     * @param stdClass $data
     * @return \App\Doctor
     */
    private function createDoctor(int $userId, stdClass $data) : \App\Doctor
    {
        return App\Doctor::create([
            'user_id' => $userId,
            'state_id' => $this->mapStatus(intval($data->status)),
            'description' => $data->description,
            'slug' => $this->getSlug($userId, $data->slug),
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
            'created_at' => $this->convertDate($data->date_create),
            'updated_at' => $this->convertDate($data->date_modified),
            'search_name' => $data->name
        ]);
    }

    /***
     * Genereate unique slug
     * @param int $userId
     * @param string $slug
     * @return string
     */
    private function getSlug(int $userId, string $slug) : string
    {
        $index = 0;
        $new_slug = $slug;
        while ( \App\Doctor::where('slug', '=', $new_slug)->where('user_id', '!=', $userId)->exists() ) {
            $new_slug = $slug . '-' . ++$index;
        }
        return $new_slug;
    }

    /***
     * Prevent empty date
     * @param string $dateString
     * @return string
     */
    private function convertDate(string $dateString) : string
    {
        if ($dateString == '0000-00-00 00:00:00') {
            return date('Y-m-d H:i:s');
        }
        return $dateString;
    }

    /***
     * Migrate services     *
     * @param int $userId
     * @param int $originalDoctorId
     */
    private function createService(int $userId, int $originalDoctorId)
    {
        foreach (DB::select("SELECT service_id, price FROM drmouse_old.doctor_price WHERE doctor_id = " . $originalDoctorId) as $item) {
            if (in_array($item->service_id, [0, 49])) continue; // skip incorrect values
            try {
                \App\Models\DoctorsService::create([
                    'user_id' => $userId,
                    'service_id' => $this->mapService(intval($item->service_id)),
                    'price' => $item->price
                ]);
            }
            catch (Throwable $tr) {
                dd($item);
            }

        }
    }


    /***
     * Migrate properities
     * @param int $userId
     * @param int $originalDoctorId
     */
    private function createProperties(int $userId, int $originalDoctorId)
    {
        foreach (DB::select("SELECT cat_val_id FROM drmouse_old.doctor_info WHERE doctor_id = " . $originalDoctorId) as $item) {

            if ( \App\Property::where('id', '=',  $item->cat_val_id)->exists() )
            {
                try {
                    \App\Models\DoctorsProperty::create([
                        'user_id' => $userId,
                        'property_id' => $item->cat_val_id
                    ]);
                }
                catch (Throwable $tr) {
                    dd($item);
                }
            }
        }
    }

    /***
     * Migrate opening hours
     * @param int $userId
     * @param int $originalDoctorId
     */
    private function createOpeningHours(int $userId, int $originalDoctorId)
    {
        foreach (DB::select("SELECT weekday_index, opening_hour, closing_hour, is_non_stop, is_closed FROM drmouse_old.doctor_workshift WHERE doctor_id = " . $originalDoctorId) as $item) {

            // arrange weekday
            $weekdayId = $item->weekday_index + 1;

            if ( \App\Weekday::where('id', '=',  $weekdayId)->exists() )
            {
                \App\Models\OpeningHour::create([
                    'weekday_id' => ($item->weekday_index + 1),
                    'user_id' => $userId,
                    'opening_hours_state_id' => ($item->is_non_stop == 1 ? OpeningHoursState::NONSTOP : ( $item->is_closed == 1 ? OpeningHoursState::CLOSED : OpeningHoursState::OPEN )),
                    'open_at' => $item->opening_hour,
                    'close_at' => $item->closing_hour
                ]);
            }
        }
    }

    /***
     * Migrate photos
     * @param int $userId
     * @param int $originalDoctorId
     */
    private function createPhotos(int $userId, int $originalDoctorId)
    {
        foreach (DB::select("SELECT image_url, position FROM drmouse_old.doctor_gallery WHERE doctor_id = " . $originalDoctorId) as $item) {

            try {

                // get relative path to image
                $path = str_replace('https://www.drmouse.cz/wp-content/uploads/', '', $item->image_url);
                $path = str_replace('https://www.drmouse.cz/new/wp-content/uploads/', '', $path);

                // save file to disk
                Storage::disk('public')->put($path, file_get_contents($item->image_url));

                \App\Models\Photo::create([
                    'user_id' => $userId,
                    'path' => $path,
                    'position' => $item->position
                ]);
            }
            catch (Exception $ex) {
               error_log("Import image error (" . $ex->getMessage() . ") - " . $item->image_url);
            }
        }
    }

    /***
     * Convert old status to new status
     * @param int $statusId
     * @return int
     */
    private function mapStatus(int $statusId) : int
    {
        switch($statusId) {
            case 1: return \App\Enum\DoctorStatus::DRAFT;
            case 2: return \App\Enum\DoctorStatus::PUBLISHED;
            case 3: return \App\Enum\DoctorStatus::UNPUBLISHED;
            case -999: return \App\Enum\DoctorStatus::DELETED;
        }
        return \App\Enum\DoctorStatus::NEW;
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
