<?php

namespace App\Models;

use App\Notifications\RegistartionMemberEmail;
use App\Notifications\RegistrationDoctorEmail;
use App\Notifications\ResetPasswordEmail;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @method static Find(int $id)
 * @method static where(string $string, $id)
 * @method static create(array $array)
 */
class User extends \TCG\Voyager\Models\User implements JWTSubject {
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'firstName',
        'lastName',
        'email',
        'avatar',
        'password',
        'role_id',
        'last_pet',
        'email_verified_at',
        'activated_at',
        'google_id',
        'facebook_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password',
                         'remember_token'];

    /**
     * Get the doctor record associated with the user.
     */
    public function doctor(): \Illuminate\Database\Eloquent\Relations\HasOne {
        return $this->hasOne('App\Models\Doctor');
    }

    /**
     * Get doctor's opening hours
     */
    public function openingHours(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany('App\Models\OpeningHour');
    }

    /**
     * Get doctor's properties
     */
    public function properties(): \Illuminate\Database\Eloquent\Relations\BelongsToMany {
        return $this->belongsToMany(
            'App\Models\Property',
            'doctors_properties'
        );
    }

    /**
     * Get doctor's services
     */
    public function services(): \Illuminate\Database\Eloquent\Relations\BelongsToMany {
        return $this->belongsToMany(
            'App\Models\Service',
            'doctors_services'
        )->withPivot('price');
    }

    /**
     * Get doctor's photos
     */
    public function photos(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany('App\Models\Photo');
    }

    /**
     * Get doctor's score
     */
    public function scores(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany('App\Models\Score')->orderBy(
            'created_at',
            'desc'
        );
    }

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array {
        return [
            'id' => $this->id,
            'role_id' => $this->role_id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar,
        ];
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendDoctorRegistrationEmailNotification() {
        $this->notify(new RegistrationDoctorEmail()); // my notification
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendMemberRegistrationEmailNotification() {
        $this->notify(new RegistartionMemberEmail()); // my notification
    }

    /**
     * Send a password reset email to the user
     */
    public function sendPasswordResetNotification($token) {
        $this->notify(new ResetPasswordEmail($token));
    }
}
