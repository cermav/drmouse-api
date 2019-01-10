<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScoreDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'score_id', 'score_item_id', 'points'
    ];
    
     /**
     * Get score parent
     */
    public function score()
    {
        return $this->belongsTo('App\Score');
    }
}
