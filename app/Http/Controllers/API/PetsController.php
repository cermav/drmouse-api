<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use JWTAuth;
use App\Pets;
use App\Models\Member;
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

class PetsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //GET pets list
    public function showAll()
    {
        $Pets = Pets::all();
        return response()->json($Pets);
    }
    public function index()
    {
        $loggedUser = Auth::user()->id;
        $pets = DB::select("SELECT * FROM pets WHERE owners_id = $loggedUser");
        return response()->json($pets);
    }
    //GET pets detail
    public function showById($id)
    {
        // get owners_id
        $owners_id = DB::table('pets')
            ->where('id', "$id")
            ->first()->owners_id;
        //authorize owners_id vs logged in user
        $this->AuthUser($owners_id);

        $pet = Pets::find($id);

        return response()->json($pet);
    }

    //create pet for POST pet
    public function createpet(object $data)
    {
        return Pets::create([
            'owners_id' => $data->owners_id,
            'pet_name' => $data->pet_name,
            'birth_date' => $data->birth_date,
            'kind' => $data->kind,
            'breed' => $data->breed,
            'gender' => $data->gender,
            'chip_number' => $data->chip_number,
            'bg' => $data->bg,
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
        $input = $this->validateRegistration($request);

        $pet = $this->createpet($input);

        $pet->save();

        return response()->json($pet, JsonResponse::HTTP_CREATED);
    }
    // DEL remove pet
    //TODO Authentication
    public function remove(request $request, int $id)
    {
        $this->AuthPet($id);

        $pet = Pets::findOrFail($id);
        $pet = DB::delete("DELETE FROM pets WHERE id = $id");
        return response()->json("Deleted", JsonResponse::HTTP_OK);
    }
    // PUT Update pet
    public function update(Request $request, int $id)
    {
        $this->AuthUser();
        // get data from json
        $input = json_decode($request->getContent());
        // prepare validator
        $validator = Validator::make((array) $input, [
            'pet_name' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'kind' => 'nullable|string|max:50',
            'breed' => 'nullable|string|max:50',
            'gender' => 'nullable|string|max:50',
            'chip_number' => 'nullable|int',
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json(
                    ['errors' => $validator->errors()],
                    JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                )
            );
        }
        //$this->validateAddress($input->address);
        //$this->validateStaffInfo($input->staff_info);
        $data = [
            'pet' => [
                'pet_name' => $input->pet_name,
                'birth_date' => $input->birth_date,
                'kind' => $input->kind,
                'breed' => $input->breed,
                'gender' => $input->gender,
                'chip_number' => $input->chip_number,
                'bg' => $input->bg,
                'avatar' => $input->avatar,
            ],
        ];

        return $data;
    }
    protected function validateRegistration(Request $request)
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
            'gender' => 'required',
            'chip_number' => 'required',
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
    public function AuthPet(int $pet_id)
    {
        $requestUser = Pets::Find($pet_id);
        $loggedUser = Auth::User();

        if (
            $requestUser->owners_id === $loggedUser->id ||
            $loggedUser->role_id === UserRole::ADMINISTRATOR
        ) {
            //logged user is authorized
            return;
        } else {
            // return unauthorized
            throw new AuthenticationException();
        }
    }
    public function AuthUser(int $id)
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
            throw new AuthenticationException();
        }
    }
}
