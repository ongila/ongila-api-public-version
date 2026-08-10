<?php

namespace App\Http\Resources\HumanResources;

use App\Http\Resources\Concerns\FiltersResourceFields;
use Illuminate\Http\Resources\Json\JsonResource;

class YearlyCalendarResource extends JsonResource
{
    use FiltersResourceFields;

    public function toArray($request): array
    {
        return $this->filterFields([
            'id' => $this->id,
            'calendar_date' => $this->calendar_date->format('Y-m-d'),
            'holiday_id' => $this->holiday_id,
            'holiday_name' => $this->holiday?->translated(),
            'is_weekend' => $this->is_weekend,
            'is_workday' => $this->is_workday,
            'workday_sequence' => $this->workday_sequence,
        ]);
    }
}
