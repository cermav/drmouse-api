<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pets extends Model
{
    protected $fillable = [
        'id',
        'owners_id',
        'pet_name',
        'birth_date',
        'kind',
        'breed',
        'gender_state_id',
        'chip_number',
        'bg',
        'profile_completedness',
        'avatar',
        'last_used',
    ];
}
