<?php

namespace App\Http\Controllers\Api\Mobile;

use App\ScoreItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ScoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(ScoreItem::get());

        $userId = Input::get('user_id');
        $dateFrom = Input::get('date_from');
        $whereArray = [['is_approved', '=', 1]];
        if ($userId) {
            array_push($whereArray, ['user_id', '=', $userId]);
        }
        if ($dateFrom) {
            array_push($whereArray, ['created_at', '>', $dateFrom]);
        }
        //var_dump(Score::where($whereArray));die;
        return ScoreResource::collection(Score::where($whereArray)->get());
    }
}
