<?php

namespace app\Http\Controllers\Api;

use App\Models\NewsletterUser;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\PetNotification;

class NewsletterUserController extends Controller
{
    /**
     * Store newsletter user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function test()
    {
        //$email = $user->email;
        $email = "cermav@gmail.com";
        Mail::to($email)->send(new PetNotification((object) ['data' => 'test']));

        
    }
    public function store(Request $request)
    {
        $input = json_decode($request->getContent());

        // validate input
        $validator = Validator::make((array) $input, [
            'email' => 'required|email|unique:newsletter_users',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // store user
        $newsletter_user = NewsletterUser::create([
            'email' => $input->email,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);

        $newsletter_user->sendEmailVerificationNotification();

        return $newsletter_user;
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        // verify url signature
        if (!$request->hasValidSignature()) {
            return response()->json('Invalid signature', 403);
        }

        // get user from id
        $newsletter_user = NewsletterUser::findOrFail($request->route('id'));

        // verify reset token
        if (
            app(PasswordBroker::class)->tokenExists(
                $newsletter_user,
                $request->get('token')
            )
        ) {
            // mark as verified
            if ($newsletter_user->markEmailAsVerified()) {
                event(new Verified($newsletter_user));
            }
        }
        return response()->json('Email verified!');
    }
}
