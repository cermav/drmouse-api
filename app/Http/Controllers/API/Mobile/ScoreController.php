<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Resources\Mobile\ScoreResource;
use App\Score;
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
        $whereArray = [];

        // add update condition
        $validatedDate = $request->validate(['updated' => 'date']);
        if (array_key_exists('updated', $validatedDate)) {
            $whereArray[] = ['scores.updated_at', '>', $validatedDate['updated']];
        }
        return ScoreResource::collection(Score::where($whereArray)->get());
    }
}
