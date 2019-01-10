<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Doctor;
use App\DoctorsLog;
use App\Weekday;
use App\OpeningHour;
use App\OpeningHoursState;
use App\PropertyCategory;
use App\Service;
use App\Degree;
use App\CzechName;

class DoctorController extends Controller {

    public function addDoctor() {
        $weekdays = Weekday::all();
        $openingHoursStates = OpeningHoursState::all();
        $propertyCategories = PropertyCategory::all();
        $services = Service::all();
        return view('add-doctor', compact('weekdays', 'openingHoursStates', 'propertyCategories', 'services'));
    }

    public function createDoctor(Request $request) {

       $request->validate([
            'name' => 'required|max:255',
            'email' => 'unique:users|required|email',
            'password' => 'required|min:6|confirmed',
            'description' => 'required',
            'street' => 'required|max:255',
            'post_code' => 'required|max:6',
            'city' => 'required|max:255',
            'phone' => 'required|max:20',
            'second_phone' => 'max:20',
            'website' => 'max:255',
            'gdpr_agreed' => 'required',
        ]);

        /* Create slug - if already exists, add the number at the end */
        $slug = strtolower(str_replace(" ", "-", preg_replace("/[^A-Za-z0-9 ]/", '', $request['name'])));
        $existingCount = Doctor::where('slug', 'like', $slug . '%')->count();
        if ($existingCount > 0) {
            $slug = $slug . '-' . ($existingCount);
        }
        
        /* Get longitude and latitude by the address */
        $location = $this->getLatLngFromAddress(trim($request['street']) . " " . trim($request['city']) . " " . trim($request['country']) . " " . trim($request['post_code']));

        /* Create user */
        $user = User::create([
                    'name' => $request['name'],
                    'email' => $request['email'],
                    'password' => Hash::make(trim($request['password'])),
                    'role_id' => 3
        ]);

        /* Create doctor */
        Doctor::create([
            'user_id' => $user->id,
            'state_id' => 1,
            'search_name' => $this->parseName($request['name']),
            'description' => $request['description'],
            'slug' => $slug,
            'speaks_english' => $request['speaks_english'],
            'street' => $request['street'],
            'post_code' => $request['post_code'],
            'city' => $request['city'],
            'latitude' => $location['latitude'],
            'longitude' => $location['longitude'],
            'phone' => $request['phone'],
            'second_phone' => $request['second_phone'],
            'website' => $request['website'],
            'working_doctors_count' => $request['working_doctors_count'],
            'working_doctors_names' => $request['working_doctors_names'],
            'nurses_count' => $request['nurses_count'],
            'other_workers_count' => $request['other_workers_count'],
            'gdpr_agreed' => 1,
            'gdpr_agreed_date' => date('Y-m-d H:i:s')
        ]);

        /* Save doctor's opening hours */
        foreach ($request['weekdays'] as $weekdayId => $weekday) {
            OpeningHour::create([
                'weekday_id' => $weekdayId,
                'user_id' => $user->id,
                'opening_hours_state_id' => (int) $weekday['state'],
                'open_at' => is_null($weekday['open_at']) ? $weekday['open_at'] : \DateTime::createFromFormat("H:i", $weekday['open_at'])->format("H:i"),
                'close_at' => is_null($weekday['close_at']) ? $weekday['close_at'] : \DateTime::createFromFormat("H:i", $weekday['close_at'])->format("H:i")
            ]);
        }

        /* Add properties to the doctor */
        $propertyCategories = PropertyCategory::all();
        foreach ($propertyCategories as $category) {
            foreach ($request['category_' . $category->id . '_properties'] as $property) {
                dump($property);
                $user->properties()->attach($property);
            }
        }

        /* Add services to the doctor */
        foreach ($request['service_prices'] as $serviceId => $price) {
            if (!is_null($price)) {
                $user->services()->attach($serviceId, compact('price'));
            }
        }
        
        
        /* Create a record in log table */
        DoctorsLog::create([
            'user_id' => $user->id,
            'state_id' => 1,
            'email_sent' => 1,
            'doctor_object' => serialize($user)
        ]);

        return redirect('/')->with('status', 'New doctor was added');
    }

    private function getLatLngFromAddress($address) {
        $address = str_replace(" ", "+", $address);

        $mapResponse = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&key=AIzaSyDSOUeQawvBZ2hCbyJFrxFRYGyjWrMKOsY");
        $mapResponseJson = json_decode($mapResponse);
        
        $latitude = $mapResponseJson->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
        $longitude = $mapResponseJson->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
        return compact('latitude', 'longitude');
    }
    
    private function parseName($name){
        $lcName = strtolower($name);
        $degrees = Degree::all();
        foreach ($degrees as $degree){
            $degreeName = strtolower($degree->name);
            if (strpos($lcName, $degreeName) !== false){
                $lcName = str_replace($degreeName, '', $lcName);
            }
        }
        $simpleName = ucwords($lcName);
        $nameParts = explode(" ", trim($simpleName));
        $czechNames = CzechName::pluck('name')->toArray();
        if (in_array($nameParts[0], $czechNames)){
            $firstName = $nameParts[0];
            unset($nameParts[0]);
            array_push($nameParts, $firstName); 
        }
        return implode(" ", $nameParts);
    }

}
