<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Score extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'comment', 'ip_address', 'is_approved', 'user_id'
    ];
    
    /*
     * Specify default order
     * Use Score::withoutGlobalScope('order')->get() if you don't want to apply default order rules
     */
    protected static function boot() {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }
    
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
