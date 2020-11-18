<?php

namespace app\Http\Controllers\API\Mobile;

use App\Models\Property;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
            $categoryValidate = $request->validate([
                'category' => 'required|integer|min:1|max:3',
            ]);

            $properties = Property::where([
                [
                    'property_category_id',
                    '=',
                    intval($categoryValidate['category']),
                ],
                ['is_approved', '=', 1],
            ])
                ->select('id', 'name')
                ->get();
            return response()->json($properties);
        } catch (ValidationException $ex) {
            return response()->json($ex->errors(), 400);
        }
    }
}
