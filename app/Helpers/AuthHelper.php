<?php

namespace App\Helpers;

use Google;
use App\Models\Doctor;
use App\Models\Member;
use App\Models\User;
use App\Types\UserRole;
use App\Types\UserState;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\HelperController;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;


class AuthHelper
{
    public function GoogleAuth(Request $request)
    {
        $data = json_decode($request->getContent());
            
            require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/vendor/autoload.php';
            
            $client_id = env('GOOGLE_APP_ID');
            $google = new Google\Client(['client_id' => $client_id]);
            
            // get json from received request
            // get id token from request json
            $id_token = $data->tokenId;
            
            //verify ID token
            return $google->verifyIdToken($id_token);
    }

    public function FacebookAuth(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $client = new \GuzzleHttp\Client();
        $token_to_inspect = $data['accessToken'];
        //get App access token
        $app_token = $this->GetFbAppToken();
        // verify received token
        
        $response = $client->get('https://graph.facebook.com/debug_token?input_token=' . $token_to_inspect . '&access_token=' . $app_token);
        $body = json_decode($response->getBody()->getContents());
        return $body->data->is_valid;
    }
    private function GetFbAppToken() {
        return '503390981088653|-voUjASnO7dkAAwGHcVrzswAEUM';
    }
    protected function validateRegistration($input)
    {
        // get data from json
        // prepare validator
        $validator = Validator::make((array) $input, [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'gdpr' => 'required',
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json(
                    ['errors' => $validator->errors()],
                    JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                )
            );
        }

        return $input;
    }

    protected function createUser(object $data)
    {
        try {
            $activated = null;

            // verify that SSA ID does not already exist
            $google_id = null;
            $facebook_id = null;
            if (isset($data->singleSide) && $data->singleSide) $activated = date('Y-m-d H:i:s');
            if (isset($data->google_id)) $google_id = $data->google_id;
            if (isset($data->facebook_id)) $facebook_id = $data->facebook_id;
            return User::create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => Hash::make(trim($data->password)),
                'role_id' => UserRole::MEMBER,
                'email_verified_at' => $activated,
                'activated_at' => $activated,
                'google_id' => $google_id,
                'facebook_id' => $facebook_id
            ]);
        } catch (\Exception $ex) {
            throw new HttpResponseException(
                response()->json(
                    ['errors' => "Error creating user: " . $ex->getMessage()],
                    JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                )
            );
        }
    }
    protected function getSlug(string $name)
    {
        $slug = strtolower(
            str_replace(
                " ",
                "-",
                preg_replace(
                    "/[^A-Za-z0-9 ]/",
                    '',
                    HelperController::replaceAccents($name)
                )
            )
        );
        $existingCount = Member::where('slug', 'like', $slug . '%')->count();
        if ($existingCount > 0) {
            $slug = $slug . '-' . $existingCount;
        }
        return $slug;
    }
}