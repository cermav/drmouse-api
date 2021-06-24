<?php

namespace App\Helpers;

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


class RegistrationHelper
{
    public function store($data)
    {
        //return response()->json($request);
        // validate input
        
        $input = $this->validateRegistration($data);
        

        // Create user
        $user = $this->createUser($input);

        $verified = null;
        if (isset($input->singleSide) && $input->singleSide) $verified = date('Y-m-d H:i:s');

        // Create doctor
        $member = Member::create([
            'user_id' => $user->id,
            'state_id' => UserState::NEW,
            'description' => "",
            'slug' => $this->getSlug($input->name),
            'gdpr_agreed' => 1,
            'gdpr_agreed_date' => date('Y-m-d H:i:s')
        ]);

        // TODO: predelat
        if (!empty($input->profile_image)) {
            $base64File = $input->profile_image;
            $encodedImgString = explode(',', $base64File, 2)[1];
            $decodedImgString = base64_decode($encodedImgString);
            $info = getimagesizefromstring($decodedImgString);
            $ext = explode('/', $info['mime']);
            @list($type, $file_data) = explode(';', $base64File);
            @list(, $file_data) = explode(',', $file_data);
            $imageName = 'profile_' . time() . '.' . $ext[1];
            $imagePath = 'users/profiles/' . $user->id . '/' . $imageName;
            Storage::disk('public')->put($imagePath, base64_decode($file_data));
            $user->avatar = $imagePath;
            $user->save();
        }

        $member->save();

        $singleSide = $data->singleSide;
        if (!isset($singleSide) || (isset($singleSide) && $singleSide != true)) $user->sendMemberRegistrationEmailNotification();

        return response()->json($member, JsonResponse::HTTP_CREATED);
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