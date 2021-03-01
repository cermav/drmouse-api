<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetVaccine extends Model
{
    protected $table = 'pet_vaccines';
    public $timestamps = false;
    protected $fillable = [
        'apply_date',
        'valid',
        'description',
        'price',
        'pet_id',
        'vaccine_id',
        'doctor_id',
        'notes',
        'color',
        'city',
        'street'
    ];
    public function pet()
    {
        return $this->belongsToMany(Pet::class);
    }
    public function vet()
    {
        return $this->belongsToMany(Doctor::class);
    }
}
