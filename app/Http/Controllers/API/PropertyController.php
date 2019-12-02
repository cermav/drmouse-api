<?php

namespace App\Http\Controllers\Api;

use App\Models\DoctorsProperty;
use App\Property;
use App\User;
use App\Validators\PropertyValidator;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $categoryValidate = $request->validate(['category' => 'required|integer|min:1|max:3']);

            $properties = Property::select(
                    'id', 'name',
                    DB::raw("(SELECT COUNT(user_id) FROM doctors_properties WHERE property_id = properties.id) AS doctor_count")
                )
                ->where([
                    ['property_category_id', '=', intval($categoryValidate['category'])],
                    ['is_approved', '=', 1]
                ])
                ->orderBy('doctor_count', 'desc')
                ->get();
            return response()->json($properties);

        }
        catch (ValidationException $ex) {
            return response()->json($ex->errors(), 400);
        }
    }

    /**
     * Change user password
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        // verify user
        $requestUser = User::find($id);
        $loggedUser = Auth::User();

        if ($requestUser->id === $loggedUser->id || $loggedUser->role_id === UserRole::ADMINISTRATOR) {

            // validate input
            $input = json_decode($request->getContent());
            foreach ($input->values as $value) {
                $validator = PropertyValidator::create((array)$value);
                if ($validator->fails()) {
                    throw new HttpResponseException(
                        response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
                    );
                }
            }

            $category_id = intval($input->category_id);
            if ($category_id > 0) {
                // remove all records
                DoctorsProperty::where('user_id', $requestUser->id)
                    ->whereRaw("property_id IN (SELECT id FROM properties WHERE property_category_id = ".$category_id.")")
                    ->delete();

                // save each new record
                foreach ($input->values as $value) {
                    DoctorsProperty::create([
                        'user_id' => $requestUser->id,
                        'property_id' => $value->id
                    ]);
                }
            }

            return response()->json('Property changed.', JsonResponse::HTTP_OK);

        } else {
            // return unauthorized
            throw new AuthenticationException();
        }

    }
}
