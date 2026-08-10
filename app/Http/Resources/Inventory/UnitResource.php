<?php

namespace App\Http\Resources\Inventory;

use App\Http\Resources\Concerns\FiltersResourceFields;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
{
    use FiltersResourceFields;

    public function toArray($request): array
    {
        return $this->filterFields([
            'id' => $this->id,
            'name' => $this->translated(),
            'short_name' => $this->translated('short_name'),
            'translations' => $this->translations
                ->map->only(['id', 'language_code', 'name', 'short_name'])
                ->values(),
            'products_count' => $this->when(isset($this->products_count), $this->products_count),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}
