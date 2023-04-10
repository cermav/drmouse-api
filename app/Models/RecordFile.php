<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, $id)
 */
class RecordFile extends Model {
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

    public function record(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(Record::class);
    }

    public function owner(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(User::class);
    }

}
