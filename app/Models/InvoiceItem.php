<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model {
    protected $table = 'invoice_items';
    public $timestamps = true;
    protected $fillable = [
        'record_id',
        'item_id',
        'count'
    ];

    public function priceChart(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(PriceChart::class, 'item_id');
    }
}
