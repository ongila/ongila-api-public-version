<?php

namespace App\Services\Inventory;

use App\Models\Inventory\Unit;
use App\Support\DomainConflictException;
use App\Support\QueryOptions;
use App\Support\TranslationSyncService;
use Illuminate\Support\Facades\DB;

class UnitService
{
    public function __construct(
        private QueryOptions $queryOptions,
        private TranslationSyncService $translations
    ) {
    }

    public function index(array $options)
    {
        $query = Unit::query()->with('translations')->withCount('products');

        $this->queryOptions->apply(
            $query,
            $options,
            [],
            [],
            ['id', 'created_at', 'updated_at'],
            ['translations' => ['name', 'short_name']]
        );

        return $this->queryOptions->result($query, $options);
    }

    public function create(array $data): Unit
    {
        return DB::transaction(function () use ($data) {
            $unit = Unit::query()->create();
            $this->translations->sync($unit, $data['translations']);

            return $this->load($unit);
        });
    }

    public function show(int $id): Unit
    {
        return $this->load(Unit::query()->findOrFail($id));
    }

    public function update(int $id, array $data): Unit
    {
        return DB::transaction(function () use ($id, $data) {
            $unit = Unit::query()->findOrFail($id);
            $this->translations->sync($unit, $data['translations']);

            return $this->load($unit);
        });
    }

    public function delete(int $id): void
    {
        $unit = Unit::query()->findOrFail($id);

        if ($unit->products()->exists()) {
            throw new DomainConflictException(
                'The unit is assigned to products and cannot be deleted.',
                'unit_in_use'
            );
        }

        $unit->delete();
    }

    private function load(Unit $unit): Unit
    {
        return $unit->refresh()->load('translations')->loadCount('products');
    }
}
