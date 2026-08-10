<?php

namespace Database\Factories\HumanResources;

use App\Models\HumanResources\Holiday;
use Illuminate\Database\Eloquent\Factories\Factory;

class HolidayFactory extends Factory
{
    protected $model = Holiday::class;

    public function definition(): array
    {
        return [
            'date' => $this->faker->unique()->date('m-d'),
            'status_id' => 1,
        ];
    }

    public function configure(): self
    {
        return $this->afterCreating(function (Holiday $holiday) {
            $holiday->translations()->create([
                'language_code' => 'en',
                'name' => 'Holiday '.$holiday->date,
            ]);
        });
    }
}
