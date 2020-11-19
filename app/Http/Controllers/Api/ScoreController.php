<?php

namespace app\Http\Controllers\Api;

use App\Models\ScoreItem;
use App\Types\ScoreStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Models\Score;
use App\Models\ScoreDetail;
use App\Http\Resources\ScoreResource;
use Illuminate\Support\Facades\Validator;

class ScoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(
            ScoreItem::where('status_id', ScoreStatus::APPROVED)->get()
        );
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
        $whereArray = [['status_id', '=', ScoreStatus::APPROVED]];
        array_push($whereArray, ['user_id', '=', $id]);
        if ($dateFrom) {
            array_push($whereArray, ['created_at', '>', $dateFrom]);
        }
        //var_dump(Score::where($whereArray));die;
        return ScoreResource::collection(
            Score::select(
                'id',
                'user_id',
                'author_id',
                'comment',
                'ip_address',
                'created_at',
                'updated_at',
                DB::raw(
                    "(SELECT SUM(value) FROM score_votes WHERE score_id = scores.id) AS voting"
                )
            )
                ->where($whereArray)
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
        $validator = Validator::make((array) $input, [
            'user_id' => 'required|integer',
            'author_id' => 'integer|nullable',
            'comment' => 'string|required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // store score
        $score = Score::create([
            'user_id' => $input->user_id,
            'author_id' => property_exists($input, 'author_id')
                ? (intval($input->author_id) > 0
                    ? intval($input->author_id)
                    : null)
                : null,
            'comment' => $input->comment,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'is_approved' => 0,
        ]);

        // store score items
        foreach ($input->score_item as $item) {
            $validator = Validator::make((array) $item, [
                'id' => 'required|integer',
                'points' => 'required|integer',
            ]);
            if ($validator->validate()) {
                ScoreDetail::create([
                    'score_id' => $score->id,
                    'score_item_id' => $item->id,
                    'points' => $item->points,
                ]);
            }
        }
        return new ScoreResource($score);
    }
}
