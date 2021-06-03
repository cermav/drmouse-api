<?php

namespace app\Http\Controllers\Api\Mobile;

use App\Models\Doctor;
use App\Types\DoctorStatus;
use App\Models\ScoreItem;
use App\Http\Resources\Mobile\DoctorResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $whereArray = [['state_id', '=', 3]];

        // add update condition
        $validatedDate = $request->validate(['updated' => 'date']);
        if (array_key_exists('updated', $validatedDate)) {
            $whereArray[] = [
                'doctors.updated_at',
                '>',
                $validatedDate['updated'],
            ];
        }


        $scoreQuery = [];
        foreach (ScoreItem::get() as $item) {
            $scoreQuery[] = "(
                SELECT IFNULL( ROUND(((SUM(points) / COUNT(id)) / 5) * 100) , 0) 
                FROM score_details 
                WHERE score_id IN (SELECT id FROM scores WHERE user_id = doctors.user_id)
                    AND score_item_id = {$item->id}
            ) AS total_score_{$item->id} ";
        }

        $doctors = Doctor::where($whereArray)
            ->select(
                'doctors.*',
                DB::raw(implode(", ", $scoreQuery)),
                DB::raw("IFNULL((
                    SELECT true
                    FROM opening_hours
                    WHERE user_id = doctors.user_id AND weekday_id = (WEEKDAY(NOW()) + 1)
                      AND (
                        (opening_hours_state_id = 1 AND CAST(NOW() AS time) BETWEEN open_at AND close_at)
                        OR
                        opening_hours_state_id = 3
                      )
                    LIMIT 1)
                  , false) AS open ")
            )
            ->whereIn('state_id', [
                DoctorStatus::NEW,
                DoctorStatus::UNPUBLISHED,
                DoctorStatus::INCOMPLETE,
                DoctorStatus::PUBLISHED,
                DoctorStatus::ACTIVE,
            ])
            ->get();

        
        /*
        return DoctorResource::collection(
            Doctor::where($whereArray)
                ->select(
                    'doctors.*',
                    DB::raw(
                        "(SELECT IFNULL( ROUND(((SUM(points)/COUNT(id))/5)*100) , 0) FROM score_details WHERE score_id IN (SELECT id FROM scores WHERE user_id = doctors.user_id)) AS total_score "
                    )
                )
                ->get()
        );
        */
        if (sizeof($doctors) > 0) {
            return DoctorResource::collection($doctors);
        }
        return response()->json((array)[]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
/*
        $validatedDate = $request->validate(['updated' => 'date']);
        if (array_key_exists('updated', $validatedDate)) {
            $whereArray[] = [
                'doctors.updated_at',
                '>',
                $validatedDate['updated'],
            ];
        }
    ThisModel::where($whereArray)->create([
        'foo' => 'bar,
    ]);
*/
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
