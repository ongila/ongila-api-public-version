<?php

namespace App\Models\Inventory;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\TracksActor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;
    use HasTranslations;
    use SoftDeletes;
    use TracksActor;

    protected $table = 'wh_categories';

    protected $fillable = ['parent_id', 'double_unit'];

    protected $casts = ['double_unit' => 'boolean'];

    public function translationModel(): string
    {
        return CategoryTranslation::class;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
