<?php

namespace App\Models\Finance;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\TracksActor;
use App\Models\Inventory\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use HasFactory;
    use HasTranslations;
    use SoftDeletes;
    use TracksActor;

    protected $table = 'fi_currencies';

    protected $fillable = ['code'];

    public function translationModel(): string
    {
        return CurrencyTranslation::class;
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'currency_code', 'code');
    }
}
