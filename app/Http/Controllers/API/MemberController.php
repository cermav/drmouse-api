<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\HelperController;
use App\Models\Member;
use App\Types\DoctorStatus;
use App\Types\UserRole;
use App\Types\UserState;
use App\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validate input
        $input = $this->validateRegistration($request);

        // Create user
        $user = $this->createUser($input);

        // Create doctor
        $member = Member::create([
            'user_id' => $user->id,
            'state_id' => UserState::NEW,
            'description' => "",
            'slug' => $this->getSlug($input->name),
            'gdpr_agreed' => true,
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

        // send registration email
        $user->sendEmailVerificationNotification();

        return response()->json($member, JsonResponse::HTTP_CREATED);
    }

    /**
     * Validate Input
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateRegistration(Request $request)
    {
        // get data from json
        $input = json_decode($request->getContent());
        // prepare validator
        $validator = Validator::make((array) $input, [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'gdpr' => 'required'
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }

        return $input;
    }

    /**
     * Create user
     * @param array $data
     * @return User
     */
    protected function createUser(object $data)
    {
        try{
            return User::create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => Hash::make(trim($data->password)),
                'role_id' => UserRole::MEMBER
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

    /**
     * Create slug - if already exists, add the number at the end
     * @param string $name
     * @return string
     */
    protected function getSlug(string $name)
    {
        $slug = strtolower(str_replace(" ", "-", preg_replace("/[^A-Za-z0-9 ]/", '', HelperController::replaceAccents($name))));
        $existingCount = Member::where('slug', 'like', $slug . '%')->count();
        if ($existingCount > 0) {
            $slug = $slug . '-' . ($existingCount);
        }
        return $slug;
    }
}
