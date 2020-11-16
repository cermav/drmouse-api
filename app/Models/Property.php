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
        'name', 'property_category_id', 'is_approved', 'show_on_registration', 'show_in_search'
    ];
    
    /**
     * Get property's category
     */
    public function category()
    {
        return $this->belongsTo('App\PropertyCategory', 'property_category_id');
    }
}
