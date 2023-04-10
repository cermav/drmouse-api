<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteVet extends Model {
    protected $table = 'user_favorite_doctors';
    protected $fillable = [
        'user_id',
        'doctor_id'
    ];
    public $timestamps = false;

    function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany {
        return $this->belongsToMany(User::class);
    }

    function doctors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany {
        return $this->belongsToMany(Doctor::class);
    }
}
