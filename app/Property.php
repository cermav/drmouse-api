<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'category_id'
    ];
    
    /**
     * Get property's category
     */
    public function category()
    {
        return $this->belongsTo('App\PropertyCategory');
    }
}
