<?php

namespace app\Http\Controllers\API\Mobile;

use App\Models\Doctor;
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
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
