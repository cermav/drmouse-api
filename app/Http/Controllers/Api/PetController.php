<?php

namespace app\Http\Controllers\API;

use Illuminate\Http\Request;
use JWTAuth;
use App\Models\Pet;
use App\Models\User;
use App\Models\DoctorsLog;
use App\Models\ScoreItem;
use App\Models\Doctor;
use App\Models\FavoriteVet;
use App\Models\Record;
use App\Models\RecordFile;
use App\Http\Controllers\HelperController;
use App\Types\DoctorStatus;
use App\Types\UserRole;
use App\Types\UserState;
use App\Utils\ImageHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\DoctorController;
use App\Http\Resources\DoctorResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use App\Http\Resources\OpeningHoursResource;
use DateTime;
use Illuminate\Http\File;

class PetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //GET all Pets as administrator

    public function showAll()
    {
        $loggedUser = Auth::User();
        if ($loggedUser->role_id === UserRole::ADMINISTRATOR) {
            $Pets = Pet::all();
            return response()->json($Pets);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    //GET Pets list
    public function index()
    {
        $pets = DB::table('pets')
            ->where('owners_id', Auth::user()->id)
            ->get();
        return response()->json($pets);
    }
    //GET Pets detail
    public function detail($id)
    {
        // get pet by id
        $pet = DB::table('pets')->where('id', $id);
        try {
            $this->AuthUser($pet->first()->owners_id);
            DB::table('users')
                ->where('id', $pet->first()->owners_id)
                ->update(['last_pet' => $id]);

            return response()->json($pet->first());
        } catch (\Exception $ex) {
            return response()->json(
                ['error' => ['location' => $ex->getMessage()]],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        //set new latest pet on visit
        //authorize owners_id vs logged in user
    }
    public function latest()
    {
        $last_pet = User::where('id', Auth::user()->id)->first()->last_pet;
        if ($last_pet === 0) {
            return response()->json($last_pet);
        }
        try {
            $list = Pet::where('owners_id', Auth::user()->id)
                ->pluck('id')
                ->toArray();
            $temp = $list[0];
            foreach ($list as $id) {
                if ($last_pet == $id) {
                    return response()->json($last_pet);
                } elseif ($id > $temp) {
                    $temp = $id;
                }
            }
            return response()->json($temp);
        } catch (\Exception $ex) {
            User::where('id', Auth::User()->id)->update([
                'last_pet' => 0,
            ]);
            return response()->json(
                ['error' => ["No existing pet found"]],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }
    //create pet for POST pet
    public function createpet(object $data)
    {
        $date = DateTime::createFromFormat('j. n. Y', $data->birth_date);
        return Pet::create([
            'owners_id' => Auth::user()->id,
            'pet_name' => $data->pet_name,
            'birth_date' => $date,
            'kind' => $data->kind,
            'breed' => $data->breed,
            'gender_state_id' => $data->gender_state_id,
            'chip_number' => $data->chip_number,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // POST pet

    public function store(Request $request)
    {
        // validate input
        $input = $this->validateNewPet($request);

        $pet = $this->createpet($input);

        $pet->save();

        //get created pet id
        $ids = DB::table('pets')
            ->where('owners_id', Auth::user()->id)
            ->pluck('id')
            ->toArray();
        $temp = $ids[0];
        foreach ($ids as $id) {
            if ($id > $temp) {
                $temp = $id;
            }
        }
        //set new pet as latest
        DB::table('users')
            ->where('id', Auth::user()->id)
            ->update(['last_pet' => $temp]);

        return response()->json($temp, JsonResponse::HTTP_CREATED);
    }
    // DEL remove pet
    public function remove(int $id)
    {
        $this->AuthPet($id);
        $user_id = Auth::user()->id;
        Pet::where('id', $id)
            ->where('owners_id', $user_id)
            ->delete();
        try {
            $ids = DB::table('pets')
                ->where('owners_id', Auth::user()->id)
                ->pluck('id')
                ->toArray();
            $temp = $ids[0];
            foreach ($ids as $id) {
                if ($id > $temp) {
                    $temp = $id;
                }
            }
            User::where('id', $user_id)->update([
                'last_pet' => $temp,
            ]);
            return response()->json($temp, JsonResponse::HTTP_OK);
        } catch (\Exception $ex) {
            User::where('id', Auth::User())->update([
                'last_pet' => 0,
            ]);
            return response()->json(
                ['error' => ['location' => $ex->getMessage()]],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }
    // PUT Update pet

    public function update(Request $request, int $id)
    {
        $this->AuthPet($id);
        // get data from json
        $input = json_decode($request->getContent());
        // prepare validator
        $validator = Validator::make((array) $input, [
            'pet_name' => 'required|string|max:50',
            'birth_date' => 'required',
            'kind' => 'required|string|max:50',
            'breed' => 'required|string|max:50',
            'gender_state_id' => 'required|int',
            'chip_number' => 'nullable|string|max:50',
        ]);
        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json(
                    ['errors' => $validator->errors()],
                    JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                )
            );
        }
        $date = DateTime::createFromFormat('j. n. Y', $input->birth_date);
        Pet::where('id', $id)->update([
            'pet_name' => $input->pet_name,
            'birth_date' => $date,
            'kind' => $input->kind,
            'breed' => $input->breed,
            'gender_state_id' => $input->gender_state_id,
            'chip_number' => $input->chip_number,
        ]);

        return response()->json(Pet::find($id), JsonResponse::HTTP_OK);
    }
    protected function saveAvatar($pet_id, $data)
    {
        // get pet info
        $pet = Pet::where('id', $pet_id)->first();

        // split image data
        $image = ImageHandler::splitEncodedData($data);
        // prepare file name
        $fileName = strtolower(
            $pet->owners_id .
                '_' .
                $pet->id .
                'av.' .
                ImageHandler::getExtensionByType($image->type)
        );
        //remove previous file
        if (Storage::disk('public')->exists($fileName)) {
            Storage::disk('public')->delete($fileName);
        }
        // save file to local storage
        Storage::disk('public')->put(
            'pet_avatar' . DIRECTORY_SEPARATOR . $fileName,
            base64_decode($image->content)
        );
        return $fileName;
    }
    protected function saveBackground($pet_id, $data)
    {
        // get doctor info
        $pet = Pet::where('id', $pet_id)->first();

        // split image data
        $image = ImageHandler::splitEncodedData($data);

        // prepare file name
        $fileName = strtolower(
            $pet->owners_id .
                '_' .
                $pet->id .
                'background.' .
                ImageHandler::getExtensionByType($image->type)
        );
        //remove previous file
        if (Storage::disk('public')->exists($fileName)) {
            Storage::disk('public')->delete($fileName);
        }
        // save file to local storage
        Storage::disk('public')->put(
            'pet_background' . DIRECTORY_SEPARATOR . $fileName,
            base64_decode($image->content)
        );
        return $fileName;
    }
    protected function validateNewPet(Request $request)
    {
        // get data from json
        $input = json_decode($request->getContent());
        // prepare validator

        $validator = Validator::make((array) $input, [
            //'owners_id' => 'required',
            'pet_name' => 'required',
            'birth_date' => 'required',
            'kind' => 'required',
            'breed' => 'required',
            'gender_state_id' => 'required',
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json(
                    ['errors' => $validator->errors()],
                    JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                )
            );
        }

        return $input;
    }
    public static function AuthPet(int $pet_id)
    {
        $owners_id = Pet::where('id', $pet_id)->first()->owners_id;
        $loggedUser = Auth::User();
        if (
            $owners_id === $loggedUser->id ||
            $loggedUser->role_id === UserRole::ADMINISTRATOR
        ) {
            //logged user is authorized
            return $owners_id;
        } else {
            // return unauthorized
            throw new AuthenticationException();
        }
    }
    public static function AuthUser(int $id)
    {
        $requestUser = User::Find($id);
        $loggedUser = Auth::User();

        if (
            $requestUser->id === $loggedUser->id ||
            $loggedUser->role_id === UserRole::ADMINISTRATOR
        ) {
            //logged user is authorized
            return;
        } else {
            // return unauthorized

            throw new HttpResponseException(
                response()->json(401, JsonResponse::HTTP_UNAUTHORIZED)
            );
        }
    }
    public function avatar(Request $request, int $pet_id)
    {
        $this->AuthPet($pet_id);
        $input = json_decode($request->getContent());
        if ($input->avatar !== null) {
            Pet::where('id', $pet_id)->update([
                'avatar' => $this->saveAvatar($pet_id, $input->avatar),
            ]);
        }
        return response()->json(
            Pet::where('id', $pet_id)->first()->avatar,
            JsonResponse::HTTP_OK
        );
    }
    public function background(Request $request, int $pet_id)
    {
        $this->AuthPet($pet_id);
        $input = json_decode($request->getContent());
        if ($input->background !== null) {
            Pet::where('id', $pet_id)->update([
                'background' => $this->saveBackground(
                    $pet_id,
                    $input->background
                ),
            ]);
        }
        return response()->json(
            Pet::where('id', $pet_id)->first()->background,
            JsonResponse::HTTP_OK
        );
    }
    public function user_has_doctors(int $user_id)
    {
        $loggedUser = Auth::User();
        if (
            $loggedUser->id === $user_id ||
            $loggedUser->role_id === UserRole::ADMINISTRATOR
        ) {
            $vets = FavoriteVet::where('user_id', $user_id)
                ->pluck('doctor_id')
                ->toArray();
            $request = new \Illuminate\Http\Request();
            $request->replace(['' => '']);
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
                    //OpeningHoursResource::collection($this->user->openingHours),
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
                    DB::raw(
                        "(SELECT IFNULL( ROUND(((SUM(points)/COUNT(id))/5)*100) , 0) FROM score_details WHERE score_id IN (SELECT id FROM scores WHERE user_id = doctors.user_id)) AS total_score "
                    )
                )
                ->selectRaw(
                    "(SELECT ST_Distance_Sphere(point(?, ?), point(longitude, latitude)) ) AS distance",
                    [
                        $request->has('long')
                            ? floatval($request->input('long'))
                            : 15.7,
                        $request->has('lat')
                            ? floatval($request->input('lat'))
                            : 49.8,
                    ]
                )
                ->join('users', 'doctors.user_id', '=', 'users.id')
                ->whereIn('doctors.state_id', [
                    DoctorStatus::PUBLISHED,
                    DoctorStatus::ACTIVE,
                ])
                ->wherein('users.id', $vets)
                ->get();
            return response()->json($doctors);
        }
    }
    public function add_favorite_doctor(int $user_id, int $doctor_id)
    {
        $loggedUser = Auth::User();
        if (
            $loggedUser->id === $user_id ||
            $loggedUser->role_id === UserRole::ADMINISTRATOR
        ) {
            $exists = DB::table('user_favorite_doctors')
                ->where('user_id', $user_id)
                ->where('doctor_id', $doctor_id)
                ->first();
            if ($exists) {
                return response()->json(
                    ['error' => 'This relation already exists.'],
                    409
                );
            } else {
                FavoriteVet::create([
                    'user_id' => $user_id,
                    'doctor_id' => $doctor_id,
                ]);
                return response()->json(
                    'favorite vet created.',
                    JsonResponse::HTTP_CREATED
                );
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function remove_favorite_doctor(int $user_id, int $doctor_id)
    {
        $loggedUser = Auth::User();
        if (
            $loggedUser->id === $user_id ||
            $loggedUser->role_id === UserRole::ADMINISTRATOR
        ) {
            DB::table('user_favorite_doctors')
                ->where('user_id', $user_id)
                ->where('doctor_id', $doctor_id)
                ->delete();
            return response()->json("Deleted", JsonResponse::HTTP_OK);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    public function get_records($pet_id)
    {
        $owners_id = $this->AuthPet($pet_id);
        try {
        $records = Record::where('pet_id', $pet_id)->get();
        $collection = collect([]);
        foreach ($records as $record){
            $files = RecordFile::where('record_id', $record->id)->get();
            $fileData = collect([]);
        foreach($files as $file)
        {
             $fileCollection = collect([
                'id' => $file->id,
                'file_name' => $file->file_name,
            ]);
            $fileData->push($fileCollection);
        }
            $recordCollection = collect([
                'id' => $record->id,
                'date' => $record->date,
                'description' => $record->description,
                'notes' => $record->notes,
                'doctor_id' => $record->doctor_id,
                'pet_id' => $record->pet_id,
                'files' => $fileData
            ]);
            $collection->push($recordCollection);
        }
        return $collection;
    }
        catch(\HttpResponseException $ex) {
            return response()->json(
                ['error' => $ex]
            );
        }
    }
    public function add_record(int $pet_id, Request $request)
    {
        $owners_id = $this->AuthPet($pet_id);
        try {
        $this->AuthPet($pet_id);
        $date = DateTime::createFromFormat('j. n. Y', $request->date);
            return Record::create([
                'pet_id' => $pet_id,
                'date' => $date,
                'description' => $request->description,
                'notes' => $request->notes,
                'doctor_id' => $request->doctor_id
            ]);
        }
        catch(\HttpResponseException $ex) {
            return response()->json(
                ['error' => $ex]
            );
        }
    }
    public function update_record($pet_id, $record_id, Request $request)
    {
        try {
        $this->AuthPet($pet_id);
        Record::where('id', $record_id)->update([
            'description' => $request->description,
            'notes' => $request->notes,
            'date' => $request->date,
            'doctor_id' => $request->doctor_id
        ]);
        return response()->json(Record::where('id', $record_id)->first(), JsonResponse::HTTP_OK);
        }
        catch(\HttpResponseException $ex) {
            return response()->json(
                ['error' => $ex]
            );
        }
    }  
    public function remove_record($pet_id, $record_id)
    {
        try {
            $files = RecordFile::where('record_id', $record_id)->get();
            foreach ($files as $file){
                $this->remove_file($pet_id, $record_id, $file->id);
            }
            Record::where('id', $record_id)->delete();
            return response()->json("Record and its files deleted successfully", JsonResponse::HTTP_OK);
        }
        catch(\HttpResponseException $ex) {
            return response()->json(
                ['error' => $ex]
            );
        }
    }

    public function get_files(int $pet_id, int $record_id)
    {
        $owner_id = $this->AuthPet($pet_id);

        $files = RecordFile::where('record_id', $record_id)->get();
        $collection = collect([]);
        foreach($files as $file)
        {
             $fileCollection = collect([
                'id' => $file->id,
                'file_name' => $file->file_name,
            ]);
            $collection->push($fileCollection);
        }
        return $collection;
    }

    public function add_files($pet_id, $record_id, Request $request)
    {
        $owner_id = $this->AuthPet($pet_id);
        try {
            Record::findOrFail($record_id);
         }
         catch(\HttpResponseException $ex) {
            return response()->json(
                ['error' => $ex]
            );
        }
        try {
        for($i = 0; $request->hasFile('file' . $i); $i++)
        {
            $file = $request->file('file' . $i);
            $size = $file->getSize();
                if ($size > 10000000) throw new HttpResponseException(
                    response()->json(
                        ['errors' => "Uploaded file exceeds maximum size of 10Mb"], 422
                    )
                );
                $validator = Validator::make((array) $request->getContent(), [
                    'file'.$i => 'file|mimetypes: pdf, docx, jpg, png'
                    ]);
                if ($validator->fails())
                    {
                        throw new HttpResponseException(
                            response()->json(
                                ['errors' => $validator->errors()],
                                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                            )
                        );
                    }
                else {
                        $storage_path = "pet_records/" . $owner_id ;
                        $path = $file->store($storage_path);
                        $path && RecordFile::create([
                            'record_id' => $record_id,
                            'file_name' => $file->getClientOriginalName(),
                            'path' => $path, // prejmenovat
                            'owner_id' => $owner_id
                        ]);
                    }

                }
                return response()->json(['status' => 200, 'message' => 'uploaded successfully!']);
            }
        catch(\HttpResponseException $ex) {
            return response()->json(
                ['error' => $ex]
            );
        }
    }

    public function remove_file($pet_id, $record_id, $file_id)
    {
        $owner_id = $this->AuthPet($pet_id);
        
        try {
            $path = RecordFile::where('record_id', $record_id)->where('id',$file_id)->first()->path;
            Storage::delete($path);
            RecordFile::where('record_id', $record_id)->where('id', $file_id)->delete();
            return response()->json("File deleted successfully", JsonResponse::HTTP_OK);
        }
        catch(\HttpResponseException $ex) {
            return response()->json(
                ['error' => $ex]
            );
        }
    }

}
