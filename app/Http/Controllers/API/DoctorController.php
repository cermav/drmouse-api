<?php

namespace App\Http\Controllers\Api;

use App\DoctorsLog;
use App\Http\Controllers\HelperController;
use App\ScoreItem;
use App\Types\DoctorStatus;
use App\Types\UserRole;
use App\Types\UserState;
use App\User;
use App\Utils\ImageHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Doctor;
use App\Http\Resources\DoctorResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;

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
            ->selectRaw(
                "(SELECT ST_Distance_Sphere(point(?, ?), point(longitude, latitude)) ) AS distance",
                [
                    $request->has('long') ? floatval($request->input('long')) : 15.7,
                    $request->has('lat') ? floatval($request->input('lat')) : 49.8
                ]
            )

            ->join('users', 'doctors.user_id', '=', 'users.id')
            ->where('doctors.state_id', DoctorStatus::PUBLISHED);


        // add fulltext condition
        if ($request->has('fulltext') && strlen(trim($request->input('fulltext'))) > 2) {
            // split words and add wildcard
            $search_text = '*' . implode('* *', explode(' ', urldecode(trim($request->input('fulltext'))))) . '*';
            $doctors->selectRaw(
                "(
                    MATCH (search_name, description, street, city, country, working_doctors_names) AGAINST (? IN BOOLEAN MODE) +
                    MATCH (email) AGAINST (? IN BOOLEAN MODE)
                ) AS relevance",
                [$search_text, $search_text]
            );
            $doctors->whereRaw("(
                    MATCH (search_name, description, street, city, country, working_doctors_names) AGAINST (? IN BOOLEAN MODE)
                    OR MATCH (email) AGAINST (? IN BOOLEAN MODE)
                )", [$search_text, $search_text]);
        } else {
            $doctors->selectRaw('0 AS relevance');
        }

        // add specialization condition - condition has to be RAW, otherwhise not working
        if ($request->has('spec') && intval($request->input('spec')) > 0) {
            $doctors->whereExists(function ($query) use ($request) {
                $query->select(DB::raw(1))
                    ->from('doctors_properties')
                    ->whereRaw('doctors_properties.user_id = users.id AND doctors_properties.property_id = ?', intval($request->input('spec')));
            });
        }

        // add experience condition - condition has to be RAW, otherwhise not working
        if ($request->has('exp') && intval($request->input('exp')) > 0) {
            $doctors->whereExists(function ($query) use ($request) {
                $query->select(DB::raw(1))
                    ->from('doctors_properties')
                    ->whereRaw('doctors_properties.user_id = users.id AND doctors_properties.property_id = ?', intval($request->input('exp')));
            });
        }

        // sorting
        $order_fields = ['rank' => 'total_score' , 'dist' => 'distance', 'rel' => 'relevance'];
        if ($request->has('order') && array_key_exists(trim($request->input('order')), $order_fields)) {
            $direction = $request->has('dir') && strtolower(trim($request->input('dir') == 'desc')) ? 'desc' : 'asc';
            // some exception
            if (in_array($request->input('order'), ['rank', 'rel'])) {
                $direction = 'desc';
            }
            $doctors->orderBy($order_fields[$request->input('order')], $direction);
        }

        $doctors->paginate($this->pageLimit);

        return $doctors->paginate($this->pageLimit);
    }

    /**
 * Return all doctors, for homepage
 * @return \Illuminate\Support\Collection
 */
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
            ->where('doctors.state_id', DoctorStatus::PUBLISHED);

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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // build score query
        $scoreQuery = [];
        foreach (ScoreItem::get() as $item) {
            $scoreQuery[] = "(
                SELECT IFNULL( ROUND(((SUM(points) / COUNT(id)) / 5) * 100) , 0) 
                FROM score_details 
                WHERE score_id IN (SELECT id FROM scores WHERE user_id = doctors.user_id)
                    AND score_item_id = {$item->id}
            ) AS total_score_{$item->id} ";
        }

        $doctor = Doctor::where('user_id', $id)
            ->select(
                'doctors.*',
                DB::raw(implode(", ", $scoreQuery)),
                DB::raw("IFNULL((
                    SELECT true
                    FROM opening_hours
                    WHERE user_id = doctors.user_id AND weekday_id = (WEEKDAY(NOW()) + 1)
                      AND (
                        (opening_hours_state_id = 1 AND CAST(NOW() AS time) BETWEEN open_at AND close_at)
                        OR
                        opening_hours_state_id = 3
                      )
                    LIMIT 1)
                  , false) AS open ")
            )
            ->whereIn('state_id', [DoctorStatus::NEW, DoctorStatus::PUBLISHED])->get();
        if (sizeof($doctor) > 0) {
            return DoctorResource::collection($doctor)->first();
        }
        return response()->json(['message' => 'Not Found!'], JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * Display doctor by slug
     * @param $slug
     */
    public function showBySlug($slug)
    {
        // build score query
        $scoreQuery = [];
        foreach (ScoreItem::get() as $item) {
            $scoreQuery[] = "(
                SELECT IFNULL( ROUND(((SUM(points) / COUNT(id)) / 5) * 100) , 0) 
                FROM score_details 
                WHERE score_id IN (SELECT id FROM scores WHERE user_id = doctors.user_id)
                    AND score_item_id = {$item->id}
            ) AS total_score_{$item->id} ";
        }

        $doctor = Doctor::where('slug', $slug)
            ->select(
                'doctors.*',
                DB::raw(implode(", ", $scoreQuery)),
                DB::raw("IFNULL((
                    SELECT true
                    FROM opening_hours
                    WHERE user_id = doctors.user_id AND weekday_id = (WEEKDAY(NOW()) + 1)
                      AND (
                        (opening_hours_state_id = 1 AND CAST(NOW() AS time) BETWEEN open_at AND close_at)
                        OR
                        opening_hours_state_id = 3
                      )
                    LIMIT 1)
                  , false) AS open ")
            )
            ->whereIn('state_id', [DoctorStatus::PUBLISHED])
            ->get();
        if (sizeof($doctor) > 0) {
            return DoctorResource::collection($doctor)->first();
        }
        return response()->json(['message' => 'Not Found!'], JsonResponse::HTTP_NOT_FOUND);
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

        try {
            // get location
            $location = $this->getDoctorLocation($input);
        } catch (\Exception $ex) {
            return response()->json(['error' => ['location' => $ex->getMessage()]], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

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
        $user->sendEmailVerificationNotification();

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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        // verify user
        $requestUser = User::find($id);
        $loggedUser = Auth::User();

        if ($requestUser->id === $loggedUser->id || $loggedUser->role_id === UserRole::ADMINISTRATOR) {

            // validate input
            $input = $this->validateProfile($request, $id);

            // TODO : add validation
            $user = User::find($id);
            $user->update($input['user']);

            $doctor = Doctor::where(['user_id' => $id])->get()->first();
            // add search name
            $input['doctor']['search_name'] = HelperController::parseName($input['user']['name']);
            $input['doctor']['profile_completedness'] = HelperController::calculateProfileCompletedness($doctor);
            $doctor->update($input['doctor']);

            // store image
            if ($input['avatar'] !== null) {
                $user->update(['avatar' => $this->saveProfileImage($id, $input['avatar'])]);
            }

            return response()->json(DoctorResource::make($doctor), JsonResponse::HTTP_OK);

        } else {
            // return unauthorized
            throw new AuthenticationException();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, int $id)
    {
        // verify user
        $requestUser = User::find($id);
        $loggedUser = Auth::User();

        if ($requestUser->id === $loggedUser->id || $loggedUser->role_id === UserRole::ADMINISTRATOR) {

            $doctor = Doctor::where(['user_id' => $id])->get()->first();
            $doctor->update(['state_id' => DoctorStatus::DELETED]);

            return response()->json(DoctorResource::make($doctor), JsonResponse::HTTP_OK);

        } else {
            // return unauthorized
            throw new AuthenticationException();
        }
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
            'email' => 'required|email|unique:users',
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
     * Validate Input
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateProfile(Request $request, int $user_id)
    {
        // get data from json
        $input = json_decode($request->getContent());
        // prepare validator
        $validator = Validator::make((array) $input, [
            'name' => 'required|max:255',
            'slug' => 'max:191|unique:doctors,slug,'.$user_id.',user_id',
            'email' => 'required|email|unique:users,email,'.$user_id.',id',
            'description' => 'string',
            'speaks_english' => 'required'
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }

        $this->validateAddress($input->address);
        $this->validateStaffInfo($input->staff_info);

        $data = [
            'user' => [
                'name' => $input->name,
                'email' => $input->email
            ],
            'doctor' => [
                'description' => $input->description,
                'speaks_english' => $input->speaks_english,
                // address detail
                'phone' => $input->address->phone,
                'second_phone' => $input->address->second_phone,
                'website' => $input->address->website,
                'street' => $input->address->street,
                'city' => $input->address->city,
                'post_code' => $input->address->post_code,
                // staff info
                'working_doctors_count' => $input->staff_info->doctors_count,
                'working_doctors_names' => $input->staff_info->doctors_names,
                'nurses_count' => $input->staff_info->nurses_count,
                'other_workers_count' => $input->staff_info->others_count
            ],
            'avatar' => $input->avatar
        ];
        if (!empty($input->slug)) {
            $data['doctor']['slug'] = $input->slug;
        }
        return $data;
    }

    /**
     * Validate Input
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateAddress(object $address)
    {
        // prepare validator
        $validator = Validator::make((array) $address, [
            'street' => 'required|max:255',
            'post_code' => 'required|max:6',
            'city' => 'required|max:255',
            'phone' => 'required|max:20',
            'second_phone' => 'string|max:20',
            'website' => 'string|max:150'
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }
    }

    /**
     * Validate Input
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateStaffInfo(object $staffInfo)
    {
        // prepare validator
        $validator = Validator::make((array) $staffInfo, [
            'doctors_count' => 'required|integer',
            'nurses_count' => 'required|integer',
            'others_count' => 'required|integer',
            'doctors_names' => 'string|max:1000'
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }
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
                'role_id' => DoctorStatus::DOCTOR
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

    protected function saveProfileImage($user_id, $data)
    {
        // get doctor info
        $doctor = Doctor::where('user_id', $user_id)->first();

        // split image data
        $image = ImageHandler::splitEncodedData($data);

        // prepare file name
        $fileName = strtolower($doctor->slug . '.' . ImageHandler::getExtensionByType($image->type));

        // save file to local storage
        Storage::disk('public')->put('profile' . DIRECTORY_SEPARATOR . $fileName, base64_decode($image->content));

        return $fileName;
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

}
