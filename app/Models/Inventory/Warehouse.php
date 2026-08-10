<?php

namespace App\Models\Inventory;

use App\Models\Concerns\TracksActor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory;
    use SoftDeletes;
    use TracksActor;

    protected $table = 'wh_warehouses';

    protected $fillable = ['company_id', 'name', 'type', 'is_market_visible'];

    protected $casts = ['is_market_visible' => 'boolean'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function productStock(): HasMany
    {
        return $this->hasMany(ProductStock::class, 'warehouse_id');
    }
}
