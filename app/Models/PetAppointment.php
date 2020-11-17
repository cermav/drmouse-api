<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetAappointment extends Model
{
    public $table = "pet_appointments";
    protected $fillable = ['date', 'description'];
}
