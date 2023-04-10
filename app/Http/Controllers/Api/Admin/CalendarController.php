<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\AuthHelper;
use App\Models\Pet;
use App\Models\PetAppointment;
use Illuminate\Http\JsonResponse;

class CalendarController extends \Illuminate\Routing\Controller {
    public function showAll(): JsonResponse {
        AuthHelper::authorizeAdmin();

        return response()->json(PetAppointment::all());
    }
}
