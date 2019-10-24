<?php

namespace App\Http\Controllers\Api;

use App\Property;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
}
