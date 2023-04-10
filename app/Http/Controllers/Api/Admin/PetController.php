<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\AuthHelper;
use App\Models\Pet;
use Illuminate\Http\JsonResponse;

class PetController extends \Illuminate\Routing\Controller {
    public function showAll(): JsonResponse {
        AuthHelper::authorizeAdmin();

        return response()->json(Pet::all());
    }
}
