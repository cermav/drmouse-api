<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static Find(int $pet_id)
 * @method static where(string $string, $id)
 * @method static create(array $array)
 * @method static findOrFail(int $pet_id)
 */
class Pet extends Model {
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

    public function vaccine(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(PetVaccine::class);
    }
}
