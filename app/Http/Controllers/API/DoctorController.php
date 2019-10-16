<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Doctor;
use App\Http\Resources\DoctorResource;

class DoctorController extends Controller {

    private $pageLimit = 30;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $whereArray = [['state_id', '=', 1]];
        return Doctor::where($whereArray)->paginate($this->pageLimit);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        return response()->json(null, 501);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        $doctor = Doctor::where('id', $id)->get();
        if (sizeof($doctor) > 0) {
            return DoctorResource::collection( $doctor )->first();
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
            return DoctorResource::collection( $doctor )->first();
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
    public function update(Request $request, $id) {
        return response()->json(null, 501);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        return response()->json(null, 501);
    }

}
