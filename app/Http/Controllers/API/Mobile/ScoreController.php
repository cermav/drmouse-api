<?php

namespace app\Http\Controllers\API\Mobile;

use App\Http\Resources\Mobile\ScoreResource;
use app\Models\Score;
use App\Types\ScoreStatus;
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
        $whereArray = [['status_id', ScoreStatus::APPROVED]];

        // add update condition
        $validatedDate = $request->validate(['updated' => 'date']);
        if (array_key_exists('updated', $validatedDate)) {
            $whereArray[] = [
                'scores.updated_at',
                '>',
                $validatedDate['updated'],
            ];
        }
        return ScoreResource::collection(Score::where($whereArray)->get());
    }
}
