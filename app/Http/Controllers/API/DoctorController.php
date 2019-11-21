<?php

namespace App\Http\Controllers\Api;

use App\DoctorsLog;
use App\Http\Controllers\HelperController;
use App\Types\UserRole;
use App\Types\UserState;
use App\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Doctor;
use App\Http\Resources\DoctorResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{

    private $pageLimit = 30;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // prepare basic select
        $doctors = DB::table('doctors')
            ->select(
                'users.id',
                'name',
                'slug',
                'street',
                'city',
                'country',
                'post_code',
                'latitude',
                'longitude',
                'avatar',
                // DB::raw("(SELECT GROUP_CONCAT(property_id) FROM doctors_properties WHERE user_id = users.id) AS properties"),
                DB::raw("IFNULL((
                    SELECT true
                    FROM opening_hours
                    WHERE user_id = users.id AND weekday_id = (WEEKDAY(NOW()) + 1)
                      AND (
                        (opening_hours_state_id = 1 AND CAST(NOW() AS time) BETWEEN open_at AND close_at)
                        OR
                        opening_hours_state_id = 3
                      )
                    LIMIT 1)
                  , false) AS open "),
                DB::raw("(SELECT IFNULL( ROUND(((SUM(points)/COUNT(id))/5)*100) , 0) FROM score_details WHERE score_id IN (SELECT id FROM scores WHERE user_id = doctors.user_id)) AS total_score ")
            )
            ->join('users', 'doctors.user_id', '=', 'users.id')
            ->where('doctors.state_id', 1);

        // add fulltext condition
        if ($request->has('fulltext') && strlen(trim($request->input('fulltext'))) > 2) {
            $doctors->whereRaw(
                "MATCH (search_name, description, street, city, country) AGAINST (? IN NATURAL LANGUAGE MODE)",
                trim($request->input('fulltext'))
            );
            $doctors->orwhereRaw(
                "MATCH (email) AGAINST (? IN NATURAL LANGUAGE MODE)",
                trim($request->input('fulltext'))
            );
        }

        // add specialization condition
        if ($request->has('spec') && intval($request->input('spec')) > 0) {
            $doctors->whereExists(function ($query) use ($request) {
                $query->select(DB::raw(1))
                    ->from('doctors_properties')
                    ->where([
                        ['doctors_properties.user_id', '=', 'users.id'],
                        ['doctors_properties.property_id', '=', intval($request->input('spec'))]
                    ]);
            });
        }

        // add experience condition
        if ($request->has('exp') && intval($request->input('exp')) > 0) {
            $doctors->whereExists(function ($query) use ($request) {
                $query->select(DB::raw(1))
                    ->from('doctors_properties')
                    ->where([
                        ['doctors_properties.user_id', '=', 'users.id'],
                        ['doctors_properties.property_id', '=', intval($request->input('exp'))]
                    ]);
            });
        }

        // sorting
        $order_fields = ['total_score'];
        if ($request->has('order') && in_array(trim($request->input('order')), $order_fields)) {
            $direction = $request->has('dir') && strtolower(trim($request->input('dir') == 'desc')) ? 'desc' : 'asc';
            $doctors->orderBy(trim($request->input('order')), $direction);
        }


        return $doctors->paginate($this->pageLimit);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validate input
        $input = $this->validateRegistration($request);

        // get location
        $location = $this->getDoctorLocation($input);

        // Create user
        $user = $this->createUser($input);

        // Create doctor
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'state_id' => UserState::NEW,
            'description' => "",
            'search_name' => HelperController::parseName($input->name),
            'slug' => $this->getDoctorSlug($input->name),
            'street' => $input->street,
            'post_code' => str_replace(' ', '', $input->post_code),
            'city' => $input->city,
            'latitude' => $location['latitude'],
            'longitude' => $location['longitude'],
            'phone' => $input->phone,
            'gdpr_agreed' => true,
            'gdpr_agreed_date' => date('Y-m-d H:i:s')
        ]);

        // TODO: predelat
        if (!empty($input->profile_image)) {
            $base64File = $input->profile_image;
            $encodedImgString = explode(',', $base64File, 2)[1];
            $decodedImgString = base64_decode($encodedImgString);
            $info = getimagesizefromstring($decodedImgString);
            $ext = explode('/', $info['mime']);
            @list($type, $file_data) = explode(';', $base64File);
            @list(, $file_data) = explode(',', $file_data);
            $imageName = 'profile_' . time() . '.' . $ext[1];
            $imagePath = 'users/profiles/' . $user->id . '/' . $imageName;
            Storage::disk('public')->put($imagePath, base64_decode($file_data));
            $user->avatar = $imagePath;
            $user->save();
        }

        $doctor->profile_completedness = HelperController::calculateProfileCompletedness($doctor);
        $doctor->save();

        // send registration email
        $this->sendRegistrationEmail($doctor, $user);

        /* Create a record in log table */
        DoctorsLog::create([
            'user_id' => $user->id,
            'state_id' => UserState::NEW,
            'email_sent' => true,
            'doctor_object' => serialize($doctor)
        ]);

        return response()->json($doctor, JsonResponse::HTTP_CREATED);
    }

    /**
     * Validate Input
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateRegistration(Request $request)
    {
        // get data from json
        $input = json_decode($request->getContent());
        // prepare validator
        $validator = Validator::make((array) $input, [
            'name' => 'required|max:255',
            'email' => 'unique:users|required|email',
            'password' => 'required|min:6',
            'street' => 'required|max:255',
            'post_code' => 'required|max:6',
            'city' => 'required|max:255',
            'phone' => 'required|max:20',
            'gdpr' => 'required'
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }

        return $input;
    }

    /**
     * Create slug - if already exists, add the number at the end
     * @param string $name
     * @return string
     */
    protected function getDoctorSlug(string $name)
    {
        $slug = strtolower(str_replace(" ", "-", preg_replace("/[^A-Za-z0-9 ]/", '', HelperController::replaceAccents($name))));
        $existingCount = Doctor::where('slug', 'like', $slug . '%')->count();
        if ($existingCount > 0) {
            $slug = $slug . '-' . ($existingCount);
        }
        return $slug;
    }

    /**
     * Get longitude and latitude by the address
     * @param array $data
     * @return array
     */
    protected function getDoctorLocation(object $data)
    {
        return HelperController::getLatLngFromAddress(
            trim($data->street) . " " . trim($data->city) . " CZ " . trim($data->post_code)
        );
    }

    /**
     * Create user
     * @param array $data
     * @return User
     */
    protected function createUser(object $data)
    {
        try{
            return User::create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => Hash::make(trim($data->password)),
                'role_id' => UserRole::DOCTOR
            ]);
        } catch (\Exception $ex) {
            throw new HttpResponseException(
                response()->json(
                    ['errors' => "Error creating user: " . $ex->getMessage()],
                    JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                )
            );
        }
    }

    protected function saveProfileImage()
    {

    }

    protected function sendRegistrationEmail(Doctor $doctor, User $user)
    {
        $email = $user->email;
        $data = [
            'doctor' => $doctor,
            'user' => $user
        ];
        Mail::send('emails/registration', $data, function ($message) use ($email) {
            $message->to($email)
                ->subject('Dr.Mouse ověření emailu');
        });
    }




    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $doctor = Doctor::where(['user_id' => $id, 'state_id' => 1])->get();
        if (sizeof($doctor) > 0) {
            return DoctorResource::collection($doctor)->first();
        }
        return response()->json(['message' => 'Not Found!'], 404);
    }

    public function showAll()
    {
        // prepare basic select
        $doctors = DB::table('doctors')
            ->select(
                'users.id',
                'name',
                'slug',
                'street',
                'city',
                'post_code',
                'latitude',
                'longitude',
                DB::raw("IFNULL((
                    SELECT true
                    FROM opening_hours
                    WHERE user_id = users.id AND weekday_id = (WEEKDAY(NOW()) + 1)
                      AND (
                        (opening_hours_state_id = 1 AND CAST(NOW() AS time) BETWEEN open_at AND close_at)
                        OR
                        opening_hours_state_id = 3
                      )
                    LIMIT 1)
                  , false) AS open ")
            )
            ->join('users', 'doctors.user_id', '=', 'users.id')
            ->where('doctors.state_id', 1);

        /*
         DB::raw("(
                    SELECT 1
                    FROM opening_hours
                    WHERE user_id = users.id AND weekday_id = (WEEKDAY(NOW()) + 1)
                      AND (
                        (opening_hours_state_id = 1 AND CAST(NOW() AS time) BETWEEN open_at AND close_at)
                        OR
                        opening_hours_state_id = 3
                      )
                  ) AS open ")
         */

        return $doctors->get();
    }

    /**
     * Display doctor by slug
     * @param $slug
     */
    public function showBySlug($slug)
    {
        $doctor = Doctor::where('slug', $slug)->get();
        if (sizeof($doctor) > 0) {
            return DoctorResource::collection($doctor)->first();
        }
        return response()->json(['message' => 'Not Found!'], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return response()->json(null, 501);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json(null, 501);
    }
}
