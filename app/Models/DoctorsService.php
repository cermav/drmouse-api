<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 29 Apr 2019 13:19:09 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class DoctorsService
 * 
 * @property int $id
 * @property int $user_id
 * @property int $service_id
 * @property int $price
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\Service $service
 * @property \App\Models\User $user
 *
 * @package App\Models
 */
class DoctorsService extends Eloquent
{
	protected $casts = [
		'user_id' => 'int',
		'service_id' => 'int',
		'price' => 'int'
	];

	protected $fillable = [
		'user_id',
		'service_id',
		'price'
	];

	public function service()
	{
		return $this->belongsTo(\App\Models\Service::class);
	}

	public function user()
	{
		return $this->belongsTo(\App\Models\User::class);
	}
}
