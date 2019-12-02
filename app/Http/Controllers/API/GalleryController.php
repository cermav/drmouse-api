<?php

namespace App\Http\Controllers\Api;

use App\Doctor;
use App\Http\Resources\PhotoResource;
use App\Models\Photo;
use App\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    /**
     * Change user password
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        // verify user
        $requestUser = User::find($id);
        $loggedUser = Auth::User();

        if ($requestUser->id === $loggedUser->id || $loggedUser->role_id === UserRole::ADMINISTRATOR) {

            // validate input
            $input = json_decode($request->getContent());

            // get doctor info
            $doctor = Doctor::where('user_id', $requestUser->id)->first();
            $result = [];

            foreach ($input as $picture) {

                // prepare file name
                $fileName = strtolower($doctor->slug . '-' . Str::random(4) .  '-' . $picture->name);

                // save file to local storage
                Storage::disk('public')->put('doctors' . DIRECTORY_SEPARATOR . $fileName, base64_decode($picture->content));

                $position = intval(Photo::where('user_id', $requestUser->id)->max('position')) + 1;

                // store it in database
                $result[] = Photo::create([
                    'user_id' => $requestUser->id,
                    'path' => 'doctors/' . $fileName,
                    'position' => $position
                ]);
            }

            return response()->json(PhotoResource::collection(Photo::where('user_id', $requestUser->id)->get()), JsonResponse::HTTP_OK);

        } else {
            // return unauthorized
            throw new AuthenticationException();
        }
    }
}
