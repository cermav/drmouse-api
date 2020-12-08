<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 30 Apr 2019 10:11:51 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class OpeningHour
 *
 * @property int $id
 * @property int $weekday_id
 * @property int $user_id
 * @property int $opening_hours_state_id
 * @property \Carbon\Carbon $open_at
 * @property \Carbon\Carbon $close_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property \App\Models\OpeningHoursState $opening_hours_state
 * @property \App\Models\User $user
 * @property \App\Models\Weekday $weekday
 *
 * @package App\Models
 */
class OpeningHour extends Eloquent
{
    protected $casts = [
        'weekday_id' => 'int',
        'user_id' => 'int',
        'opening_hours_state_id' => 'int',
    ];

    protected $fillable = [
        'weekday_id',
        'user_id',
        'opening_hours_state_id',
        'open_at',
        'close_at',
    ];

    public function openingHoursState()
    {
        return $this->belongsTo('App\Models\OpeningHoursState');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function weekday()
    {
        return $this->belongsTo('App\Models\Weekday');
    }
}
