<?php

namespace App\Services\HumanResources;

use App\Models\HumanResources\Holiday;
use App\Models\HumanResources\YearlyCalendar;
use App\Support\DomainConflictException;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class YearlyCalendarService
{
    public function years(): array
    {
        return YearlyCalendar::query()
            ->orderByDesc('calendar_date')
            ->pluck('calendar_date')
            ->map(fn ($date) => Carbon::parse($date)->year)
            ->unique()
            ->values()
            ->all();
    }

    public function year(int $year, ?int $month = null): Collection
    {
        $query = YearlyCalendar::query()
            ->with('holiday.translations')
            ->whereYear('calendar_date', $year)
            ->orderBy('calendar_date');

        if ($month) {
            $query->whereMonth('calendar_date', $month);
        }

        return $query->get();
    }

    public function month(string $date): array
    {
        $month = Carbon::parse($date);
        $days = YearlyCalendar::query()
            ->whereYear('calendar_date', $month->year)
            ->whereMonth('calendar_date', $month->month)
            ->get();

        return [
            'days' => $days->count(),
            'work_days' => $days->where('is_workday', true)->count(),
            'non_work_days' => $days->where('is_workday', false)->count(),
            'weekend_days' => $days->where('is_weekend', true)->count(),
            'holidays' => $days->whereNotNull('holiday_id')->count(),
        ];
    }

    public function generate(int $year): Collection
    {
        if ($year < 2000 || $year > 2100) {
            throw new DomainConflictException('The year must be between 2000 and 2100.', 'invalid_year');
        }

        if (YearlyCalendar::query()->whereYear('calendar_date', $year)->exists()) {
            throw new DomainConflictException('This year has already been generated.', 'calendar_exists');
        }

        return DB::transaction(function () use ($year) {
            $holidays = Holiday::query()
                ->where('status_id', 1)
                ->get()
                ->keyBy('date');
            $workDays = config('erp.work_days');
            $date = CarbonImmutable::create($year, 1, 1)->startOfDay();
            $end = $date->endOfYear();
            $sequence = 1;
            $rows = [];
            $now = now();

            while ($date->lessThanOrEqualTo($end)) {
                $holiday = $holidays->get($date->format('m-d'));
                $isWeekend = ! in_array($date->dayOfWeek, $workDays, true);
                $isWorkday = ! $isWeekend && ! $holiday;

                $rows[] = [
                    'calendar_date' => $date->format('Y-m-d'),
                    'holiday_id' => $holiday?->id,
                    'is_weekend' => $isWeekend,
                    'is_workday' => $isWorkday,
                    'workday_sequence' => $isWorkday ? $sequence++ : null,
                    'created_by' => Auth::id(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $date = $date->addDay();
            }

            YearlyCalendar::query()->insert($rows);

            return $this->year($year);
        });
    }

    public function updateDay(int $id, array $data): Collection
    {
        return DB::transaction(function () use ($id, $data) {
            $day = YearlyCalendar::query()->findOrFail($id);

            if (! empty($data['holiday_id'])) {
                $day->holiday_id = $data['holiday_id'];
                $day->is_workday = false;
            } else {
                $day->holiday_id = null;
                $day->is_workday = (bool) $data['is_workday'];
            }

            $day->save();
            $this->resequence($day->calendar_date->year);

            return $this->year($day->calendar_date->year);
        });
    }

    private function resequence(int $year): void
    {
        $sequence = 1;
        $days = YearlyCalendar::query()
            ->whereYear('calendar_date', $year)
            ->orderBy('calendar_date')
            ->get();

        foreach ($days as $day) {
            $day->workday_sequence = $day->is_workday ? $sequence++ : null;
            $day->save();
        }
    }
}
