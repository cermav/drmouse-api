<?php

namespace app\Http\Controllers\API\Mobile;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $services = Service::where([['is_approved', '=', 1]])->get();
        return response()->json($services);
    }
}
