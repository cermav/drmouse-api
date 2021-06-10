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
use Google;

use Jose\Component\Core\JWK;
use Jose\Easy\Build;
use Jose\Easy\Load;
use Jose\Component\KeyManagement\JWKFactory;

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
    require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/vendor/autoload.php';


    $data = json_decode($request->getContent());
    
    $access_token = $data->qc->id_token;

    $jwk = JWKFactory::createFromSecret(
        'gMgRiejAi61op900bOoICxQu',       // The shared secret
        [                      // Optional additional members
            'alg' => 'RS256',
            'use' => 'sig'
        ]
    );

    $jwt = Load::jws($access_token)->key($jwk)->run();
    return response()->json($jwt);
    
    $client = new \GuzzleHttp\Client();
    //$client = new Google\Client();
    //$client->setAuthConfig(dirname($_SERVER['DOCUMENT_ROOT']) . '/client_credentials.json');

    //$response = $client->get('https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=' . $id_token);
    
    //$client->authenticate($request->getContent());
    //$access_token = $client->getAccessToken();
    return response()->json($response, 200);
        /*
        consume 3rd party API response
        re-confirm received data with 3rd party API
        check for existing user account
        create account from consumed data if no account exists
        // handle missing data
        respond with Dr.Mouse bearer token
        */
        try {
            $token = $request->header('Authorization');
            $userMail = $data->profileObj->email;
            return response()->json(['token' => $token, 'email' => $userMail], 200);
        }
            catch(\HttpResponseException $ex) {
                return response()->json(
                    ['error' => $ex]
                );
            }
    }
    public function facebook(Request $request)
    {
        /*
        consume 3rd party API response
        re-confirm received data with 3rd party API
        check for existing user account
        create account from consumed data if no account exists
        // handle missing data
        respond with Dr.Mouse bearer token
        */
        try {
            //$header = $request->header('Authorization');
            //json_encode($header);
            
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
        //$client = new \GuzzleHttp\Client();
        //$App_Id = 503390981088653;
        //$App_Secret = '991146ba1547cc5ed6211edb4ed3a4f0';
        //$request = $client->post('https://graph.facebook.com/oauth/access_token?client_id=' . $App_Id . '&client_secret=' . $App_Secret . '&grant_type=client_credentials&redirect_uri=https://drmouse.dev.code8.link&fb_exchange_token=' . $user_token);
        //var_dump($request);
        //$response = $request->getBody();
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
