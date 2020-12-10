<?php

namespace app\Http\Controllers\Api\Mobile;

use App\Models\ScoreItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ScoreCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(ScoreItem::select('id', 'title')->get());
    }
}
