<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetAppointment extends Model
{
    public $table = "pet_appointments";
    protected $fillable = [
        'owners_id',
        'date',
        'description',
        'updated_at',
        'created_at',
        'pet_id',
    ];
}
