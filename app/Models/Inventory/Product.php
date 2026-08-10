<?php

namespace App\Models\Inventory;

use App\Models\Concerns\TracksActor;
use App\Models\Finance\Currency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    use TracksActor;

    protected $table = 'wh_products';

    protected $fillable = [
        'name',
        'name_eng',
        'model',
        'code',
        'article',
        'expiration_days',
        'category_id',
        'unit_id',
        'currency_code',
        'price',
        'buy_price',
        'package_qty',
        'min_stock',
        'max_stock',
        'weight',
        'dimensions',
        'capacity',
        'description',
        'is_detail',
        'is_published',
    ];

    protected $casts = [
        'price' => 'float',
        'buy_price' => 'float',
        'package_qty' => 'float',
        'min_stock' => 'float',
        'max_stock' => 'float',
        'weight' => 'float',
        'is_detail' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(ProductStock::class, 'product_id');
    }
}
