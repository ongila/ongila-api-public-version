<?php

namespace Database\Factories\Inventory;

use App\Models\Finance\Currency;
use App\Models\Inventory\Category;
use App\Models\Inventory\Product;
use App\Models\Inventory\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => ucfirst($this->faker->words(2, true)),
            'model' => strtoupper($this->faker->bothify('MOD-###??')),
            'code' => strtoupper($this->faker->unique()->bothify('PRD-#####')),
            'expiration_days' => 0,
            'category_id' => Category::factory(),
            'unit_id' => Unit::factory(),
            'currency_code' => fn () => Currency::factory()->create()->code,
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'buy_price' => $this->faker->randomFloat(2, 1, 500),
            'is_detail' => false,
            'is_published' => true,
        ];
    }
}
