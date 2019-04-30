<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 30 Apr 2019 10:59:24 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Photo
 * 
 * @property int $id
 * @property int $user_id
 * @property string $path
 * @property int $position
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\User $user
 *
 * @package App\Models
 */
class Photo extends Eloquent
{
	protected $casts = [
		'user_id' => 'int',
		'position' => 'int'
	];

	protected $fillable = [
		'user_id',
		'path',
		'position'
	];

	public function user()
	{
		return $this->belongsTo(\App\Models\User::class);
	}
}
