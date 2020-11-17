<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorsLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'doctors_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id',
        'user_id',
        'state_id',
        'note',
        'email_sent',
        'doctor_object',
    ];
}
