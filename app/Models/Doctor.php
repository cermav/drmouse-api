<?php

namespace App;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Doctor extends Model {

    use HasRelationships;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        'user_id', 'state_id',
        'description', 'slug', 'speaks_english',
        'search_name',
        'phone', 'second_phone', 'second_phone', 'website',
        'street', 'city', 'country', 'post_code', 'latitude', 'longitude',
        'working_doctors_count', 'working_doctors_names', 'nurses_count', 'other_workers_count',
        'gdpr_agreed', 'gdpr_agreed_date', 'gdpr_agreed_ip', 'profile_completedness',

    ];

    /*
     * Specify default order
     * Use Doctor::withoutGlobalScope('order')->get() if you don't want to apply default order rules
     */
    protected static function boot() {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('search_name', 'asc');
        });
    }

    /**
     * Get the user that owns the doctor.
     */
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
