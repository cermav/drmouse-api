<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetVaccine extends Model {
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
        'seen'
    ];

    public function pet(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(Pet::class);
    }

    public function vet(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(Doctor::class);
    }
}
