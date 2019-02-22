<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Score;
use App\ScoreDetail;

class ScoreController extends Controller {

    public function saveScore(Request $request) {
        $success = true;
        try {
            $score = Score::create([
                        'comment' => $request['comment'],
                        'ip_address' => $_SERVER['REMOTE_ADDR'],
                        'score_date' => date('Y-m-d H:i:s'),
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
        } catch (Exception $e) {
            $success = false;
        }
        return response()->json(['success'=>$success]);
    }

}
