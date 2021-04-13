<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RecordFile;
use App\Types\UserRole;
use JWTAuth;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    public function getFileURI($file_id)
    {
        if (!RecordFile::find($file_id)) return response()->json(
            ['error' => 'Failed to authorize the request.'],
            401
        );
        echo Auth::User()->id;
        echo " ";
        echo RecordFile::find($file_id)->owner_id;
        if (Auth::User()->id !== RecordFile::find($file_id)->owner_id || Auth::User()->role_id != UserRole::ADMINISTRATOR) {
            return response()->json(
                ['error' => 'Failed to authorize the request.'],
                401
            );
        }
        return RecordFile::where('owner_id', Auth::User()->id)->find($file_id)->uuid;

    }
}
