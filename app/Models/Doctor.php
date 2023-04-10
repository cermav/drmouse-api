<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, $id)
 */
class Doctor extends Model {
    use HasRelationships;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'state_id',
        'description',
        'slug',
        'speaks_english',
        'search_name',
        'phone',
        'second_phone',
        'website',
        'street',
        'city',
        'country',
        'post_code',
        'latitude',
        'longitude',
        'working_doctors_count',
        'working_doctors_names',
        'nurses_count',
        'other_workers_count',
        'gdpr_agreed',
        'gdpr_agreed_date',
        'gdpr_agreed_ip',
        'profile_completedness',
        'ICO',
        'DIC',
        'bank_account'
    ];

    /*
     * Specify default order
     * Use Doctor::withoutGlobalScope('order')->get() if you don't want to apply default order rules
     */
    protected static function boot() {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('search_name');
        });
    }

    /**
     * Get the user that owns the doctor.
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function priceCharts(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(PriceChart::class);
    }
}
