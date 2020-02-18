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
        'user_id', 'author_id', 'comment', 'ip_address', 'is_approved', 'verified_by', 'verify_date'
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

    /**
     * Get user who added the score
     */
    public function author()
    {
        return $this->belongsTo('App\User');
    }
}
