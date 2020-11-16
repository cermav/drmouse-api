<?php

namespace app\Http\Controllers\API\Admin;

use app\Models\ScoreItem;
use App\Types\UserRole;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use app\Models\Score;
use app\Models\ScoreDetail;
use App\Http\Resources\ScoreResource;
use Illuminate\Support\Facades\Validator;

class ScoreController extends Controller
{
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

        $whereArray = [];

        // search by status
        if ($request->has('status') && intval($request->input('status')) > 0) {
            $whereArray[] = ['status_id', intval($request->input('status'))];
        }

        // add fulltext condition
        if (
            $request->has('fulltext') &&
            strlen(trim($request->input('fulltext'))) > 2
        ) {
            $whereArray[] = [
                'comment',
                'LIKE',
                '%' . trim($request->input('fulltext')) . '%',
            ];
        }

        return response()->json(
            ScoreResource::collection(Score::where($whereArray)->get())
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
        if (Auth::User()->role_id != UserRole::ADMINISTRATOR) {
            throw new AuthenticationException();
        }

        $dateFrom = Input::get('date_from');
        $whereArray = [['is_approved', '=', 1]];
        array_push($whereArray, ['user_id', '=', $id]);
        if ($dateFrom) {
            array_push($whereArray, ['created_at', '>', $dateFrom]);
        }
        //var_dump(Score::where($whereArray));die;
        return ScoreResource::collection(
            Score::select(
                'id',
                'user_id',
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        if (Auth::User()->role_id != UserRole::ADMINISTRATOR) {
            throw new AuthenticationException();
        }

        $input = json_decode($request->getContent());

        // validate input
        $validator = Validator::make((array) $input, [
            'status_id' => 'int',
            'comment' => 'string',
        ]);
        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $score = Score::find($id);

        if (
            property_exists($input, 'status_id') &&
            intval($input->status_id) > 0
        ) {
            $score->status_id = intval($input->status_id);

            // add validation info
            $score->verify_date = date('Y-m-d H:i:s');
            $score->verified_by = Auth::User()->id;
        }

        if (property_exists($input, 'comment') && !empty($input->comment)) {
            $score->comment = $input->comment;
        }

        $score->update();

        return $score;
    }
}
