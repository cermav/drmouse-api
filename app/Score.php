<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'comment', 'ip_address', 'score_date', 'is_approved', 'user_id'
    ];
    
    /**
     * Get score details
     */
    public function details() {
        return $this->hasMany('App\ScoreDetail');
    }
    
    /**
     * Get user who added the score
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
