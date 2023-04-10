<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, $id)
 * @method static create(array $array)
 * @method static find($id)
 */
class PriceChart extends Model {
    protected $table = 'price_chart';
    public $timestamps = true;
    protected $fillable = ['id',
                           'doctor_id',
                           'description',
                           'price',
                           'currency',
                           'display'];
}
