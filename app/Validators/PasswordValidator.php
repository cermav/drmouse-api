<?php
/**
 * Created by PhpStorm.
 * User: petr
 * Date: 28.11.2019
 * Time: 17:02
 */

namespace App\Validators;


use Illuminate\Support\Facades\Validator;

class PasswordValidator
{
    public static function create(array $data)
    {
        $messages = [
            'current_password.required' => 'Please enter current password',
            'password.required' => 'Please enter password',
        ];

        $validator = Validator::make($data, [
            'current_password' => 'required',
            'password' => 'required|same:password|string|min:6',
            'password_confirmation' => 'required|same:password',
        ], $messages);

        return $validator;
    }

}