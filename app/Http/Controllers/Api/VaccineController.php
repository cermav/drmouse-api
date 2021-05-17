<?php

namespace App\Http\Controllers\Api;

use DateTime;
use App\Models\Pet;
use App\Models\Vaccine;
use App\Models\PetVaccine;
use App\Models\PetAppointment;
use App\Models\Member;
use App\Models\ScoreItem;
use App\Models\DoctorsLog;
use App\Models\User;
use App\Models\Doctor;
use App\Http\Controllers\HelperController;
use App\Types\DoctorStatus;
use App\Types\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VaccineController extends Controller
{
    //GET all as admin
    //TODO Authentication
    //done
    public function showAll()
    {
        $loggedUser = Auth::User();
        if ($loggedUser->role_id === UserRole::ADMINISTRATOR) {
            $vaccines = PetVaccine::all();
            return response()->json($vaccines);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    //GET vaccines from pet ID
    //TODO Authentication
    public function list()
    {
        return response()->json(Vaccine::all());
    }
    public function index($pet_id)
    {
        $this->AuthPet($pet_id);
        $vaccines = Pet::find($pet_id)
            ->vaccine()
            ->leftJoin('doctors', 'doctor_id', '=', 'doctors.user_id')
            ->leftJoin('users', 'doctor_id', '=', 'users.id')
            ->leftJoin('vaccines', 'vaccine_id', '=', 'vaccines.id')
            ->select(
                'pet_vaccines.*',
                'doctors.search_name',
                'doctors.city',
                'doctors.street',
                'users.avatar',
                'vaccines.name',
                'vaccines.company',
            )
            ->get();
        return response()->json($vaccines);
    }

    public function detail(int $user_id, int $pet_id)
    {
        PetController::AuthUser($user_id);
        $vaccines = Pet::find($pet_id)
            ->vaccine()
            ->first();
        return response()->json($vaccines);
    }
    // POST add vaccine by pet_id
    public function store(Request $request, $pet_id)
    {
        // validate input
        //PetController::AuthPet($pet_id);
        Pet::FindOrFail($pet_id);
        $input = $this->validateRegistration($request);
        if (
            DB::table('pets')
                ->where('id', $pet_id)
                ->first()->owners_id === Auth::User()->id &&
            $input->pet_id == $pet_id
        ) {
            $input = $this->validateRegistration($request);
            $vaccine = $this->AddVaccine($input, $pet_id);

            $vaccine->save();

            return response()->json($vaccine, JsonResponse::HTTP_CREATED);
        } else {
            return response()->json("Unauthorized", 401);
        }
    }
    //create vaccine for POST add vaccine
    //TODO Authentication
    //done
    public function AddVaccine(object $data, $pet_id)
    {
        $validator = Validator::make((array) $data, [
            'description' => 'required|string|max:100',
            'apply_date' => 'required',
            'pet_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json(
                    ['errors' => $validator->errors()],
                    JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                )
            );
        }
        $date = DateTime::createFromFormat('j. n. Y', $data->apply_date);
        return PetVaccine::create([
            'description' => $data->description,
            'apply_date' => $date,
            'valid' => $data->valid,
            'pet_id' => $pet_id,
            'doctor_id' => $data->doctor_id,
            'price' => $data->price,
            'vaccine_id' => $data->vaccine_id,
            'notes' => $data->notes,
            'color' => rand(0, 7)
        ]);
    }
    // DEL remove appointment
    public function remove(int $pet_id, int $vac_id)
    {
        try {
            $this->AuthUser(Pet::where('id', $pet_id)->first()->owners_id);
        } catch (\Exception $e) {
            return response()->json("non-existent pet or vaccine", 404);
        }
        DB::table('pet_vaccines')
            ->where('id', $vac_id)
            ->where('pet_id', $pet_id)
            ->delete();
        return response()->json("Deleted", JsonResponse::HTTP_OK);
    }
    // PUT Update appointment
    public function update(Request $request, int $pet_id, int $id)
    {
        $data = json_decode($request->getContent());
        try {
            $this->AuthUser(Pet::where('id', $pet_id)->first()->owners_id);
        } catch (\Exception $e) {
            return response()->json("non-existent pet or vaccine", 404);
        }
        $date = DateTime::createFromFormat('j. n. Y', $data->apply_date);

        PetVaccine::where('id', $id)->where('pet_id',$pet_id)->update([
            'description' => $data->description,
            'vaccine_id' => $data->vaccine_id,
            'apply_date' => $date,
            'valid' => $data->valid,
            'pet_id' => $pet_id,
            'doctor_id' => $data->doctor_id,
            'price' => $data->price,
            'notes' => $data->notes,
        ]);
        return response()->json(JsonResponse::HTTP_OK, 200
        );
    }
    protected function validateRegistration(Request $request)
    {
        // get data from json
        $input = json_decode($request->getContent());
        // prepare validator
        $validator = Validator::make((array) $input, [
            'apply_date' => 'required',
            'description' => 'required',
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
    public static function AuthPet(int $pet_id)
    {
        $owners_id = Pet::where('id', $pet_id)->first()->owners_id;
        $loggedUser = Auth::User();
        if (
            $owners_id === $loggedUser->id ||
            $loggedUser->role_id === UserRole::ADMINISTRATOR
        ) {
            //logged user is authorized
            return;
        } else {
            // return unauthorized
            throw new AuthenticationException();
        }
    }
    public static function setSeen(int $pet_id, int $vaccine_id)
    {
        $owners_id = Pet::where('id', $pet_id)->first()->owners_id;
        $loggedUser = Auth::User();
        if (
            $owners_id === $loggedUser->id ||
            $loggedUser->role_id === UserRole::ADMINISTRATOR
        ) {
            //logged user is authorized
            PetVaccine::where('id', $vaccine_id)->where('pet_id',$pet_id)->update([
                'seen' => true
            ]);
            return response()->json(JsonResponse::HTTP_OK, 200
            );
        } else {
            // return unauthorized
            throw new AuthenticationException();
        }
    }
}
