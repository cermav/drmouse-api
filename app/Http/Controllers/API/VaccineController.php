<?php

namespace app\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Vaccines;
use App\Models\Pet;
use App\Models\PetAppointment;
use App\Models\Member;
use App\Models\DoctorsLog;
use App\Http\Controllers\HelperController;
use App\Models\ScoreItem;
use App\Types\DoctorStatus;
use App\Models\User;
use App\Types\UserRole;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Doctor;
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
            $vaccines = Vaccine::all();
            return response()->json($vaccines);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    //GET vaccines from pet ID
    //TODO Authentication
    //done
    public function index($id)
    {
        $vaccine = DB::table('vaccines')
            ->where('pet_id', $id)
            ->get();

        return response()->json($vaccine);
    }
    //done
    public function detail($pet_id, $vac_id)
    {
        $vaccine = DB::table('vaccines')
            ->where('pet_id', $pet_id)
            ->where('id', $vac_id)
            ->get();
        return response()->json($vaccine);
    }
    // POST add vaccine by pet_id
    //done
    public function store(Request $request, $pet_id)
    {
        // validate input
        Pets::FindOrFail($pet_id);
        $input = $this->validateRegistration($request);
        if (
            DB::table('pets')
                ->where('id', $pet_id)
                ->first()->owners_id === Auth::User()->id
        ) {
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
        return Vaccines::create([
            'apply_date' => $data->apply_date,
            'pet_id' => $pet_id,
            'valid' => $data->valid,
            'name' => $data->name,
            'price' => $data->price,
        ]);
    }
    // DEL remove appointment
    //TODO Authentication
    //done
    public function remove(int $pet_id, int $vac_id)
    {
        try {
            $this->AuthUser(
                DB::table('Pets')
                    ->where('id', $pet_id)
                    ->first()->owners_id
            );
        } catch (\Exception $e) {
            return response()->json("non-existent pet or vaccine", 404);
        }
        DB::table('Vaccines')
            ->where('id', $vac_id)
            ->where('pet_id', $pet_id)
            ->delete();
        return response()->json("Deleted", JsonResponse::HTTP_OK);
    }
    // PUT Update appointment
    //TODO Authentication
    //done
    public function update(Request $request, int $pet_id, int $id)
    {
        try {
            $this->AuthUser(
                DB::table('Pets')
                    ->where('id', $pet_id)
                    ->first()->owners_id
            );
        } catch (\Exception $e) {
            return response()->json("non-existent pet or vaccine", 404);
        }
        Vaccines::where('pet_id', $pet_id)
            ->where('id', $id)
            ->FirstOrFail();
        $input = $this->validateRegistration($request, $id);
        $data = [
            'Vaccines' => [
                'apply_date' => $request->apply_date,
                'valid' => $request->valid,
                'name' => $request->name,
                'price' => $request->price,
            ],
        ];
        return response()->json(
            Pets_appointments::find($id),
            JsonResponse::HTTP_OK
        );

        //} //else {
        // return unauthorized
        //throw new AuthenticationException();
        //}
    }
    protected function validateRegistration(Request $request)
    {
        // get data from json
        $input = json_decode($request->getContent());
        // prepare validator
        $validator = Validator::make((array) $input, [
            'apply_date' => 'required',
            'valid' => 'required',
            'name' => 'required',
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
}
