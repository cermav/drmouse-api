<?php

namespace App\Http\Controllers\API\Auth;


use App\Http\Controllers\Controller;
use App\Notifications\ActivationDoctorEmail;
use App\Types\UserRole;
use App\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ActivationController extends Controller
{
    use Notifiable;

    private $email;

    public function activate(Request $request, int $id)
    {
        // validate input
        $requestData = json_decode($request->getContent());

        // verify user
        $requestUser = User::find($id);
        $this->email = $requestData->email;

        if (
            $requestUser &&
            $requestUser->role_id === UserRole::DOCTOR &&
            $requestUser->activated_at === null &&
            strtolower($requestUser->email) === strtolower($this->email)
        ) {

            // generate new password
            $newPassword = Str::random(rand(10, 12));

            // update user with new password and date or verified email
            $requestUser->password = Hash::make($newPassword);
            $requestUser->email_verified_at = date('Y-m-d H:i:s');
            $requestUser->save();

            // email user with new password
            $this->notify(new ActivationDoctorEmail($newPassword)); // my notification

            return response()->json('Account activated.', JsonResponse::HTTP_OK);

        } else {
            // return unauthorized
            throw new HttpResponseException(
                response()->json(['errors' => 'Account is not found or has been already activated'], JsonResponse::HTTP_UNAUTHORIZED)
            );
        }

    }

}