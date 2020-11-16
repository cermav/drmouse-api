<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $fillable = [
        'pet_name',
        'birth_date',
        'kind',
        'breed',
        'gender_state_id',
        'chip_number',
        'background',
        'avatar',
    ];
}
