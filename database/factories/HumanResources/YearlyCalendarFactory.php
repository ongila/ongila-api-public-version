<?php

namespace Database\Factories\HumanResources;

use App\Models\HumanResources\YearlyCalendar;
use Illuminate\Database\Eloquent\Factories\Factory;

class YearlyCalendarFactory extends Factory
{
    protected $model = YearlyCalendar::class;

    public function definition(): array
    {
        $date = $this->faker->unique()->dateTimeBetween('2025-01-01', '2030-12-31');
        $isWeekend = in_array((int) $date->format('N'), [6, 7], true);

        return [
            'calendar_date' => $date->format('Y-m-d'),
            'holiday_id' => null,
            'is_weekend' => $isWeekend,
            'is_workday' => ! $isWeekend,
            'workday_sequence' => null,
        ];
    }
}
