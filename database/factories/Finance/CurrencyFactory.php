<?php

namespace Database\Factories\Finance;

use App\Models\Finance\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    public function definition(): array
    {
        return ['code' => strtoupper($this->faker->unique()->lexify('???'))];
    }

    public function configure(): self
    {
        return $this->afterCreating(function (Currency $currency) {
            $currency->translations()->create([
                'language_code' => 'en',
                'name' => $currency->code.' Currency',
            ]);
        });
    }
}
