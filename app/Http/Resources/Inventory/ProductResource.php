<?php

namespace App\Http\Resources\Inventory;

use App\Http\Resources\Concerns\FiltersResourceFields;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    use FiltersResourceFields;

    public function toArray($request): array
    {
        return $this->filterFields([
            'id' => $this->id,
            'name' => $this->name,
            'name_eng' => $this->name_eng,
            'model' => $this->model,
            'code' => $this->code,
            'article' => $this->article,
            'expiration_days' => $this->expiration_days,
            'category_id' => $this->category_id,
            'category_name' => $this->category?->translated(),
            'unit_id' => $this->unit_id,
            'unit_name' => $this->unit?->translated(),
            'unit_short_name' => $this->unit?->translated('short_name'),
            'currency_code' => $this->currency_code,
            'currency_name' => $this->currency?->translated(),
            'price' => $this->price,
            'buy_price' => $this->buy_price,
            'package_qty' => $this->package_qty,
            'min_stock' => $this->min_stock,
            'max_stock' => $this->max_stock,
            'weight' => $this->weight,
            'dimensions' => $this->dimensions,
            'capacity' => $this->capacity,
            'description' => $this->description,
            'is_detail' => $this->is_detail,
            'is_published' => $this->is_published,
            'stock' => (float) ($this->stocks_sum_stock ?? 0),
            'reserved' => (float) ($this->stocks_sum_reserved ?? 0),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}
