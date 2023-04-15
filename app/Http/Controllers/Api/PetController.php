<?php /** @noinspection ALL */

namespace app\Http\Controllers\API;

use App\Helpers\AuthHelper;
use App\Http\Controllers\Controller;
use App\Models\FavoriteVet;
use App\Models\Pet;
use App\Models\Record;
use App\Models\RecordFile;
use App\Models\User;
use App\Types\DoctorStatus;
use App\Types\UserRole;
use App\Utils\ImageHandler;
use DateTime;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class PetController extends Controller {
    /**
     * Display a listing of pets.
     *
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function index(): JsonResponse {
        $pets = DB::table('pets')
            ->where('owners_id', Auth::user()->id)
            ->get();
        return response()->json($pets);
    }

    /**
     * @throws AuthenticationException
     */
    public function detail(int $pet_id): JsonResponse {
        $pet = $this->authorizeUser($pet_id);
        $user = User::find($pet->owners_id);

        if ($user->last_pet != $pet_id) {
            $user->update(['last_pet' => $pet_id]);
        }

        return response()->json($pet);
    }

    /**
     * @return JsonResponse
     */
    public function latest(): JsonResponse {
        $last_pet = User::where('id', Auth::user()->id)->first()->last_pet;

        if ($last_pet === 0) {
            return response()->json($last_pet);
        }

        try {
            $list = Pet::where('owners_id', Auth::user()->id)
                ->pluck('id')
                ->toArray();
            $temp = $list[0];
            foreach ($list as $id) {
                if ($last_pet == $id) {
                    return response()->json($last_pet);
                } elseif ($id > $temp) {
                    $temp = $id;
                }
            }
            return response()->json($temp);
        } catch (Exception $ex) {
            User::where('id', Auth::User()->id)->update([
                'last_pet' => 0,
            ]);
            return response()->json(
                ['error' => ["No existing pet found"]],
                Response::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request) {
        $input = $this->validatePet($request);

        $pet = $this->createPet($input);

        $pet->save();

        $ids = Pet::where('owners_id', Auth::user()->id)
            ->pluck('id')
            ->toArray();
        $temp = $ids[0];
        foreach ($ids as $id) {
            if ($id > $temp) {
                $temp = $id;
            }
        }
        //set new pet as latest
        User::where('id', Auth::user()->id)
            ->update(['last_pet' => $temp]);

        return response()->json($temp, Response::HTTP_CREATED);
    }

    /**
     * @throws AuthenticationException
     */
    public function remove(int $id): JsonResponse {
        $pet = $this->authorizeUser($id);
        $owner_id = Auth::user()->id;

        $pet->delete();
        try {
            $ids = DB::table('pets')
                ->where('owners_id', $owner_id)
                ->pluck('id')
                ->toArray();
            $temp = $ids[0];
            foreach ($ids as $id) {
                if ($id > $temp) {
                    $temp = $id;
                }
            }
            User::where('id', $owner_id)->update([
                'last_pet' => $temp,
            ]);
            return response()->json($temp, Response::HTTP_OK);
        } catch (Exception $ex) {
            User::where('id', Auth::User())->update([
                'last_pet' => 0,
            ]);
            return response()->json(
                ['error' => ['location' => $ex->getMessage()]],
                Response::HTTP_NOT_FOUND
            );
        }
    }

    public function update(Request $request, int $id): JsonResponse {
        $pet = $this->authorizeUser($id);

        $input = json_decode($request->getContent());
        $this->validatePet($request);

        $pet->update([
            'pet_name' => $input->pet_name,
            'birth_date' => DateTime::createFromFormat('j. n. Y', $input->birth_date),
            'kind' => $input->kind,
            'breed' => $input->breed,
            'gender_state_id' => $input->gender_state_id,
            'chip_number' => $input->chip_number,
        ]);

        return response()->json($pet, Response::HTTP_OK);
    }

    protected function saveAvatar($pet_id, $data): string {
        $pet = $this->authorizeUser($pet_id);

        // split image data
        $image = ImageHandler::splitEncodedData($data);
        // prepare file name
        $fileName = strtolower(
            $pet->owners_id .
            '_' .
            $pet->id .
            'av.' .
            ImageHandler::getExtensionByType($image->type)
        );
        //remove previous file
        if (Storage::disk('public')->exists($fileName)) {
            Storage::disk('public')->delete($fileName);
        }
        // save file to local storage
        Storage::disk('public')->put(
            'pet_avatar' . DIRECTORY_SEPARATOR . $fileName,
            base64_decode($image->content)
        );
        return $fileName;
    }

    private function createPet($data) {
        return Pet::create([
            'owners_id' => Auth::user()->id,
            'pet_name' => $data->pet_name,
            'birth_date' => DateTime::createFromFormat('j. n. Y', $data->birth_date),
            'kind' => $data->kind,
            'breed' => $data->breed,
            'gender_state_id' => $data->gender_state_id,
            'chip_number' => $data->chip_number,
        ]);
    }

    protected function saveBackground($pet_id, $data): string {
        // get doctor info
        $pet = $this->authorizeUser($pet_id);

        // split image data
        $image = ImageHandler::splitEncodedData($data);

        // prepare file name
        $fileName = strtolower(
            $pet->owners_id .
            '_' .
            $pet->id .
            'background.' .
            ImageHandler::getExtensionByType($image->type)
        );
        //remove previous file
        if (Storage::disk('public')->exists($fileName)) {
            Storage::disk('public')->delete($fileName);
        }
        // save file to local storage
        Storage::disk('public')->put(
            'pet_background' . DIRECTORY_SEPARATOR . $fileName,
            base64_decode($image->content)
        );
        return $fileName;
    }

    protected function validatePet(Request $request) {
        $input = json_decode($request->getContent());

        $validator = Validator::make((array)$input, [
            //'owners_id' => 'required',
            'pet_name' => 'required',
            'birth_date' => 'required',
            'kind' => 'required',
            'breed' => 'required',
            'gender_state_id' => 'required',


            //            'pet_name' => 'required|string|max:50',
            //            'birth_date' => 'required',
            //            'kind' => 'required|string|max:50',
            //            'breed' => 'required|string|max:50',
            //            'gender_state_id' => 'required|int',
            //            'chip_number' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json(
                    ['errors' => $validator->errors()],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                )
            );
        }

        return $input;
    }

    public function avatar(Request $request, int $pet_id): JsonResponse {
        $pet = $this->authorizeUser($pet_id);

        $input = json_decode($request->getContent());
        if ($input->avatar !== null) {
            $pet->update([
                'avatar' => $this->saveAvatar($pet_id, $input->avatar),
            ]);
        }
        return response()->json(
            $pet->first()->avatar,
            Response::HTTP_OK
        );
    }

    public function background(Request $request, int $pet_id): JsonResponse {
        $pet = $this->authorizeUser($pet_id);

        $input = json_decode($request->getContent());
        if ($input->background !== null) {
            $pet->update([
                'background' => $this->saveBackground(
                    $pet_id,
                    $input->background
                ),
            ]);
        }
        return response()->json(
            $pet->first()->background,
            Response::HTTP_OK
        );
    }

    private function authorizeUser(int $pet_id): Pet {
        $pet = Pet::findOrFail($pet_id);
        AuthHelper::authorizeUser($pet->owners_id);

        return $pet;
    }
}
