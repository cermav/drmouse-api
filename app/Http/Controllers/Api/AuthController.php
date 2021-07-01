<?php
namespace app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\MemberController;
use App\Types\DoctorStatus;
use App\Types\UserRole;
use App\Models\User;
use App\Models\Member;
use App\Models\Doctor;
use App\Helpers\RegistrationHelper;
use App\Helpers\AuthHelper;
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
        try {
            $payload = (new AuthHelper)->GoogleAuth($request);
            if ($payload) {
                $userMail = $payload['email'];
                $userid = $payload['sub'];
                $emailVerified = $payload['email_verified'];
            } else {
                return response()->json(['error' => 'invalid token'], 401);
            }
            
            $user = null;
            $userByMail = null;
            $user = User::where('google_id', $userid)->first();

            // user connected with google account
            if ($user){
                $token = JWTAuth::fromUser($user);
            
                return $this->respondWithToken($token);
            }
           
            // google id not found
            if (!$user){
                $userByMail = User::where('email', $userMail)->first();
            }

            if ($userByMail) {
                $userByMail->update(['google_id' => $userid]);
                if ($userByMail->gdpr_agreed == 0)
                {
                    $userByMail->update(['gdpr_agreed' => '1']);
                }
                // předělat - user typ, podle toho hledat member/doctor
                $roleID = $userByMail->role_id;
                if ($roleID == UserRole::DOCTOR){
                    $doctor = Doctor::where('user_id', $userByMail->id)->first();
                    if ($doctor && $doctor->gdpr_agreed == 0)
                    {
                        $doctor->update(['gdpr_agreed' => 1, 'gdpr_agreed_date' => date('Y-m-d H:i:s')]);
                    }
                }
                if ($roleID == UserRole::MEMBER){
                    $member = Member::where('user_id', $userByMail->id)->first();
                    if ($member && $member->gdpr_agreed == 0)
                    {
                        $member->update(['gdpr_agreed' => 1, 'gdpr_agreed_date' => date('Y-m-d H:i:s')]);
                    }
                }
                

                $token = JWTAuth::fromUser($userByMail);
                
                return $this->respondWithToken($token);
            }
            // user not registered
            else {
            $profile = $data->profileObj;

            $password = bin2hex(random_bytes(16));
            $options = (object) [
                    'name' => "$profile->givenName $profile->familyName", 
                    'email' => $profile->email,
                    'gdpr' => true,
                    'password' => $password,
                    'google_id' => $userid,
                    'singleSide' => true
                ];
                
            $this->sendRegistrationRequest($options);
            
            $user = User::where('google_id', $userid)->first();
            // TODO - send activation request on the fly
            $token = JWTAuth::fromUser($user);
            
            return $this->respondWithToken($token);

            }
        }
        catch(\HttpResponseException $ex) {
            return response()->json(
                ['error' => $ex]
            );
        }
    }

    //turn back to private
    private function sendRegistrationRequest($options)
    {
        return (new RegistrationHelper)->store($options);
    }

    public function facebook(Request $request)
    {
        try {
        $data = json_decode($request->getContent(), true);
        $valid = (new AuthHelper)->FacebookAuth($request);
        if ($valid){

            // user connected with google account
            if ($user = User::where('facebook_id', $data['id'])->first()){
                $token = JWTAuth::fromUser($user);
            
                return $this->respondWithToken($token);
            }

            // try to find user, or register new one
            if ($user = User::where('email', $data['email'])->first()) {
                $user->update(['facebook_id' => $data['id']]);
                if ($user->gdpr_agreed == 0)
                {
                    $user->update(['gdpr_agreed' => '1']);
                }
                // předělat - user typ, podle toho hledat member/doctor
                $roleID = $user->role_id;
                if ($roleID == UserRole::DOCTOR){
                    $doctor = Doctor::where('user_id', $user->id)->first();
                    if ($doctor && $doctor->gdpr_agreed == 0)
                    {
                        $doctor->update(['gdpr_agreed' => 1, 'gdpr_agreed_date' => date('Y-m-d H:i:s')]);
                    }
                }
                if ($roleID == UserRole::MEMBER){
                    $member = Member::where('user_id', $user->id)->first();
                    if ($member && $member->gdpr_agreed == 0)
                    {
                        $member->update(['gdpr_agreed' => 1, 'gdpr_agreed_date' => date('Y-m-d H:i:s')]);
                    }
                }
                

                $token = JWTAuth::fromUser($user);
                
                return $this->respondWithToken($token);
            }


            else {
                // change
               $options = (object) [
                'name' => $data['name'],
                'email' => $data['email'],
                'gdpr' => true,
                'password' => bin2hex(random_bytes(16)),
                'facebook_id' => $data['id'],
                'singleSide' => true
            ];
                  
            $this->sendRegistrationRequest($options);
                
            $user = User::where('facebook_id', $data['id'])->first();
            $token = JWTAuth::fromUser($user);
               
            return $this->respondWithToken($token);
            }}
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
    
    public function googleLink(Request $request)
    {
        try {
        $loggedUser = Auth::User();
        $payload = (new AuthHelper)->GoogleAuth($request);
            if ($payload) {
                $userid = $payload['sub'];
            } else {
                return response()->json(['error' => 'invalid token'], 401);
            }
            
            $user = User::where('google_id', $userid)->first();
            // user connected with google account
            if ($user && isset($user->google_id)){
                return response()->json(["message" => "Ucet je jiz sparovany"], HTTP_CONFLICT);
            }
            else User::where('id', $loggedUser->id)->update(['google_id' => $userid]);
            return response()->json("Ucet uspesne sparovany.", 200);
        }
        catch(\HttpResponseException $ex) {
            return response()->json(
                ['error' => $ex]
            );
        }
    }
    public function googleUnlink(Request $request)
    {
        try {
        $loggedUser = Auth::User();
        $payload = (new AuthHelper)->GoogleAuth($request);
            if ($payload) {
                $userid = $payload['sub'];
            } else {
                return response()->json(['error' => 'invalid token'], 401);
            }
            
            $user = User::where('google_id', $userid)->first();
            // user connected with google account
            if ($user && isset($user->google_id)){
                $user->update(['google_id' => null]);
                return response()->json("Ucet uspesne sparovany.", 200);
            }
            else return response()->json(["message" => "Tento ucet neni sparovany"], HTTP_CONFLICT);
        }
        catch(\HttpResponseException $ex) {
            return response()->json(
                ['error' => $ex]
            );
        }
    }
    public function facebookLink(Request $request)
    {
        try {
        $loggedUser = Auth::User();
        $data = json_decode($request->getContent(), true);
        $valid = (new AuthHelper)->FacebookAuth($request);
        if ($valid){
            // user connected with google account
            $user = User::where('facebook_id', $data['id'])->first();
            if ($user){
                return response()->json(["message" => "Ucet je jiz sparovany"], HTTP_CONFLICT);
            }
            else User::where('id', $loggedUser->id)->update(['facebook_id' => $data['id']]);
            return response()->json("Ucet uspesne sparovany.", 200);
        }
    }
            catch(\HttpResponseException $ex) {
                return response()->json(
                    ['error' => $ex]
                );
            }
        
    }

    public function facebookUnlink(Request $request)
    {
        try {
        $loggedUser = Auth::User();
        $data = json_decode($request->getContent(), true);
        $valid = (new AuthHelper)->FacebookAuth($request);
        if ($valid){
            // user connected with google account
            $user = User::where('facebook_id', $data['id'])->first();
            if ($user){
                $user->update(['facebook_id' => null]);
                return response()->json("Sparovani uspesne odstraneno.", 200);
            }
            else return response()->json(["message" => "Tento ucet neni sparovany"], HTTP_CONFLICT);
        }
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
