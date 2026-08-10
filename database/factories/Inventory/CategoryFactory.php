<?php

namespace Database\Factories\Inventory;

use App\Models\Inventory\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'parent_id' => null,
            'double_unit' => false,
        ];
    }

    public function configure(): self
    {
        return $this->afterCreating(function (Category $category) {
            $category->translations()->create([
                'language_code' => 'en',
                'name' => $this->faker->unique()->word(),
            ]);
        });
    }
}
