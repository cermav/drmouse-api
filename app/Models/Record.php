<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{ 
    protected $table = 'pet_records';
    protected $fillable = [
            'pet_id',
            'updated_at',
            'created_at',
            'description',
            'doctor_id'
];
public function files()
{
    return $this->hasMany(RecordFile::class);
}
}
