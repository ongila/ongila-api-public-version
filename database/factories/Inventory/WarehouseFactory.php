<?php

namespace Database\Factories\Inventory;

use App\Models\Inventory\Company;
use App\Models\Inventory\Warehouse;
use App\Support\WarehouseType;
use Illuminate\Database\Eloquent\Factories\Factory;

class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => $this->faker->unique()->city().' Warehouse',
            'type' => WarehouseType::WAREHOUSE,
            'is_market_visible' => false,
        ];
    }
}
