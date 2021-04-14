<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RecordFile;
use App\Types\UserRole;
use JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function getFileURI($file_id)
    {
        return Auth::User()->id;
        if (!RecordFile::find($file_id)) return response()->json(
            ['error' => 'Failed to authorize the request'],
            401
        );
        if (Auth::User()->id != RecordFile::find($file_id)->owner_id || Auth::User()->role_id != UserRole::ADMINISTRATOR) 
        {
            return response()->json(
                ['error' => 'Failed to authorize the request.'],
                401
            );
        }
        try{
            $path = RecordFile::where('owner_id', Auth::User()->id)->where('id',$file_id)->first()->path;
        }
        catch (\Exception $ex) {
            return response()->json(
                ['error' => ['location' => $ex->getMessage()]],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
        return Storage::download($path);
    }
}
