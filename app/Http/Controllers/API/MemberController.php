<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\HelperController;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use App\Types\DoctorStatus;
use App\Types\MemberStatus;
use App\Types\UserRole;
use App\Types\UserState;
use App\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $member = Member::where('user_id', $id)
            ->select(
                'members.*'
            )
            ->whereIn('state_id', [MemberStatus::NEW, MemberStatus::VALID, MemberStatus::BLOCKED])->get();
        if (sizeof($member) > 0) {
            return MemberResource::collection($member)->first();
        }
        return response()->json(['message' => 'Not Found!'], JsonResponse::HTTP_NOT_FOUND);
    }

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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        // verify user
        $requestUser = User::find($id);
        $loggedUser = Auth::User();

        if ($requestUser->id === $loggedUser->id || $loggedUser->role_id === UserRole::ADMINISTRATOR) {

            // validate input
            $data = $this->validateProfile($request, $id);

            // TODO : add validation
            $user = User::find($id);
            $user->update($data['user']);

            // store image
            if ($data['avatar'] !== null) {
                $user->update(['avatar' => $this->saveProfileImage($id, $data['avatar'])]);
            }

            $member = Member::where(['user_id' => $id])->get()->first();

            return response()->json(MemberResource::make($member), JsonResponse::HTTP_OK);

        } else {
            // return unauthorized
            throw new AuthenticationException();
        }
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
     * Validate Input
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateProfile(Request $request, int $user_id)
    {
        // get data from json
        $input = json_decode($request->getContent());
        // prepare validator
        $validator = Validator::make((array) $input, [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,'.$user_id.',id',
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }

        $data = [
            'user' => [
                'name' => $input->name,
                'email' => $input->email
            ],
            'avatar' => $input->avatar
        ];
        return $data;
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

    protected function saveProfileImage($user_id, $data)
    {
        // get doctor info
        $member = Member::where('user_id', $user_id)->first();

        // split image data
        $image = ImageHandler::splitEncodedData($data);

        // prepare file name
        $fileName = strtolower($member->slug . '.' . ImageHandler::getExtensionByType($image->type));

        // save file to local storage
        Storage::disk('public')->put('member' . DIRECTORY_SEPARATOR . $fileName, base64_decode($image->content));

        return $fileName;
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
