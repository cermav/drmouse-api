<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator, DB, Hash, Mail, Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    /**
     * API Register
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $credentials = $request->only('name', 'email', 'password', 'surname', 'relationship', 'birthday', 'gender', 'ethnicity', 'ethnicity_other');
        $validator = Validator::make($credentials, User::getValidationRules());
        if($validator->fails()) {
            return response()->json(['success'=> false, 'error'=> $validator->messages()]);
        }
        $user_data = [
            'name' => $request->name,
            'role_id' => 2,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ];
        $optional_fields = ['avatar'];
        foreach ($optional_fields as $field) {
            if ($request->has($field) && !empty($request->$field)) {
                if ($field == 'birthday') {
                    $user_data[$field] = date('Y-m-d', strtotime($request->get($field)));
                } else {
                    $user_data[$field] = $request->get($field);
                }

            }
        }

        User::create($user_data);
        return $this->login($request);
    }
    /**
     * API Login, on success return JWT Auth token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // validate input data
        $credentials = $request->only('email', 'password');
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if($validator->fails()) {
            return response()->json(['error'=> $validator->messages()], 400);
        }

        // attempt to verify the credentials
        $user = User::where('email', $credentials['email'])->first();
        if ($user == null || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['error' => 'We cant find an account with this credentials.'], 401);
        }

        try {
            // create a token for the user
            $token = JWTAuth::fromUser($user);

        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'Failed to login, please try again.'], 500);
        }
        // all good so return the token
        return $this->respondWithToken($token);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Log out
     * Invalidate the token, so user cannot use it anymore
     * They have to relogin to get a new token
     *
     * @param Request $request
     */
    public function logout(Request $request)
    {

        $this->validate($request, ['token' => 'required']);
        try {
            JWTAuth::invalidate($request->input('token'));
            auth()->logout();
            return response()->json(['message'=> "You have successfully logged out."]);
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'Failed to logout, please try again.'], 500);
        }
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
