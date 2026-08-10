<?php

namespace App\Http\Resources\Finance;

use App\Http\Resources\Concerns\FiltersResourceFields;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyRateResource extends JsonResource
{
    use FiltersResourceFields;

    public function toArray($request): array
    {
        return $this->filterFields([
            'id' => $this->id,
            'from_currency' => $this->from_currency,
            'to_currency' => $this->to_currency,
            'value' => $this->value,
            'begin_date' => optional($this->begin_date)->toIso8601String(),
            'end_date' => optional($this->end_date)->toIso8601String(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}
