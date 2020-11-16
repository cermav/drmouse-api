<?php

namespace app\Http\Controllers\API\Mobile;

use App\Http\Controllers\Controller;
use App\Models\OpeningHour;
use Illuminate\Http\Request;

class OpeningHoursController extends Controller
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
            $whereArray[] = [
                'opening_hours.updated_at',
                '>',
                $validatedDate['updated'],
            ];
        }
        return OpeningHour::where($whereArray)
            ->select(
                'id',
                'user_id',
                'weekday_id',
                'open_at',
                'close_at',
                'updated_at'
            )
            ->get();
    }
}
