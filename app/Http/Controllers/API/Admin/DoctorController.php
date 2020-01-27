<?php

namespace App\Http\Controllers\Api\Admin;

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
use Illuminate\Http\Request;
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

class DoctorController extends Controller
{

    private $pageLimit = 30;

    /**
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
        $doctors = DB::table('doctors')
            ->select(
                'users.id',
                DB::raw("(SELECT name FROM states WHERE id = doctors.state_id) AS state_name"),
                'name',
                'slug',
                'street',
                'city',
                'country',
                'post_code',
                'avatar',
                DB::raw("(SELECT IFNULL( ROUND(((SUM(points)/COUNT(id))/5)*100) , 0) FROM score_details WHERE score_id IN (SELECT id FROM scores WHERE user_id = doctors.user_id)) AS total_score ")
            )
            ->join('users', 'doctors.user_id', '=', 'users.id')
            ->whereIn('doctors.state_id', [
                DoctorStatus::NEW,
                DoctorStatus::DRAFT,
                DoctorStatus::PUBLISHED,
                DoctorStatus::UNPUBLISHED,
                DoctorStatus::INCOMPLETE
            ]);


        // add fulltext condition
        if ($request->has('fulltext') && strlen(trim($request->input('fulltext'))) > 2) {
            // split words and add wildcard
            $search_text = '*' . implode('* *', explode(' ', urldecode(trim($request->input('fulltext'))))) . '*';
            $doctors->selectRaw(
                "(
                    MATCH (search_name, description, street, city, country, working_doctors_names) AGAINST (? IN BOOLEAN MODE) +
                    MATCH (email) AGAINST (? IN BOOLEAN MODE)
                ) AS relevance",
                [$search_text, $search_text]
            );
            $doctors->whereRaw("(
                    MATCH (search_name, description, street, city, country, working_doctors_names) AGAINST (? IN BOOLEAN MODE)
                    OR MATCH (email) AGAINST (? IN BOOLEAN MODE)
                )", [$search_text, $search_text]);
        } else {
            $doctors->selectRaw('0 AS relevance');
        }

        // search by status
        if ($request->has('status') && intval($request->input('status')) > 0) {
            $doctors->where('doctors.state_id', intval($request->input('status')));
        }

        // sorting
        $doctors->orderBy('search_name', 'ASC');
        $doctors->paginate($this->pageLimit);

        return $doctors->paginate($this->pageLimit);
    }

}
