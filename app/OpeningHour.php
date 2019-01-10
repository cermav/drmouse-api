<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpeningHour extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'open_at', 'close_at', 'is_nonstop', 'is_closed', 'weekday_id', 'user_id'
    ];
    
    /**
     * Get the weekday for the opening hour item.
     */
    public function weekday()
    {
        return $this->belongsTo('App\Weekday');
    }

}
