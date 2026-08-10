<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStock extends Model
{
    public $timestamps = false;

    protected $table = 'wh_product_stock';

    protected $fillable = ['product_id', 'warehouse_id', 'stock', 'reserved'];

    protected $casts = ['stock' => 'float', 'reserved' => 'float'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
