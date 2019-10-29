<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Doctor;
use App\Http\Resources\DoctorResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{

    private $pageLimit = 30;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // prepare basic select
        $doctors = DB::table('doctors')
            ->select(
                'users.id',
                'name',
                'slug',
                'street',
                'city',
                'country',
                'post_code',
                'avatar',
                DB::raw("(SELECT GROUP_CONCAT(property_id) FROM doctors_properties WHERE user_id = users.id) AS properties")
            )
            ->join('users', 'doctors.user_id', '=', 'users.id')
            ->where('doctors.state_id', 1);

        // add fulltext condition
        if ($request->has('fulltext') && strlen(trim($request->input('fulltext'))) > 2) {
            $doctors->whereRaw(
                "MATCH (search_name, description, street, city, country) AGAINST (? IN NATURAL LANGUAGE MODE)",
                trim($request->input('fulltext'))
            );
        }

        // add specialization condition
        if ($request->has('spec') && intval($request->input('spec')) > 0) {
            $doctors->whereExists(function ($query) use ($request) {
                $query->select(DB::raw(1))
                    ->from('doctors_properties')
                    ->where([
                        ['doctors_properties.user_id', '=', 'users.id'],
                        ['doctors_properties.property_id', '=', intval($request->input('spec'))]
                    ]);
            });
        }

        // add experience condition
        if ($request->has('exp') && intval($request->input('exp')) > 0) {
            $doctors->whereExists(function ($query) use ($request) {
                $query->select(DB::raw(1))
                    ->from('doctors_properties')
                    ->where([
                        ['doctors_properties.user_id', '=', 'users.id'],
                        ['doctors_properties.property_id', '=', intval($request->input('exp'))]
                    ]);
            });
        }

        // sorting
        $order_fields = [];
        if ($request->has('order') && in_array(trim($request->input('order')), $order_fields)) {
            $direction = $request->has('dir') && strtolower(trim($request->input('dir') == 'desc')) ? 'desc' : 'asc';
            $doctors->orderBy(trim($request->input('order')), $direction);
        }


        return $doctors->paginate($this->pageLimit);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return response()->json(null, 501);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $doctor = Doctor::where(['user_id', '=', $id], ['state_id', '=', '1'])->get();
        if (sizeof($doctor) > 0) {
            return DoctorResource::collection($doctor)->first();
        }
        return response()->json(['message' => 'Not Found!'], 404);
    }

    /**
     * Display doctor by slug
     * @param $slug
     */
    public function showBySlug($slug)
    {
        $doctor = Doctor::where('slug', $slug)->get();
        if (sizeof($doctor) > 0) {
            return DoctorResource::collection($doctor)->first();
        }
        return response()->json(['message' => 'Not Found!'], 404);
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
        return response()->json(null, 501);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json(null, 501);
    }
}
