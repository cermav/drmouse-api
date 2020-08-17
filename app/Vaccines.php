<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vaccines extends Model
{
    protected $fillable = [
        'owner_id',
        'pet_id',
        'apply_date',
        'valid',
        'name',
        'price',
    ];
}
