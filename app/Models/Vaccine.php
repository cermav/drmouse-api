<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vaccine extends Model
{
    protected $fillable = ['apply_date', 'valid', 'name', 'price'];
}
