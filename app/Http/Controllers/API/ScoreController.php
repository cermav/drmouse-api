<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Score;
use App\ScoreDetail;
use App\Http\Resources\ScoreResource;

class ScoreController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
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

    /**
     * Display the specified resource.
     *
     * @param  App\Score  $score
     * @return \Illuminate\Http\Response
     */
    public function show(Score $score) {
        return response()->json(null, 501);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $score = Score::create([
                    'comment' => $request['comment'],
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                    'is_approved' => 0,
                    'user_id' => $request['userId']
        ]);
        foreach ($request['rating'] as $item) {
            ScoreDetail::create([
                'score_id' => $score->id,
                'score_item_id' => $item['id'],
                'points' => $item['score']
            ]);
        }
        return new ScoreResource($score);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Score $score
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Score $score) {
        return response()->json(null, 501);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Score $score
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, Score $score) {
        return response()->json(null, 501);
    }

}
