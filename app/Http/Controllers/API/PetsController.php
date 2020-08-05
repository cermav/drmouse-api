<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Pets;
use App\DoctorsLog;
use App\Http\Controllers\HelperController;
use App\ScoreItem;
use App\Types\DoctorStatus;
use App\Types\UserRole;
use App\Types\UserState;
use App\User;
use App\Utils\ImageHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Doctor;
use App\Http\Resources\DoctorResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;


class PetsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //GET pets list
     public function showAll()
    {
        $Pets = Pets::all();
        return response()->json($Pets);
    }
    //GET pets detail
    public function showById($id)
    {
        $pet = Pets::find($id);

        return response()->json($pet);
    }
            
            

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    //create pet for POST pet
     public function createpet(object $data)
    {
        return Pets::create([
        'owners_id' => $data->owners_id,
        'pet_name' => $data->pet_name,
        'birth_date' => $data->birth_date,
        'kind' => $data->kind,
        'breed' => $data->breed,
        'gender' => $data->gender,
        'chip_number' => $data->chip_number,
        'bg' => $data->bg,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // POST pet
    public function store(Request $request)
    {
        /*$request->validate([
        'owners_id'=>'required',
        'pet_name'=>'required',
        'birth_date'=>'required',
        'kind'=>'required',
        'breed'=>'required',
        'gender'=>'required',
        'chip_number'=>'required',
        'bg'=>'required',
        'profile_completedness'=>'required',
        ]);*/

        // validate input
        $input = $this->validateRegistration($request);
        
        $pet = $this->createpet($input);


            /*
        $Pets = new Pets ([
            'owners_id'=> $request->get('owners_id'),
            'pet_name'=> $request->get('pet_name'),
            'birth_date'=> $request->get('birth_date'),
            'kind'=> $request->get('kind'),
            'breed'=> $request->get('breed'),
            'gender'=> $request->get('gender'),
            'chip_number'=> $request->get('chip_number'),
            'bg'=> $request->get('bg'),
            'profile_completedness'=> $request->get('profile_completedness'),
        ]);
        $Pets->save();
        return redirect('/pets')->with('success','Pets saved!');
    }*/
    
        
        $pet->save();

        /* Create a record in log table
        DoctorsLog::create([
            'user_id' => $user->id,
            'state_id' => UserState::NEW,
            'email_sent' => true,
            'doctor_object' => serialize($doctor)
        ]);*/

        return response()->json($pet, JsonResponse::HTTP_CREATED);
    }
 

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $Pets = Pets::find($id);
        return view('Pets.edit', compact('Pets'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    protected function validateRegistration(Request $request)
    {
        // get data from json
        $input = json_decode($request->getContent());
        // prepare validator
        $validator = Validator::make((array) $input, [
            'owners_id' => 'required',
            'pet_name' => 'required',
            'birth_date' => 'required',
            'kind' => 'required',
            'breed' => 'required',
            'gender' => 'required',
            'chip_number' => 'required',
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }

        return $input;
    }
}
