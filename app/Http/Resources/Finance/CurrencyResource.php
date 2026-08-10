<?php

namespace App\Http\Resources\Finance;

use App\Http\Resources\Concerns\FiltersResourceFields;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
{
    use FiltersResourceFields;

    public function toArray($request): array
    {
        return $this->filterFields([
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->translated(),
            'translations' => $this->translations->map->only(['id', 'language_code', 'name'])->values(),
            'products_count' => $this->when(isset($this->products_count), $this->products_count),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}
