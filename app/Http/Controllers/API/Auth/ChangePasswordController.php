<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Types\UserRole;
use app\Models\User;
use App\Validators\PasswordValidator;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
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

        if (
            $requestUser->id === $loggedUser->id ||
            $loggedUser->role_id === UserRole::ADMINISTRATOR
        ) {
            // validate input
            $input = json_decode($request->getContent());
            $validator = PasswordValidator::create((array) $input);

            if ($validator->fails()) {
                throw new HttpResponseException(
                    response()->json(
                        ['errors' => $validator->errors()],
                        JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                    )
                );
            }

            // update password
            if (
                Hash::check($input->current_password, $requestUser->password) ||
                $loggedUser->role_id === UserRole::ADMINISTRATOR
            ) {
                $requestUser->password = Hash::make($input->password);
                $requestUser->save();
            } else {
                return response()->json(
                    (object) [
                        'errors' => [
                            'current_password' => [
                                'Please enter correct current password',
                            ],
                        ],
                    ],
                    400
                );
            }

            return response()->json('Password changed.', JsonResponse::HTTP_OK);
        } else {
            // return unauthorized
            throw new AuthenticationException();
        }
    }
}
