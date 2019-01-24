<?php

namespace App;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasRelationships;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'search_name', 'description', 'slug', 'speaks_english',
        'phone', 'second_phone', 'second_phone',
        'street', 'city', 'country', 'post_code', 'latitude', 'longitude', 
        'working_doctors_count', 'working_doctors_names', 'nurses_count', 'other_workers_count',
        'gdpr_agreed', 'gdpr_agreed_date', 'gdpr_agreed_ip', 'profile_completedness', 
        'user_id', 'state_id'
    ];
    
    /**
     * Get the user that owns the doctor.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}