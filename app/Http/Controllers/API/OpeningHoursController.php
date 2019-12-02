<?php

namespace App\Http\Controllers\Api;

use App\Models\OpeningHour;
use App\User;
use App\Validators\OpeningHoursValidator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OpeningHoursController extends Controller
{
    /**
     * Change user password
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        // verify user
        $requestUser = User::find($id);
        $loggedUser = Auth::User();

        if ($requestUser->id === $loggedUser->id || $loggedUser->role_id === UserRole::ADMINISTRATOR) {

            // validate input
            $input = json_decode($request->getContent());
            foreach ($input as $day) {
                foreach ($day as $item) {
                    $validator = OpeningHoursValidator::create((array)$item);
                    if ($validator->fails()) {
                        var_dump($item);
                        throw new HttpResponseException(
                            response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
                        );
                    }
                }
            }

            // remove all records
            OpeningHour::where('user_id', $requestUser->id)->delete();

            // save each new record
            foreach ($input as $day) {
                foreach ($day as $item) {
                    OpeningHour::create([
                        'weekday_id' => $item->weekday_id,
                        'user_id' => $requestUser->id,
                        'opening_hours_state_id' => $item->state_id,
                        'open_at' => $item->open_at,
                        'close_at' => $item->close_at
                    ]);
                }
            }

            return response()->json('Opening hours changed.', JsonResponse::HTTP_OK);

        } else {
            // return unauthorized
            throw new AuthenticationException();
        }

    }
}
