<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static find(int $id)
 * @method static findOrFail(int $event_id)
 * @method static where(string $string, $pet_id)
 * @method static create(array $array)
 */
class PetAppointment extends Model {
    public $table = "pet_appointments";
    protected $fillable = [
        'owners_id',
        'date',
        'title',
        'updated_at',
        'created_at',
        'pet_id',
        'doctor_id',
        'start',
        'end',
        'accepted',
        'assigned_to',
        'phone_number',
        'mail',
        'name',
        'surname',
        'allDay'
    ];

    public function pet(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(Pet::class);
    }

    public function vet(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(Doctor::class);
    }

    public function owner(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function records(): \Illuminate\Database\Eloquent\Relations\HasOne {
        return $this->hasOne(Record::class);
    }
}
