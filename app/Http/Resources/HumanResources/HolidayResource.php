<?php

namespace App\Http\Resources\HumanResources;

use App\Http\Resources\Concerns\FiltersResourceFields;
use Illuminate\Http\Resources\Json\JsonResource;

class HolidayResource extends JsonResource
{
    use FiltersResourceFields;

    public function toArray($request): array
    {
        return $this->filterFields([
            'id' => $this->id,
            'date' => $this->date,
            'name' => $this->translated(),
            'status_id' => $this->status_id,
            'translations' => $this->translations->map->only(['id', 'language_code', 'name'])->values(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}
