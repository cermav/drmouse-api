<?php

namespace app\Http\Controllers\API;

use App\Models\DoctorsProperty;
use app\Models\Property;
use App\Types\UserRole;
use App\Models\User;
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
            $categoryId = intval($request->get('category'));

            if ($categoryId > 0) {
                $properties = Property::select(
                    'id',
                    'name',
                    DB::raw(
                        "(SELECT COUNT(user_id) FROM doctors_properties WHERE property_id = properties.id) AS doctor_count"
                    )
                )
                    ->where([
                        ['property_category_id', '=', $categoryId],
                        ['is_approved', '=', 1],
                    ])
                    ->orderBy('name', 'asc')
                    ->get();
            } else {
                $properties = [];
                $all = Property::select(
                    'id',
                    'property_category_id',
                    'name',
                    DB::raw(
                        "(SELECT COUNT(user_id) FROM doctors_properties WHERE property_id = properties.id) AS doctor_count"
                    )
                )
                    ->where([['is_approved', '=', 1]])
                    ->orderBy('name', 'asc')
                    ->get();
                foreach ($all as $item) {
                    $properties[$item->property_category_id][] = (object) [
                        'id' => $item->id,
                        'name' => $item->name,
                        'doctor_count' => $item->doctor_count,
                    ];
                }
            }

            return response()->json($properties);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), 400);
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

        if (
            $requestUser->id === $loggedUser->id ||
            $loggedUser->role_id === UserRole::ADMINISTRATOR
        ) {
            // validate input
            $input = json_decode($request->getContent());
            foreach ($input->values as $value) {
                $validator = PropertyValidator::create((array) $value);
                if ($validator->fails()) {
                    throw new HttpResponseException(
                        response()->json(
                            ['errors' => $validator->errors()],
                            JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                        )
                    );
                }
            }

            $category_id = intval($input->category_id);
            if ($category_id > 0) {
                // remove all records
                DoctorsProperty::where('user_id', $requestUser->id)
                    ->whereRaw(
                        "property_id IN (SELECT id FROM properties WHERE property_category_id = " .
                            $category_id .
                            ")"
                    )
                    ->delete();

                // save each new record
                foreach ($input->values as $value) {
                    DoctorsProperty::create([
                        'user_id' => $requestUser->id,
                        'property_id' => $value->id,
                    ]);
                }
            }

            // return data
            $all_properties = $requestUser
                ->properties()
                ->where('is_approved', 1)
                ->get();
            // split properties
            $properties = [];
            $categories = [1 => 'equipment', 'expertise', 'specialization'];
            foreach ($all_properties as $item) {
                $properties[
                    $categories[$item->property_category_id]
                ][] = (object) ['id' => $item->id, 'name' => $item->name];
            }
            return response()->json($properties, JsonResponse::HTTP_OK);
        } else {
            // return unauthorized
            throw new AuthenticationException();
        }
    }
}
