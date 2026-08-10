<?php

namespace App\Http\Resources\Inventory;

use App\Http\Resources\Concerns\FiltersResourceFields;
use App\Support\WarehouseType;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
{
    use FiltersResourceFields;

    public function toArray($request): array
    {
        return $this->filterFields([
            'id' => $this->id,
            'name' => $this->name,
            'company_id' => $this->company_id,
            'company_name' => $this->company?->name,
            'type' => $this->type,
            'type_name' => WarehouseType::label((int) $this->type),
            'is_market_visible' => $this->is_market_visible,
            'stock_rows_count' => $this->when(isset($this->product_stock_count), $this->product_stock_count),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}
