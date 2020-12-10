<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\DoctorsLog;
use App\Models\Member;
use App\Models\Pet;
use App\Models\PetAppointment;
use App\Models\ScoreItem;
use App\Models\User;
use App\Http\Controllers\HelperController;
use App\Types\DoctorStatus;
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
use Larapack\DoctrineSupport\Connections\MySqlConnection;
use DateTime;

class AppointmentController extends Controller
{
    //GET appointments list
    //done
    public function index($pet_id)
    {
        if (
            Auth::user()->id ==
            DB::table('pets')
                ->where('id', $pet_id)
                ->first()->owners_id
        ) {
            $appointment = PetAppointment::where('pet_id', $pet_id)->get();
            return response()->json($appointment);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    //done
    public function showAll()
    {
        $loggedUser = Auth::User();
        if ($loggedUser->role_id === UserRole::ADMINISTRATOR) {
            $appointment = PetAppointment::all();
            return response()->json($appointment);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    //GET appointment by appointment ID
    //done
    public function detail(int $pet_id, int $id)
    {
        $appointment = DB::table('PetAppointment')
            ->where('pet_id', $pet_id)
            ->where('id', $id)
            ->get();
        return response()->json($appointment);
    }
    // POST add appointment
    //done
    public function store(Request $request, int $pet_id)
    {
        //get id of pets owner
        $owners_id = DB::table('pets')
            ->where('id', $pet_id)
            ->first()->owners_id;
        //authorize user vs owner
        $this->AuthUser($owners_id);
        //validate input
        $input = $this->validateRegistration($request);
        //create input
        if (
            DB::table('pets')
                ->where('id', $pet_id)
                ->first()->owners_id === Auth::User()->id
        ) {
            $object = json_decode(json_encode($input), false);

            $appointment = $this->createAppointment(
                $object,
                $pet_id,
                $owners_id
            );
            //add input to database
            $appointment->save();
            //respond
            return response()->json($appointment, JsonResponse::HTTP_CREATED);
        } else {
            return response()->json("Unauthorized", 401);
        }
    }
    //create Appointment for POST add appointment
    //done
    public function createAppointment(object $data, int $pet_id, int $owners_id)
    {
        $date = DateTime::createFromFormat('j. n. Y', $data->date);
        try {
            return PetAppointment::create([
                'pet_id' => $pet_id,
                'date' => $date,
                'description' => $data->description,
                'owners_id' => $owners_id,
            ]);
        } catch (\Exception $ex) {
            throw new HttpResponseException(
                response()->json(
                    [
                        'errors' =>
                            "Error creating appointment: " . $ex->getMessage(),
                    ],
                    JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                )
            );
        }
    }
    // DEL remove appointment
    //TODO Authentication
    //done
    public function remove(int $pet_id, int $id)
    {
        try {
            $this->AuthUser(
                DB::table('pets')
                    ->where('id', $pet_id)
                    ->first()->owners_id
            );
        } catch (\Exception $e) {
            return response()->json("non-existent pet or appointment", 404);
        }

        PetAppointment::where('id', $id)
            ->where('pet_id', $pet_id)
            ->delete();
        return response()->json("Deleted", JsonResponse::HTTP_OK);
    }
    // PUT Update appointment
    //TODO Authentication
    //done
    public function update(Request $request, int $pet_id, int $id)
    {
        // verify user
        $this->AuthPet($pet_id);
        PetAppointment::where('pet_id', $pet_id)
            ->where('id', $id)
            ->FirstOrFail();
        $input = $this->validateRegistration($request, $id);
        $date = DateTime::createFromFormat('j. n. Y', $request->date);
        PetAppointment::where('id', $id)
            ->where('pet_id', $pet_id)
            ->update([
                'date' => $date,
                'description' => $request->description,
            ]);
        return response()->json(
            PetAppointment::find($id),
            JsonResponse::HTTP_OK
        );
    }
    protected function validateRegistration(Request $request)
    {
        // get data from json
        $input = json_decode($request->getContent(), true);
        // prepare validator
        $validator = Validator::make((array) $input, [
            'date' => 'required',
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
    public function AuthPet(int $pet_id)
    {
        $requestUser = Pet::Find($pet_id);
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
}
