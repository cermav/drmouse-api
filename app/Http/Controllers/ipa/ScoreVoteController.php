<?php

namespace app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScoreVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScoreVoteController extends Controller
{
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
            'score_id' => 'required|integer',
            'author_id' => 'integer',
            'value' => 'integer|required',
            'ip_address' => [],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // validate ip address
        if (
            ScoreVote::where([
                ['score_id', $input->score_id],
                ['ip_address', $_SERVER['REMOTE_ADDR']],
            ])->exists()
        ) {
            return response()->json("Request from same IP.", 401);
        }

        // store score vote
        $vote = ScoreVote::create([
            'score_id' => $input->score_id,
            'author_id' => property_exists($input, 'author_id')
                ? $input->author_id
                : null,
            'value' => $input->value,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);

        return $vote;
    }
}
