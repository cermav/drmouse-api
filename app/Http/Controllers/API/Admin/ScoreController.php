<?php

namespace App\Http\Controllers\Api\Admin;

use App\ScoreItem;
use App\Types\UserRole;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Score;
use App\ScoreDetail;
use App\Http\Resources\ScoreResource;
use Illuminate\Support\Facades\Validator;


class ScoreController extends Controller {


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::User()->role_id != UserRole::ADMINISTRATOR) {
            throw new AuthenticationException();
        }

        // search by status
        if ($request->has('status') && intval($request->input('status')) > 0) {
            $result = ScoreItem::where('status_id', intval($request->input('status')))->get();
        } else {
            return response()->json(
                ScoreResource::collection(ScoreItem::where('status_id', 10))
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $dateFrom = Input::get('date_from');
        $whereArray = [['is_approved', '=', 1]];
        array_push($whereArray, ['user_id', '=', $id]);
        if ($dateFrom) {
            array_push($whereArray, ['created_at', '>', $dateFrom]);
        }
        //var_dump(Score::where($whereArray));die;
        return ScoreResource::collection(Score::select(
            'id', 'user_id', 'comment', 'ip_address', 'created_at', 'updated_at',
            DB::raw("(SELECT SUM(value) FROM score_votes WHERE score_id = scores.id) AS voting")
        )->where($whereArray)->get());
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id) {
        $input = json_decode($request->getContent());

        // validate input
        $validator = Validator::make((array)$input, [
            'is_approved' => 'boolean',
            'comment' => 'string'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $score = Score::find($id);

        if (property_exists($input, 'is_approved') && $input->is_approved === true) {
           $score->is_approved = true;
        }

        if (property_exists($input, 'comment') && !empty($input->comment)) {
            $score->comment = $input->comment;
        }
        $score->update();

        return $score;
    }

}
