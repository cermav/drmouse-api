<?php

namespace app\Http\Controllers\API;

use Illuminate\Http\Request;
use JWTAuth;
use App\Models\Pet;
use App\Models\Member;
use App\Models\DoctorsLog;
use App\Models\ScoreItem;
use App\Models\User;
use App\Models\Doctor;
use App\Http\Controllers\HelperController;
use App\Types\DoctorStatus;
use App\Types\UserRole;
use App\Types\UserState;
use App\Utils\ImageHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use DateTime;

class PetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //GET all Pets as administrator
    //done and working
    public function showAll()
    {
        $loggedUser = Auth::User();
        if ($loggedUser->role_id === UserRole::ADMINISTRATOR) {
            $Pets = Pets::all();
            return response()->json($Pets);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    //GET Pets list
    public function index()
    {
        $pets = DB::table('pets')
            ->where('owners_id', Auth::user()->id)
            ->get();
        return response()->json($pets);
    }
    //GET Pets detail
    public function detail($id)
    {
        // get pet by id
        $pet = DB::table('pets')->where('id', $id);
        //authorize owners_id vs logged in user
        $this->AuthUser($pet->first()->owners_id);
        //set new latest pet on visit
        DB::table('users')
            ->where('id', $pet->first()->owners_id)
            ->update(['last_pet' => $id]);

        return response()->json($pet->first());
    }
    public function latest()
    {
        return response()->json(
            DB::table('users')
                ->where('id', Auth::user()->id)
                ->first()->last_pet
        );
    }
    //create pet for POST pet
    public function createpet(object $data)
    {
        $date = DateTime::createFromFormat('j. n. Y', $data->birth_date);
        return Pet::create([
            'owners_id' => Auth::user()->id,
            'pet_name' => $data->pet_name,
            'birth_date' => $date,
            'kind' => $data->kind,
            'breed' => $data->breed,
            'gender_state_id' => $data->gender_state_id,
            'chip_number' => $data->chip_number,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // POST pet
    //done and working
    public function store(Request $request)
    {
        // validate input
        $input = $this->validateNewPet($request);

        $pet = $this->createpet($input);

        $pet->save();

        //get created pet id
        $ids = DB::table('pets')
            ->where('owners_id', Auth::user()->id)
            ->pluck('id')
            ->toArray();
        $temp = $ids[0];
        foreach ($ids as $id) {
            if ($id > $temp) {
                $temp = $id;
            }
        }
        //set new pet as latest
        DB::table('users')
            ->where('id', Auth::user()->id)
            ->update(['last_pet' => $temp]);

        return response()->json($pet, JsonResponse::HTTP_CREATED);
    }
    // DEL remove pet
    public function remove(int $id)
    {
        $this->AuthPet($id);
        $user_id = Auth::user()->id;
        Pet::where('id', $id)
            ->where('owners_id', $user_id)
            ->delete();
        $last = Pet::where('owners_id', $user_id)->first()->id;
        User::where('id', $user_id)->update([
            'last_pet' => $last,
        ]);
        return response()->json("Deleted", JsonResponse::HTTP_OK);
    }
    // PUT Update pet
    //done and working
    public function update(Request $request, int $id)
    {
        $this->AuthPet($id);
        // get data from json
        $input = json_decode($request->getContent());
        // prepare validator
        $validator = Validator::make((array) $input, [
            'pet_name' => 'required|string|max:50',
            'birth_date' => 'required',
            'kind' => 'required|string|max:50',
            'breed' => 'required|string|max:50',
            'gender_state_id' => 'required|int',
            'chip_number' => 'nullable|int|max_length:20',
        ]);
        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json(
                    ['errors' => $validator->errors()],
                    JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                )
            );
        }
        $date = DateTime::createFromFormat('j. n. Y', $input->birth_date);
        Pet::where('id', $id)->update([
            'pet_name' => $input->pet_name,
            'birth_date' => $date,
            'kind' => $input->kind,
            'breed' => $input->breed,
            'gender_state_id' => $input->gender_state_id,
            'chip_number' => $input->chip_number,
        ]);

        return response()->json(Pet::find($id), JsonResponse::HTTP_OK);
    } //done and working
    protected function saveAvatar($pet_id, $data)
    {
        // get pet info
        $pet = Pet::where('id', $pet_id)->first();

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
    protected function saveBackground($pet_id, $data)
    {
        // get doctor info
        $pet = Pet::where('id', $pet_id)->first();

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
    protected function validateNewPet(Request $request)
    {
        // get data from json
        $input = json_decode($request->getContent());
        // prepare validator
        //done and working
        $validator = Validator::make((array) $input, [
            //'owners_id' => 'required',
            'pet_name' => 'required',
            'birth_date' => 'required',
            'kind' => 'required',
            'breed' => 'required',
            'gender_state_id' => 'required',
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
    } //done and working
    public function AuthPet(int $pet_id)
    {
        $owners_id = Pet::where('id', $pet_id)->first()->owners_id;
        $loggedUser = Auth::User();
        if (
            $owners_id === $loggedUser->id ||
            $loggedUser->role_id === UserRole::ADMINISTRATOR
        ) {
            //logged user is authorized
            return;
        } else {
            // return unauthorized
            throw new AuthenticationException();
        }
    } //done and working
    public function AuthUser(int $id)
    {
        $requestUser = User::Find($id);
        $loggedUser = Auth::User();

        if (
            $requestUser->id === $loggedUser->id ||
            $loggedUser->role_id === UserRole::ADMINISTRATOR
        ) {
            //logged user is authorized
            return;
        } else {
            // return unauthorized
            throw new AuthenticationException();
        }
    }
    public function avatar(Request $request, int $pet_id)
    {
        $this->AuthPet($pet_id);
        $input = json_decode($request->getContent());
        if ($input->avatar !== null) {
            Pet::where('id', $pet_id)->update([
                'avatar' => $this->saveAvatar($pet_id, $input->avatar),
            ]);
        }
        return response()->json(
            Pet::where('id', $pet_id)->first()->avatar,
            JsonResponse::HTTP_OK
        );
    }
    public function background(Request $request, int $pet_id)
    {
        $this->AuthPet($pet_id);
        $input = json_decode($request->getContent());
        if ($input->background !== null) {
            Pet::where('id', $pet_id)->update([
                'background' => $this->saveBackground(
                    $pet_id,
                    $input->background
                ),
            ]);
        }
        return response()->json(
            Pet::where('id', $pet_id)->first()->background,
            JsonResponse::HTTP_OK
        );
    }
    // favorite vets
    public function addVet(int $pet_id, int $vet_id)
    {
        $this->AuthPet($pet_id);
        $owners_id = Pet::where('id', $pet_id)->first()->owners_id;

        favorite_vets::where('owners_id', $owners_id)
            ->where('vet_id', $vet_id)
            ->firstOrCreate([
                'owners_id' => $owners_id,
                'vet_id' => $vet_id,
            ]);
        return response()->json(
            favorite_vets::where('owners_id', $owners_id)
                ->where('vet_id', $vet_id)
                ->first(),
            JsonResponse::HTTP_OK
        );
    }
    public function getVet(int $pet_id)
    {
        $this->AuthPet($pet_id);

        return response()->json(
            DB::table('favorite_vets')
                ->where(
                    'owners_id',
                    Pet::where('id', $pet_id)->first()->owners_id
                )
                ->get()
        );
    }
    public function deleteVet(int $pet_id, int $vet_id)
    {
        $this->AuthPet($pet_id);
        $owners_id = Pet::where('id', $pet_id)->first()->owners_id;
        DB::table('favorite_vets')
            ->where('owners_id', $owners_id)
            ->where('vet_id', $vet_id)
            ->delete();
        return response()->json("Deleted", JsonResponse::HTTP_OK);
    }
}
