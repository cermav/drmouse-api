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
use Jose\Component\Signature\Algorithm\RS256;

use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Math\BigInteger;
use phpseclib3\Crypt\RSA;

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
    function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
      }
      
      function base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
      }
    public function google(Request $request)
    {
    require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/vendor/autoload.php';
    $client = new \GuzzleHttp\Client();
    $google = new Google\Client();

    $data = json_decode($request->getContent());
    $google->authenticate(json_encode($data));
    $google->setAuthConfig(dirname($_SERVER['DOCUMENT_ROOT']) . '/client_credentials.json');

    $client_id = "1020802082701-snpg5g9rkrgs6nnln90f6g79nh3t3tj1.apps.googleusercontent.com";
    $google->setDeveloperKey($client_id);

    $id_token = $data->qc->id_token;

    
    $ticket = $google->verifyIdToken($id_token);
    print_r($ticket);

    $response = $client->get('https://oauth2.googleapis.com/tokeninfo?id_token=' . $id_token);
    $response = json_decode($response->getBody()->getContents());
    print_r($response);

    $parts = explode('.', $id_token);

    $header = base64_decode($parts[0]);
    $header = json_decode($header);

    $body = base64_decode($parts[1]);
    $body = json_decode($body);
    
    /*
    echo "echo parts[2]";
    echo "\r\n";
    echo $parts[2];
    echo "\r\n";
    echo "\r\n";
    echo "echo base64_encode(parts[2]);";
    echo "\r\n";
    echo base64_encode($parts[2]);
    echo "\r\n";
    echo "\r\n";
    echo "echo base64_decode(parts[2]);";
    echo "\r\n";
    echo base64_decode($parts[2]);
    echo "\r\n";
    echo "\r\n";
    echo "echo this->base64url_encode(parts[2]);";
    echo "\r\n";
    echo $this->base64url_encode($parts[2]);
    echo "\r\n";
    echo "\r\n";
   echo "echo this->base64url_decode(parts[2]);";
    echo $this->base64url_decode($parts[2]);
    */
    //$signature = base64_encode($parts[2]);
    $signature = $parts[2];

    $keys = json_decode(file_get_contents('https://www.googleapis.com/oauth2/v3/certs'));

    $google_key = file_get_contents('https://www.googleapis.com/oauth2/v1/certs');


    $kid = $keys->keys[1]->kid;
    $token_kid = $header->kid;

    if ("accounts.google.com" !== $body->iss) return response()->json(['error' => 'invalid token (iss)'], 422);
    if ($client_id !== $body->aud) return response()->json(['error' => 'invalid token (aud)'], 422);
    if ($kid !== $token_kid) return response()->json(['error' => 'invalid token (kid)'], 422);
    
    $modulus = $keys->keys[1]->n;
    $exponent = $keys->keys[1]->e;
    $alg = $keys->keys[1]->alg;

    $cert ="-----BEGIN CERTIFICATE-----MIIDJjCCAg6gAwIBAgIIVGBFY93ZYokwDQYJKoZIhvcNAQEFBQAwNjE0MDIGA1UEAxMrZmVkZXJhdGVkLXNpZ25vbi5zeXN0ZW0uZ3NlcnZpY2VhY2NvdW50LmNvbTAeFw0yMTA2MTIwNDI5NTVaFw0yMTA2MjgxNjQ0NTVaMDYxNDAyBgNVBAMTK2ZlZGVyYXRlZC1zaWdub24uc3lzdGVtLmdzZXJ2aWNlYWNjb3VudC5jb20wggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDRi52e+K7A11wKhcQzAyUlaHFZimYB5FdDwN/lsJV/nEVUSYvlqb/ZNNZHBF/fi+om6ganJ/dLvMl4m/wjYvK+anDfctF5ESQ5sK3W6nXskbDn930rYx/n0Sec+R3thQaSVTGN7yvEguJOGI90RoXw/mlF575YPaaZBK6DSuo2Uylp1hVoy/dj8cuv3sd6HUAJGh9h+/aGYZKYLqijRI3h3mA/7+CADOD0qjssNVwGDpNYB8kuHfcaky0AjYw+N3pcUmO75H13rwgMIhSj4ITwrSkBmdcZLxpaWf92mNmGUyNeuBjjbdBrhg2yWg9zCRDbSuTxcZgWvQf/0a5YhpZZAgMBAAGjODA2MAwGA1UdEwEB/wQCMAAwDgYDVR0PAQH/BAQDAgeAMBYGA1UdJQEB/wQMMAoGCCsGAQUFBwMCMA0GCSqGSIb3DQEBBQUAA4IBAQCrfG7K0x6L/Y9Sj/Au3GraEX3lPScu5AuW7tP26iYMf69n4m8Vi/UtkiHbZJeOWQ0HNgevq50ke8MHXOMBoHMfcjEsPyxufWRtIsqNWnNCWgbfSTIhk/NLHbZKnSbW+qysLcDNMrFc1XEaMR7i0XTQE8tNPfV9NJSI+scn6Oq/z6Tjdw+iSbqkw8n8+PfSRl0J8hx6gEQoKFagw1Zt/jAApSW6SWKby4VwFHgTVDbPwdMV4VbseKKx66Lb8qGPqTu8TM70nQlIHUnbXccalXGOaQsycaaNWPGpychl1JxUftwbdaW/dY5NVpGEwXJ2DRAJiNK6jDcSsrjOJI4d7ukb-----END CERTIFICATE-----";
    $key = PublicKeyLoader::load($cert);

    $sign = $this->base64url_decode($parts[2]); // inline signature. I'm using SHA512
    $data = $parts[0] . $parts[1]; // 64 charactor for SHA512. It's raw data, not hashed data
    $pubkeyid = openssl_pkey_get_public($cert);
    $ok = openssl_verify(base64_encode($data), base64_encode($parts[2]), $key);
    if($ok==1) $message = "valid";
    if($ok==0) $message = "invalid";
    if($ok==-1) $message = "ugly";
    echo $message;


    $key->verify($parts[1], $signature) ? $message = "valid signature" : $message = "invalid signature";
    return response()->json($message);
    
    //$client = new Google\Client();
    //$client->setAuthConfig(dirname($_SERVER['DOCUMENT_ROOT']) . '/client_credentials.json');

    //$response = $client->get('https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=' . $id_token);
    return response()->json($response, 200);
    
    //$client->authenticate($request->getContent());
    //$access_token = $client->getAccessToken();
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
