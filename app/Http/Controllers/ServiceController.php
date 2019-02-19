<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use App\Service;

class ServiceController extends Controller
{
    public function getServiceByName() {
        $name = Input::get('name') ? Input::get('name') : "";
        $properties = Service::where([
                ['name', 'like', $name.'%'],
                ['is_approved', '=', 1],
                ['show_on_registration', '=', 0]
        ])->get();
        return response()->json($properties);
    }
}
