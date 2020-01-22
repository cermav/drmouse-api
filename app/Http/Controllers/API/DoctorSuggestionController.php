<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\DoctorSuggestion;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class DoctorSuggestionController extends Controller
{
    public function store(Request $request)
    {
        // get data from json
        $input = json_decode($request->getContent());

        // prepare validator
        $validator = Validator::make((array) $input, [
            'name' => 'required|string',
            'address' => 'required|string',
            'description' => 'string'
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }

        Mail::to(config('mail.service_email'))->send(new DoctorSuggestion($input));

        return response()->json($input, JsonResponse::HTTP_CREATED);

    }
}
