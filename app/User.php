<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends \TCG\Voyager\Models\User {

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the doctor record associated with the user.
     */
    public function doctor() {
        return $this->hasOne('App\Doctor');
    }

    /**
     * Get doctor's opening hours
     */
    public function openingHours() {
        return $this->hasMany('App\OpeningHour');
    }

    /**
     * Get doctor's properties
     */
    public function properties() {
        return $this->belongsToMany('App\Property', 'doctors_properties');
    }

    /**
     * Get doctor's services
     */
    public function services() {
        return $this->belongsToMany('App\Service', 'doctors_services')->withPivot('price');
    }

    /**
     * Get doctor's photos
     */
    public function photos() {
        return $this->hasMany('App\Photo');
    }
    
    /**
     * Get doctor's score
     */
    public function scores() {
        return $this->hasMany('App\Score');
    }

}
