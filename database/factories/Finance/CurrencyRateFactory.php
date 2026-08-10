<?php

namespace Database\Factories\Finance;

use App\Models\Finance\CurrencyRate;
use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyRateFactory extends Factory
{
    protected $model = CurrencyRate::class;

    public function definition(): array
    {
        return [
            'from_currency' => 'UZS',
            'to_currency' => 'USD',
            'value' => 0.00008,
            'begin_date' => now(),
            'end_date' => null,
        ];
    }
}
