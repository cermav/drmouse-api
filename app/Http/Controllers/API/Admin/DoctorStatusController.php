<?php

namespace app\Http\Controllers\API\Admin;

use App\Types\UserRole;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DoctorStatusController extends Controller
{
    /*
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::User()->role_id != UserRole::ADMINISTRATOR) {
            throw new AuthenticationException();
        }

        // prepare basic select
        $states = DB::table('states')
            ->select(
                'id',
                'name',
                DB::raw(
                    "(SELECT COUNT(id) FROM doctors WHERE state_id = states.id) AS doctor_count"
                )
            )
            ->orderBy('id', 'ASC');

        return $states->get();
    }
}
