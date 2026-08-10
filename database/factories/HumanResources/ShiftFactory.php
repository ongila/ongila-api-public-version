<?php

namespace Database\Factories\HumanResources;

use App\Models\HumanResources\Shift;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShiftFactory extends Factory
{
    protected $model = Shift::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('S##')),
            'name' => ucfirst($this->faker->word()).' Shift',
            'status_id' => 1,
            'from' => '09:00',
            'to' => '18:00',
            'lunch_from' => '13:00',
            'lunch_to' => '14:00',
            'count_overtime' => true,
        ];
    }
}
