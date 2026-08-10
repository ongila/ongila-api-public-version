<?php

namespace App\Services\HumanResources;

use App\Models\HumanResources\Holiday;
use App\Support\DomainConflictException;
use App\Support\QueryOptions;
use App\Support\TranslationSyncService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class HolidayService
{
    public function __construct(
        private QueryOptions $queryOptions,
        private TranslationSyncService $translations
    ) {
    }

    public function index(array $options)
    {
        $query = Holiday::query()->with('translations');

        $this->queryOptions->apply(
            $query,
            $options,
            ['date'],
            ['status_id'],
            ['id', 'date', 'status_id', 'created_at', 'updated_at'],
            ['translations' => ['name']]
        );

        return $this->queryOptions->result($query, $options);
    }

    public function create(array $data): Holiday
    {
        return DB::transaction(function () use ($data) {
            $holiday = Holiday::query()->create(Arr::except($data, 'translations'));
            $this->translations->sync($holiday, $data['translations']);

            return $this->load($holiday);
        });
    }

    public function show(int $id): Holiday
    {
        return $this->load(Holiday::query()->findOrFail($id));
    }

    public function update(int $id, array $data): Holiday
    {
        return DB::transaction(function () use ($id, $data) {
            $holiday = Holiday::query()->findOrFail($id);

            if ($holiday->date !== $data['date'] && $holiday->calendarDays()->exists()) {
                throw new DomainConflictException(
                    'A holiday date used by a generated calendar cannot be changed.',
                    'holiday_date_in_use'
                );
            }

            $holiday->update(Arr::except($data, 'translations'));
            $this->translations->sync($holiday, $data['translations']);

            return $this->load($holiday);
        });
    }

    public function delete(int $id): void
    {
        $holiday = Holiday::query()->findOrFail($id);

        if ($holiday->calendarDays()->exists()) {
            throw new DomainConflictException(
                'The holiday is used by a generated calendar and cannot be deleted.',
                'holiday_in_use'
            );
        }

        $holiday->delete();
    }

    private function load(Holiday $holiday): Holiday
    {
        return $holiday->refresh()->load('translations');
    }
}
