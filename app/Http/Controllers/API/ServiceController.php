<?php

namespace App\Http\Controllers\Api;

use App\Service;
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
        $services = Service::where([
            ['is_approved', '=', 1]
        ])
            ->select(['id', 'name', 'show_on_registration', 'show_in_search'])
            ->get();
        return response()->json($services);
    }
}
