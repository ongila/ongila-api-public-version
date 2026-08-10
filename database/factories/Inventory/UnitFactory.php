<?php

namespace Database\Factories\Inventory;

use App\Models\Inventory\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    protected $model = Unit::class;

    public function definition(): array
    {
        return [];
    }

    public function configure(): self
    {
        return $this->afterCreating(function (Unit $unit) {
            $name = $this->faker->unique()->word();
            $unit->translations()->create([
                'language_code' => 'en',
                'name' => ucfirst($name),
                'short_name' => substr($name, 0, 5),
            ]);
        });
    }
}
