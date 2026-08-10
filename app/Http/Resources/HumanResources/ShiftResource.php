<?php

namespace App\Http\Resources\HumanResources;

use App\Http\Resources\Concerns\FiltersResourceFields;
use Illuminate\Http\Resources\Json\JsonResource;

class ShiftResource extends JsonResource
{
    use FiltersResourceFields;

    public function toArray($request): array
    {
        return $this->filterFields([
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'status_id' => $this->status_id,
            'from' => $this->from,
            'to' => $this->to,
            'lunch_from' => $this->lunch_from,
            'lunch_to' => $this->lunch_to,
            'count_overtime' => $this->count_overtime,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}
