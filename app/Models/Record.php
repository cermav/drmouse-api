<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{ 
    protected $table = 'pet_records';
    public $timestamps = false;
    protected $fillable = [
            'pet_id',
            'date',
            'notes',
            'description',
            'doctor_id'
];
public function files()
{
    return $this->hasMany(RecordFile::class);
}
}
