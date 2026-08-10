<?php

namespace App\Models\Inventory;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\TracksActor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory;
    use HasTranslations;
    use SoftDeletes;
    use TracksActor;

    protected $table = 'wh_units';

    protected $guarded = [];

    public function translationModel(): string
    {
        return UnitTranslation::class;
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'unit_id');
    }
}
