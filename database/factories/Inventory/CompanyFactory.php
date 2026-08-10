<?php

namespace Database\Factories\Inventory;

use App\Models\Inventory\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return ['name' => $this->faker->unique()->company()];
    }
}
