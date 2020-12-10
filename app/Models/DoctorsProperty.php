<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 29 Apr 2019 14:13:35 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class DoctorsProperty
 *
 * @property int $id
 * @property int $user_id
 * @property int $property_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property \App\Models\Property $property
 * @property \App\Models\User $user
 *
 * @package App\Models
 */
class DoctorsProperty extends Eloquent
{
    protected $casts = [
        'user_id' => 'int',
        'property_id' => 'int',
    ];

    protected $fillable = ['user_id', 'property_id'];

    public function property()
    {
        return $this->belongsTo(\App\Models\Property::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
