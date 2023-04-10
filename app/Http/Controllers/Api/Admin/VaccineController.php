<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\AuthHelper;
use App\Models\Pet;
use App\Models\PetAppointment;
use App\Models\PetVaccine;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;

class VaccineController extends \Illuminate\Routing\Controller {
    /**
     * @throws AuthenticationException
     */
    public function showAll(): JsonResponse {
        AuthHelper::authorizeAdmin();

        return response()->json(PetVaccine::all());
    }
}
