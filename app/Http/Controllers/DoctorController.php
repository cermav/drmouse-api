<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Doctor;
use App\Models\DoctorsLog;
use App\Models\Weekday;
use App\Models\OpeningHour;
use App\Models\OpeningHoursState;
use App\Models\PropertyCategory;
use App\Models\Property;
use App\Models\Service;
use App\Models\Photo;
use App\Models\Score;
use App\Models\ScoreItem;
use App\Http\Controllers\HelperController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;

class DoctorController extends Controller
{
    public function addDoctor()
    {
        $weekdays = Weekday::all();
        $openingHoursStates = OpeningHoursState::all();
        $propertyCategories = PropertyCategory::all();
        $services = Service::where('show_on_registration', '=', 1)->get();
        return view(
            'add-doctor',
            compact(
                'weekdays',
                'openingHoursStates',
                'propertyCategories',
                'services'
            )
        );
    }

    public function createDoctor(Request $request)
    {
        $request->validdate([
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
        $slug = strtolower(
            str_replace(
                " ",
                "-",
                preg_replace(
                    "/[^A-Za-z0-9 ]/",
                    '',
                    HelperController::replaceAccents($request['name'])
                )
            )
        );
        $existingCount = Doctor::where('slug', 'like', $slug . '%')->count();
        if ($existingCount > 0) {
            $slug = $slug . '-' . $existingCount;
        }

        /* Get longitude and latitude by the address */
        $location = HelperController::getLatLngFromAddress(
            trim($request['street']) .
                " " .
                trim($request['city']) .
                " " .
                trim($request['country']) .
                " " .
                trim($request['post_code'])
        );

        /* Create directories for uploads */
        $galleryPath = 'users/gallery/';
        $profilesPath = 'users/profiles/';

        /* Create user */
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make(trim($request['password'])),
            'role_id' => 3,
        ]);

        /* Create doctor */
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'state_id' => 1,
            'search_name' => HelperController::parseName($request['name']),
            'description' => $request['description'],
            'slug' => $slug,
            'speaks_english' => $request['speaks_english']
                ? $request['speaks_english']
                : 0,
            'street' => $request['street'],
            'post_code' => $request['post_code'],
            'city' => $request['city'],
            'latitude' => $location['latitude'],
            'longitude' => $location['longitude'],
            'phone' => "+420 " . $request['phone'],
            'second_phone' => $request['second_phone']
                ? "+420 " . $request['second_phone']
                : null,
            'website' => $request['website'],
            'working_doctors_count' => $request['working_doctors_count'],
            'working_doctors_names' => $request['working_doctors_names'],
            'nurses_count' => $request['nurses_count'],
            'other_workers_count' => $request['other_workers_count'],
            'gdpr_agreed' => 1,
            'gdpr_agreed_date' => date('Y-m-d H:i:s'),
        ]);

        if ($request["doc_profile_pic"]) {
            $base64File = $request['doc_profile_pic'];
            $encodedImgString = explode(',', $base64File, 2)[1];
            $decodedImgString = base64_decode($encodedImgString);
            $info = getimagesizefromstring($decodedImgString);
            $ext = explode('/', $info['mime']);
            @list($type, $file_data) = explode(';', $base64File);
            @list(, $file_data) = explode(',', $file_data);
            $imageName = 'profile_' . time() . '.' . $ext[1];
            $imagePath = $profilesPath . $user->id . '/' . $imageName;
            Storage::disk('public')->put($imagePath, base64_decode($file_data));
            $user->avatar = $imagePath;
            $user->save();
        }

        if ($request["doc_profile_pic2"]) {
            $url = $request["doc_profile_pic2"];
            $contents = file_get_contents($url);
            $imageName = 'profile_' . time() . '.png';
            $imagePath = $profilesPath . $user->id . '/' . $imageName;
            Storage::disk('public')->put($imagePath, $contents);
            $user->avatar = $imagePath;
            $user->save();
        }

        /* Save doctor's opening hours */
        foreach ($request['weekdays'] as $weekdayId => $weekdayItem) {
            foreach ($weekdayItem as $weekday) {
                $weekdayState = (int) $weekday['state'];
                OpeningHour::create([
                    'weekday_id' => $weekdayId,
                    'user_id' => $user->id,
                    'opening_hours_state_id' => $weekdayState,
                    'open_at' =>
                        $weekdayState === 1 && $weekday['open_at']
                            ? \DateTime::createFromFormat(
                                "H:i",
                                $weekday['open_at']
                            )->format("H:i")
                            : null,
                    'close_at' =>
                        $weekdayState === 1 && $weekday['close_at']
                            ? \DateTime::createFromFormat(
                                "H:i",
                                $weekday['close_at']
                            )->format("H:i")
                            : null,
                ]);
            }
        }

        /* Add properties to the doctor */
        $propertyCategories = PropertyCategory::all();
        foreach ($propertyCategories as $category) {
            if (isset($request['category_' . $category->id . '_properties'])) {
                foreach (
                    $request['category_' . $category->id . '_properties']
                    as $property
                ) {
                    $propertyId = $property;
                    if (!is_numeric($property)) {
                        $propertyObj = Property::create([
                            'property_category_id' => $category->id,
                            'name' => $property,
                            'is_approved' => 0,
                            'show_on_registration' => 0,
                            'show_in_search' => 0,
                        ]);
                        $propertyId = $propertyObj->id;
                    }
                    $user->properties()->attach($propertyId);
                }
            }
        }

        /* Add services to the doctor */
        foreach ($request['service_prices'] as $serviceId => $price) {
            if (!is_null($price)) {
                if (!is_numeric($serviceId)) {
                    $serviceObj = Service::create([
                        'name' => $serviceId,
                        'is_approved' => 0,
                        'show_on_registration' => 0,
                        'show_in_search' => 0,
                    ]);
                    $serviceId = $serviceObj->id;
                }
                $user->services()->attach($serviceId, compact('price'));
            }
        }

        $counter = 0;
        if ($request->file('photos')) {
            foreach ($request->file('photos') as $file) {
                if ($file) {
                    $path = Storage::disk('public')->putFile(
                        $galleryPath . $user->id,
                        $file
                    );
                    Photo::create([
                        'path' => $path,
                        'user_id' => $user->id,
                        'position' => $counter,
                    ]);
                    $counter++;
                }
            }
        }

        $doctor->profile_completedness = HelperController::calculateProfileCompletedness(
            $doctor
        );
        $doctor->save();

        /* Create a record in log table */
        DoctorsLog::create([
            'user_id' => $user->id,
            'state_id' => 1,
            'email_sent' => 1,
            'doctor_object' => serialize($user),
        ]);

        return redirect('/')->with('status', 'New doctor was added');
    }

    public function showAll()
    {
        $orderBy = Input::get('ob');
        $orderDirection = Input::get('od') ? Input::get('od') : 'asc';
        $doctors = Doctor::where('state_id', '=', 3);
        if ($orderBy) {
            $doctors
                ->join('users', 'users.id', '=', 'doctors.user_id')
                ->orderBy($orderBy, $orderDirection);
        }
        $doctors = $doctors->paginate(3);
        return view('doctors', compact('doctors'));
    }

    public function show($slug)
    {
        $doctor = Doctor::where('slug', '=', $slug)->first();
        $propertyCategories = PropertyCategory::all();
        $weekdays = Weekday::all();
        $scoreItems = ScoreItem::all();
        return view(
            'doctor',
            compact('doctor', 'propertyCategories', 'weekdays', 'scoreItems')
        );
    }
}
