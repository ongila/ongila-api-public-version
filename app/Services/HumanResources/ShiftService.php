<?php

namespace App\Services\HumanResources;

use App\Models\HumanResources\Shift;
use App\Support\QueryOptions;

class ShiftService
{
    public function __construct(private QueryOptions $queryOptions)
    {
    }

    public function index(array $options)
    {
        $query = Shift::query();

        $this->queryOptions->apply(
            $query,
            $options,
            ['code', 'name', 'description'],
            ['status_id'],
            ['id', 'code', 'name', 'from', 'to', 'created_at', 'updated_at']
        );

        return $this->queryOptions->result($query, $options);
    }

    public function create(array $data): Shift
    {
        return Shift::query()->create($data);
    }

    public function show(int $id): Shift
    {
        return Shift::query()->findOrFail($id);
    }

    public function update(int $id, array $data): Shift
    {
        $shift = Shift::query()->findOrFail($id);
        $shift->update($data);

        return $shift->refresh();
    }

    public function delete(int $id): void
    {
        Shift::query()->findOrFail($id)->delete();
    }
}
