<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScoreVote extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'score_id', 'author_id', 'value', 'ip_address'
    ];

}
