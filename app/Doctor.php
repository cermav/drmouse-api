<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description', 'slug', 'speaks_english',
        'phone', 'second_phone', 'second_phone',
        'street', 'city', 'country', 'post_code', 'latitude', 'longitude', 
        'working_doctors_count', 'working_doctors_names', 'nurses_count', 'other_workers_count',
        'gdpr_agreed', 'gdpr_agreed_date', 'gdpr_agreed_ip', 'profile_completedness'
    ];
    
    /**
     * Get the user that owns the doctor.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}