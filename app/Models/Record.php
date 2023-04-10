<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, $id)
 * @method static create(array $array)
 * @method static findOrFail($record_id)
 */
class Record extends Model {
    protected $table = 'pet_records';
    public $timestamps = false;
    protected $fillable = [
        'appointment_id',
        'pet_id',
        'date',
        'medical_record',
        'description',
        'doctor_id',
        'time'
    ];

    public function files(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(RecordFile::class);
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(InvoiceItem::class);
    }

    public function pet(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(Pet::class);
    }

    public function vet(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(PetAppointment::class);
    }
}
