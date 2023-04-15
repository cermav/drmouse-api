<?php /** @noinspection ALL */

namespace app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvoiceItem;
use App\Models\PetAppointment;
use App\Models\PriceChart;
use App\Models\Record;
use App\Models\RecordFile;
use App\Models\User;
use App\Types\UserRole;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Support\Facades\DB;

class RecordController extends Controller {
    /**
     * @throws AuthenticationException
     */
    public function index(): JsonResponse {
        $records = Record::where('doctor_id', Auth::user()->id)->get();

        return response()->json($records);
    }

    /**
     * creates record with invoice items
     * @throws AuthenticationException
     */
    public function store(Request $request, int $event_id): JsonResponse {
        $doctor_id = $this->authorizeDoctor();

        $object = json_decode($request->getContent(), false);

        $record = $this->createRecord($object, $doctor_id, $event_id);
        $record->save();

        $this->createInvoiceItems($object, $record, $doctor_id);

        return response()->json(Response::HTTP_CREATED);
    }

    public function update(Request $request) {}

    public function delete(Request $request) {}

    public function createRecord($data, int $doctor_id, int $event_id) {
        try {
            $event = PetAppointment::findOrFail($event_id);

            return Record::create([
                'pet_id' => $event->pet_id ?: null,
                'appointment_id' => $event->id,
                'doctor_id' => $doctor_id,
                'date' => $event->date,
                'time' => $data->time ?: $event->time,
                'medical_record' => $data->medical_record,
                'description' => $data->description
            ]);
        } catch (Exception $ex) {
            throw new HttpResponseException(response()->json(['errors' => "Error creating appointment: " . $ex->getMessage(),], Response::HTTP_UNPROCESSABLE_ENTITY));
        }
    }

    public function getAllRecords(): JsonResponse {
        $id = Auth::user()->id;
        $records = DB::table("pet_records")->join('pet_appointments', 'pet_records.appointment_id', 'pet_appointments.id')
            ->select('pet_appointments.*', 'pet_records.*')
            ->where('pet_appointments.owners_id', $id)
            ->orWhere('pet_records.doctor_id', $id)
            ->get();

        return response()->json($records);
    }

    public function getRecords($pet_id): JsonResponse {
        try {
            $records = Record::where('pet_id', $pet_id)->get();
            $collection = collect([]);
            foreach ($records as $record) {
                $files = RecordFile::where('record_id', $record->id)->get();
                $fileData = collect([]);
                foreach ($files as $file) {
                    $fileCollection = collect([
                        'id' => $file->id,
                        'file_name' => $file->file_name . "." . $file->extension
                    ]);
                    $fileData->push($fileCollection);
                }
                $recordCollection = collect([
                    'id' => $record->id,
                    'date' => $record->date,
                    'description' => $record->description,
                    'notes' => $record->notes,
                    'doctor_id' => $record->doctor_id,
                    'pet_id' => $record->pet_id,
                    'files' => $fileData
                ]);
                $collection->push($recordCollection);
            }
            //return $collection;
            return response()->json($collection, Response::HTTP_OK);
        } catch (\HttpResponseException $ex) {
            return response()->json(
                ['error' => $ex]
            );
        }
    }

