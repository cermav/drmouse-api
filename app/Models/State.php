<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 14 May 2019 09:02:07 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class State
 * 
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \Illuminate\Database\Eloquent\Collection $doctors
 * @property \Illuminate\Database\Eloquent\Collection $doctors_logs
 *
 * @package App\Models
 */
class State extends Eloquent
{
	protected $fillable = [
		'name'
	];

	public function doctors()
	{
		return $this->hasMany(\App\Models\Doctor::class);
	}

	public function doctors_logs()
	{
		return $this->hasMany(\App\Models\DoctorsLog::class);
	}
}
