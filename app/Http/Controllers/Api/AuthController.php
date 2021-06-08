<?php
namespace app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Types\DoctorStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use App\Helpers\JwtDecoderHelper;
use Illuminate\Http\Exceptions\HttpResponseException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator, DB, Hash, Mail, Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
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
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        // attempt to verify the credentials
        $user = User::where('email', $credentials['email'])->first();
        if ($user == null) {
            return response()->json(
                ['error' => 'No user with entered e-mail found.'],
                404
            );
        }
        if ($user->email_verified_at === null) {
            return response()->json(
                [
                    'error' => 'Account is not activated.',
                ],
                403
            );
        }
        if (!Hash::check($credentials['password'], $user->password)) {
            return response()->json(['error' => 'Incorrect password.'], 401);
        }

        try {
            // create a token for the user
            $token = JWTAuth::fromUser($user);

            // check user wheter it is first login
            if ($user->activated_at === null) {
                $user->activated_at = date('Y-m-d H:i:s');
                $user->status_id = DoctorStatus::ACTIVE;
                $user->save();
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(
                ['error' => 'Failed to login, please try again.'],
                500
            );
        }
        // all good so return the token
        return $this->respondWithToken($token);
    }

    public function google(Request $request)
    {

        try {
            $header = $request->header('Authorization');
            return response()->json(['header' => $header, 200]);
        }
            catch(\HttpResponseException $ex) {
                return response()->json(
                    ['error' => $ex]
                );
            }

            
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    public function info()
    {
        return Auth::user();
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
            return response()->json([
                'message' => "You have successfully logged out.",
            ]);
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(
                ['error' => 'Failed to logout, please try again.'],
                500
            );
        }
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' =>
                auth('api')
                    ->factory()
                    ->getTTL() * 60,
        ]);
    }
}
