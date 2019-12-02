<?php
/**
 * Created by PhpStorm.
 * User: petr
 * Date: 28.11.2019
 * Time: 17:02
 */

namespace App\Validators;


use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OpeningHoursValidator
{
    public static function create(array $data)
    {
        $validator = Validator::make($data, [
            'weekday_id' => 'required|int',
            'state_id' => 'required|int',
            'open_at' => 'required|date_format:H:i:s',
            'close_at' => 'required|date_format:H:i:s'
        ]);

        return $validator;
    }

}