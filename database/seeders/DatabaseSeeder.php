<?php

namespace Database\Seeders;

use App\Models\Finance\Currency;
use App\Models\HumanResources\Holiday;
use App\Models\HumanResources\Shift;
use App\Models\Inventory\Category;
use App\Models\Inventory\Company;
use App\Models\Inventory\Product;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Audit Reviewer',
            'email' => 'reviewer@example.test',
        ]);

        $uzs = $this->currency('UZS', 'Uzbekistani Som');
        $this->currency('USD', 'US Dollar');
        $this->currency('EUR', 'Euro');

        $category = Category::query()->create(['double_unit' => false]);
        $category->translations()->create(['language_code' => 'en', 'name' => 'Office Equipment']);

        $unit = Unit::query()->create();
        $unit->translations()->create([
            'language_code' => 'en',
            'name' => 'Piece',
            'short_name' => 'pc',
        ]);

        $company = Company::query()->create(['name' => 'ONGILA Demo Company']);
        Warehouse::query()->create([
            'company_id' => $company->id,
            'name' => 'Main Warehouse',
            'type' => 1,
            'is_market_visible' => false,
        ]);

        Product::query()->create([
            'name' => 'Ergonomic Office Chair',
            'model' => 'CHAIR-001',
            'code' => 'PRD-001',
            'expiration_days' => 0,
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'currency_code' => $uzs->code,
            'price' => 1500000,
            'buy_price' => 1100000,
            'is_published' => true,
        ]);

        Shift::query()->create([
            'code' => 'DAY',
            'name' => 'Day Shift',
            'status_id' => 1,
            'from' => '09:00',
            'to' => '18:00',
            'lunch_from' => '13:00',
            'lunch_to' => '14:00',
            'count_overtime' => true,
        ]);

        $holiday = Holiday::query()->create(['date' => '01-01', 'status_id' => 1]);
        $holiday->translations()->create(['language_code' => 'en', 'name' => 'New Year']);
    }

    private function currency(string $code, string $name): Currency
    {
        $currency = Currency::query()->create(['code' => $code]);
        $currency->translations()->create(['language_code' => 'en', 'name' => $name]);

        return $currency;
    }
}
