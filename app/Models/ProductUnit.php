<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductUnit extends Model
{
    protected $fillable = [
        'product_id',
        'unit_id',
        'conversion_to_base',
        'sell_price',
        'is_base',
    ];

    protected function casts(): array
    {
        return [
            'conversion_to_base' => 'decimal:3',
            'sell_price' => 'decimal:2',
            'is_base' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
