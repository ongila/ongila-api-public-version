<?php

namespace App\Services\Inventory;

use App\Models\Inventory\Warehouse;
use App\Support\DomainConflictException;
use App\Support\QueryOptions;

class WarehouseService
{
    public function __construct(private QueryOptions $queryOptions)
    {
    }

    public function index(array $options)
    {
        $query = Warehouse::query()->with('company')->withCount('productStock');

        $this->queryOptions->apply(
            $query,
            $options,
            ['name'],
            ['company_id', 'type', 'is_market_visible'],
            ['id', 'name', 'type', 'created_at', 'updated_at']
        );

        return $this->queryOptions->result($query, $options);
    }

    public function create(array $data): Warehouse
    {
        return $this->load(Warehouse::query()->create($data));
    }

    public function show(int $id): Warehouse
    {
        return $this->load(Warehouse::query()->findOrFail($id));
    }

    public function update(int $id, array $data): Warehouse
    {
        $warehouse = Warehouse::query()->findOrFail($id);
        $warehouse->update($data);

        return $this->load($warehouse);
    }

    public function delete(int $id): void
    {
        $warehouse = Warehouse::query()->findOrFail($id);
        $hasStock = $warehouse->productStock()
            ->where(function ($query) {
                $query->where('stock', '>', 0)->orWhere('reserved', '>', 0);
            })
            ->exists();

        if ($hasStock) {
            throw new DomainConflictException(
                'The warehouse has stock or reservations and cannot be deleted.',
                'warehouse_has_stock'
            );
        }

        $warehouse->delete();
    }

    private function load(Warehouse $warehouse): Warehouse
    {
        return $warehouse->refresh()->load('company')->loadCount('productStock');
    }
}
