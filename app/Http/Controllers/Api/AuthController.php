<?php
namespace app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Types\DoctorStatus;
use App\Models\User;
use App\Models\Member;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator, DB, Hash, Mail, Illuminate\Support\Facades\Password;
use Google;

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
        
        $client = new \GuzzleHttp\Client();

        $data = json_decode($request->getContent());

        // google PHP client
        
        // http request client
        
        // app id - constant
        
        require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/vendor/autoload.php';
        
        $client_id = env('GOOGLE_APP_ID');
        $google = new Google\Client(['client_id' => $client_id]);
        
        // get json from received request
        // get id token from request json
        $id_token = $data->tokenId;
        
        //verify ID token
        $payload = $google->verifyIdToken($id_token);
        if ($payload) {
            $userMail = $payload['email'];
            $userid = $payload['sub'];
            $emailVerified = $payload['email_verified'];
        } else {
            return response()->json(['error' => 'invalid token'], 401);
        }
        
        $user = User::where('email', $userMail)->first();
        
        if ($user) {
            if ($user->gdpr_agreed == 0)
            {
                $user->update(['gdpr_agreed' => '1']);
            }
            $member = Member::where('user_id', $user->id)->first();
            $doctor = Doctor::where('user_id', $user->id)->first();
            if ($member && $member->gdpr_agreed == 0)
            {
                $member->update(['gdpr_agreed' => 1, 'gdpr_agreed_date' => date('Y-m-d H:i:s')]);
            }
            if ($doctor && $doctor->gdpr_agreed == 0)
            {
                $doctor->update(['gdpr_agreed' => 1, 'gdpr_agreed_date' => date('Y-m-d H:i:s')]);
            }

            $token = JWTAuth::fromUser($user);
            
            return $this->respondWithToken($token);
        }
        else {
           $profile = $data->profileObj;

           $password = bin2hex(random_bytes(16));
           $options = [
               'json' => [
                   'name' => "$profile->givenName $profile->familyName", 
                   'email' => $profile->email,
                   'gdpr' => true,
                   'password' => $password,
                   'singleSide' => true
                  ]
              ];
              
           return $this->sendRegistrationRequest($options);
            
           $user = User::where('email', $userMail)->first();
           // TODO - send activation request on the fly
           $token = JWTAuth::fromUser($user);
           
           return $this->respondWithToken($token);

        }
    }

    private function sendRegistrationRequest($options)
    {
        $client = new \GuzzleHttp\Client();
        // NOTE - URL DEPENDS ON .ENV !!
        $url = env('APP_URL') . "/api/members";
        $response = $client->request('POST', $url, $options);
        return $response;
    }
    public function facebook(Request $request)
    {
        try {
            
            $data = json_decode($request->getContent(), true);
            $token_to_inspect = $data['accessToken'];
        //get App access token
            $app_token = $this->GetFbAppToken();
        // verify received token
        
        $client = new \GuzzleHttp\Client();
        $response = $client->get('https://graph.facebook.com/debug_token?input_token=' . $token_to_inspect . '&access_token=' . $app_token);
        $body = json_decode($response->getBody()->getContents());
        $valid = $body->data->is_valid;

        if ($valid){
            $userMail = $data['email'];
            $user = User::where('email', $userMail)->first();
            // try to find user, or register new one

            $token = JWTAuth::fromUser($user);
            return $this->respondWithToken($token);
        }
        else return response()->json(
            ['error' => "not valid"], 422
        );
        }
            catch(\HttpResponseException $ex) {
                return response()->json(
                    ['error' => $ex]
                );
            }
    }





    private function GetFbAppToken() {
        return '503390981088653|-voUjASnO7dkAAwGHcVrzswAEUM';
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

            require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/vendor/autoload.php';
            $client_id = env('GOOGLE_APP_ID');
            $google = new Google\Client(['client_id' => $client_id]);
            $data = json_decode($request->getContent());
            $id_token = $data->qc->id_token;
            $google->revokeToken($id_token);

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
