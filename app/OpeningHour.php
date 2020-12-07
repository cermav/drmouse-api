<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpeningHour extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'open_at',
        'close_at',
        'weekday_id',
        'user_id',
        'opening_hours_state_id',
    ];

    /**
     * Get the weekday for the opening hour item.
     */
    public function weekday()
    {
        return $this->belongsTo('App\Models\Weekday');
    }

    /**
     * Get the state for the opening hour item.
     */
    public function openingHoursState()
    {
        return $this->belongsTo('ihaha');
    }
}
