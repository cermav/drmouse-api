<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pets_appointments extends Model
{
    protected $fillable = ['owners_id', 'pet_id', 'date', 'description'];
}
