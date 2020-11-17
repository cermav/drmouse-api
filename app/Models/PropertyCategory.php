<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyCategory extends Model
{
    /**
     * Get category's properties
     */
    public function properties()
    {
        return $this->hasMany('App\Property')->orderBy('name');
    }
}
