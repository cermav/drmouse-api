<?php

namespace app\Http\Controllers\Api;

use App\Http\Resources\ServiceResource;
use App\Models\DoctorsService;
use App\Models\Service;
use App\Types\UserRole;
use App\Models\User;
use App\Validators\ServiceValidator;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $services = Service::where([['is_approved', '=', 1]])
            ->select(['id', 'name', 'show_on_registration', 'show_in_search'])
            ->get();
        return response()->json($services);
    }

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

        if (
            $requestUser->id === $loggedUser->id ||
            $loggedUser->role_id === UserRole::ADMINISTRATOR
        ) {
            // validate input
            $input = json_decode($request->getContent());
            foreach ($input as $item) {
                $validator = ServiceValidator::create((array) $item);
                if ($validator->fails()) {
                    var_dump($item);
                    throw new HttpResponseException(
                        response()->json(
                            ['errors' => $validator->errors()],
                            JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                        )
                    );
                }
            }

            // remove all records
            DoctorsService::where('user_id', $requestUser->id)->delete();

            // save each new record
            foreach ($input as $item) {
                if (property_exists($item, 'price') && $item->price > 0) {
                    DoctorsService::create([
                        'user_id' => $requestUser->id,
                        'service_id' => $item->id,
                        'price' => $item->price,
                    ]);
                }
            }

            return response()->json(
                ServiceResource::collection(
                    $requestUser
                        ->services()
                        ->where('is_approved', 1)
                        ->get()
                ),
                JsonResponse::HTTP_OK
            );
        } else {
            // return unauthorized
            throw new AuthenticationException();
        }
    }
}
