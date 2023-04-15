<?php

namespace App\Http\Controllers\Api;

use App\Helpers\AuthHelper;
use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\PetAppointment;
use App\Types\UserRole;
use DateTime;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 */
class AppointmentController extends Controller {
    /**
     * @param $pet_id
     * @return JsonResponse list of pet appointments
     */
    public function index($pet_id): JsonResponse {
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

    /**
     * @return JsonResponse
     */
    public function showAll(): JsonResponse {
        $loggedUser = Auth::User();
        if ($loggedUser->role_id === UserRole::ADMINISTRATOR) {
            $appointment = PetAppointment::all();
            return response()->json($appointment);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * @param int $pet_id
     * @param int $id
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthenticationException
     */
    public function detail(int $pet_id, int $id): JsonResponse {
        $this->authorizeUser($pet_id);

        $appointment = PetAppointment::Find($id)->where('pet_id', $pet_id)->get();
        return response()->json($appointment);
    }

    /**
     * @param Request $request
     * @param int $pet_id
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function store(Request $request, int $pet_id): JsonResponse {
        $this->authorizeUser($pet_id);

        $input = $this->validateInputData($request);

        $object = json_decode(json_encode($input), false);

        $appointment = $this->createAppointment(
            $object,
            $pet_id,
            Auth::user()->id
        );

        $appointment->save();

        return response()->json($appointment, Response::HTTP_CREATED);
    }

    /**
     * @param int $pet_id
     * @param int $owners_id
     * @return mixed
     * @throws AuthenticationException
     */
    private
    function createAppointment($data, int $pet_id, int $owners_id) {
        $this->authorizeUser($pet_id);
        try {
            return PetAppointment::create([
                'title' => $data->title,
                'pet_id' => $pet_id,
                'date' => DateTime::createFromFormat('j. n. Y', $data->date),
                'owners_id' => $owners_id,
                'doctor_id' => $data->doctor_id,
                'start' => $data->start,
                'end' => $data->end,
                'allDay' => !$data->start && !$data->end
            ]);
        } catch (Exception $ex) {
            throw new HttpResponseException(
                response()->json(
                    [
                        'errors' =>
                            "Error creating appointment: " . $ex->getMessage(),
                    ],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                )
            );
        }
    }

    /**
     * @param int $pet_id
     * @param int $id
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public
    function remove(int $pet_id, int $id): JsonResponse {
        $this->authorizeUser($pet_id);

        PetAppointment::where('id', $id)
            ->where('pet_id', $pet_id)
            ->delete();
        return response()->json("Deleted", Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $pet_id
     * @param int $id
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthenticationException
     */
    public
    function update(Request $request, int $pet_id, int $id): JsonResponse {
        $this->authorizeUser($pet_id);

        $appointment = PetAppointment::where('pet_id', $pet_id)
            ->where('id', $id)
            ->FirstOrFail();

        $this->validateInputData($request);

        $appointment->update([
            'date' => DateTime::createFromFormat('j. n. Y', $request->date),
            'title' => $request->title,
            'doctor_id' => $request->doctor_id
        ]);
        return response()->json(
            PetAppointment::find($id),
            Response::HTTP_OK
        );
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private
    function validateInputData(Request $request) {
        $input = json_decode($request->getContent(), true);

        $validator = Validator::make((array)$input, [
            'date' => 'required',
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json(
                    ['errors' => $validator->errors()],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                )
            );
        }

        return $input;
    }

    /**
     * @throws AuthenticationException
     */
    private function authorizeUser(int $pet_id): Pet {
        $pet = Pet::findOrFail($pet_id);
        AuthHelper::authorizeUser($pet->owners_id);

        return $pet;
    }
}