    public function add_record(int $pet_id, Request $request): JsonResponse {
        $this->authorizeUser($pet_id);

        $payload = $request->all();
        $validator = Validator::make($payload, [
            'description' => 'required',
            'notes' => 'max:500'
        ]);
        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->errors()],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        $date = DateTime::createFromFormat('j. n. Y', $request->date);
        return Record::create([
            'pet_id' => $pet_id,
            'date' => $date,
            'description' => $request->description,
            'notes' => $request->notes,
            'doctor_id' => $request->doctor_id,
            'appointment_id' => $request->appointment_id
        ]);
    }

    public function update_record($pet_id, $record_id, Request $request): JsonResponse {
        try {
            $this->AuthorizeUser($pet_id);
            $payload = $request->all();
            $validator = Validator::make($payload, [
                'description' => 'required',
                'notes' => 'max:500'
            ]);
            if ($validator->fails()) {
                return response()->json(
                    ['errors' => $validator->errors()],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
            $date = DateTime::createFromFormat('j. n. Y', $request->date);
            Record::where('id', $record_id)->update([
                'description' => $request->description,
                'notes' => $request->notes,
                'date' => $date,
                'doctor_id' => $request->doctor_id,
                'appointment_id' => $request->appointment_id]);
            return response()->json(Record::where('id', $record_id)->first(), Response::HTTP_OK);
        } catch (\HttpResponseException $ex) {
            return response()->json(
                ['error' => $ex]
            );
        }
    }

    public function remove_record($pet_id, $record_id): JsonResponse {
        try {
            $files = RecordFile::where('record_id', $record_id)->get();
            foreach ($files as $file) {
                $this->remove_file($pet_id, $record_id, $file->id);
            }
            Record::where('id', $record_id)->delete();
            return response()->json("Record and its files deleted successfully", Response::HTTP_OK);
        } catch (\HttpResponseException $ex) {
            return response()->json(
                ['error' => $ex]
            );
        }
    }

    public function get_files(int $pet_id, int $record_id): Collection {
        $files = RecordFile::where('record_id', $record_id)->get();
        $collection = collect([]);
        foreach ($files as $file) {
            $fileCollection = collect([
                'id' => $file->id,
                'file_name' => $file->file_name . "." . $file->extension
            ]);
            $collection->push($fileCollection);
        }
        return $collection;
    }

    public function add_files($pet_id, $record_id, Request $request): JsonResponse {
        $payload = $request->all();
        $pet = $this->authorizeUser($pet_id);
        try {
            Record::findOrFail($record_id);
        } catch (\HttpResponseException $ex) {
            return response()->json(
                ['error' => $ex]
            );
        }
        try {
            $filesCollection = collect([]);
            for ($i = 0; $request->hasFile('file' . $i); $i++) {
                $validator = Validator::make($payload, [
                    'file' . $i => 'mimes:doc,docx,pdf,txt,jpg,jpeg,png'
                ]);
                if ($validator->fails()) {
                    return response()->json(['errors' => "Uploaded file must be of type: doc, docx, pdf, txt, jpg, jpeg, png, odt"], 422);
                }
                $file = $request->file('file' . $i);
                $size = $file->getSize();
                if ($size > 10000000) throw new HttpResponseException(
                    response()->json(
                        ['errors' => "Uploaded file exceeds maximum size of 10Mb"], 422
                    ));
                try {
                    $storage_path = "pet_records/" . $pet->owner_id;
                    $path = $file->store($storage_path);
                    $ext = pathinfo($path, PATHINFO_EXTENSION);
                    $original_name = $file->getClientOriginalName();
                    if (strpos($original_name, $ext)) $new_name = rtrim($original_name, "." . $ext);
                    $path && RecordFile::create([
                        'record_id' => $record_id,
                        'file_name' => $new_name,
                        'path' => $path,
                        'owner_id' => $pet->owner_id,
                        'extension' => $ext
                    ]);
                    $file_id = RecordFile::where('path', $path)->first()->id;
                    $newFile = collect(['id' => $file_id]);
                    $filesCollection->push($newFile);
                } catch (\HttpResponseException $ex) {
                    return response()->json(
                        ['error' => $ex]
                    );
                }
            }
            return response()->json(['status' => 200,
                                     'files' => $filesCollection]);
        } catch (\HttpResponseException $ex) {
            return response()->json(
                ['error' => $ex]
            );
        }
    }

    public function rename_file($pet_id, $record_id, $file_id, Request $request): JsonResponse {
        $pet = $this->authorizeUser($pet_id);
        Record::findOrFail($record_id);
        try {
            $file = RecordFile::where('id', $file_id)->where('owner_id',
                $pet->owner_id)->where('record_id', $record_id)->first();
            $data = json_decode($request->getContent());
            RecordFile::where('id', $file->id)->first()->update([
                'file_name' => $data->name,
            ]);
            return response()->json("File renamed successfully", Response::HTTP_OK);
        } catch (\HttpResponseException $ex) {
            return response()->json(
                ['error' => $ex]
            );
        }
    }


    public function remove_file($pet_id, $record_id, $file_id): JsonResponse {
        $this->authorizeUser($pet_id);
        try {
            $path = RecordFile::where('record_id', $record_id)->where('id', $file_id)->first()->path;
            Storage::delete($path);
            RecordFile::where('record_id', $record_id)->where('id', $file_id)->delete();
            return response()->json("File deleted successfully", Response::HTTP_OK);
        } catch (\HttpResponseException $ex) {
            return response()->json(
                ['error' => $ex]
            );
        }
        //make me a new deploy
    }

    /**
     * @throws AuthenticationException
     */
    protected function authorizeUser(int $id) {
        $requestUser = User::Find($id);
        $loggedUser = Auth::User();

        if ($requestUser->id !== $loggedUser->id && $loggedUser->role_id !== UserRole::ADMINISTRATOR) {
            throw new AuthenticationException();
        }
    }

    protected function authorizeDoctor() {
        $loggedUser = Auth::User();

        if ($loggedUser->role_id !== UserRole::DOCTOR && $loggedUser->role_id
            !== UserRole::ADMINISTRATOR) {
            throw new AuthenticationException();
        }

        return $loggedUser->id;
    }

    private function createInvoiceItems($data, Record $record, int $doctor_id) {
        foreach ($data->billing_items as $item) {
            $chart = PriceChart::findOrFail($item->id);

            if ($chart->doctor_id !== $doctor_id) {
                throw new BadRequestHttpException();
            }

            InvoiceItem::create([
                'record_id' => $record->id,
                'item_id' => $item->id,
                'count' => $item->count
            ]);
        }
    }
}

