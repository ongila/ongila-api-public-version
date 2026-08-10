<?php

namespace App\Http\Resources\Inventory;

use App\Http\Resources\Concerns\FiltersResourceFields;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    use FiltersResourceFields;

    public function toArray($request): array
    {
        return $this->filterFields([
            'id' => $this->id,
            'name' => $this->translated(),
            'parent_id' => $this->parent_id,
            'parent_name' => $this->parent?->translated(),
            'double_unit' => $this->double_unit,
            'translations' => $this->translations->map->only(['id', 'language_code', 'name'])->values(),
            'children_count' => $this->when(isset($this->children_count), $this->children_count),
            'products_count' => $this->when(isset($this->products_count), $this->products_count),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}
