<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vaccine extends Model {
    protected $table = 'vaccines';
    protected $fillable = [
        'id',
        'company',
        'name'
    ];
    public $timestamps = false;

    public function petVaccine(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(PetVaccine::class);
    }
}
