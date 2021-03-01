<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $fillable = [
        'owners_id',
        'pet_name',
        'birth_date',
        'kind',
        'breed',
        'gender_state_id',
        'chip_number',
        'background',
        'avatar',
    ];
    public function vaccine()
    {
        return $this->hasMany(PetVaccine::class);
    }
}
