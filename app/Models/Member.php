<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, int $id)
 */
class Member extends Model {
    use HasRelationships;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'state_id',
        'description',
        'slug',
        'gdpr_agreed',
        'gdpr_agreed_date',
    ];

    /**
     * Get the user that owns the doctor.
     */
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
