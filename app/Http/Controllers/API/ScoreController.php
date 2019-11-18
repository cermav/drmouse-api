<?php

namespace App\Http\Controllers\Api;

use App\ScoreItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
    public function index()
    {
        return response()->json(ScoreItem::get());
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

    public function waiting()
    {
        return ScoreResource::collection(
            Score::select('id', 'user_id', 'comment', 'ip_address', 'created_at', 'updated_at')
                ->where('is_approved', 0)
                ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = json_decode($request->getContent());

        // validate input
        $validator = Validator::make((array)$input, [
            'user_id' => 'required|integer',
            'author_id' => 'integer',
            'comment' => 'string|required'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        // store score
        $score = Score::create([
            'user_id' => $input->user_id,
            'author_id' => (property_exists($input, 'author_id') ? $input->author_id : null),
            'comment' => $input->comment,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'is_approved' => 0
        ]);

        // store score items
        foreach ($input->score_item as $item) {
            $validator = Validator::make((array)$item, [
               'id' => 'required|integer',
                'points' => 'required|integer',
            ]);
            if ($validator->validate()) {
                ScoreDetail::create([
                    'score_id' => $score->id,
                    'score_item_id' => $item->id,
                    'points' => $item->points
                ]);
            }
        }
        return new ScoreResource($score);
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id) {
        $score = Score::findOrFail($id);
        $score->delete();

        return 204;
    }

}
