<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecordFile extends Model
{
    protected $table = 'record_files';
    protected $fillable = [
            'updated_at',
            'created_at',
            'record_id',
            'file_name',
            'path',
            'owner_id',
            'extension'
];
}
