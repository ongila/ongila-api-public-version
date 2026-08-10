<?php

namespace Tests\Feature;

use App\Models\Finance\Currency;
use App\Models\Finance\CurrencyRate;
use App\Models\User;
use App\Services\Finance\CurrencyRateService;
use App\Support\DomainConflictException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CurrencyManagementApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_currency_master_data_normalizes_codes_and_protects_the_base(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $base = $this->postJson('/api/organization/fi/currency', [
            'code' => 'uzs',
            'translations' => [['language_code' => 'en', 'name' => 'Uzbekistani Som']],
        ])->assertCreated()->assertJsonPath('data.code', 'UZS')->json('data');

        $currency = $this->postJson('/api/organization/fi/currency', [
            'code' => ' usd ',
            'translations' => [['language_code' => 'en', 'name' => 'US Dollar']],
        ])->assertCreated()->assertJsonPath('data.code', 'USD')->json('data');

        $this->getJson('/api/organization/fi/currency?pagination=0&code=usd')
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.code', 'USD');

        $this->putJson("/api/organization/fi/currency/{$currency['id']}", [
            'code' => 'USN',
            'translations' => [['language_code' => 'en', 'name' => 'Test Dollar']],
        ])->assertOk()->assertJsonPath('data.code', 'USN');

        $this->deleteJson("/api/organization/fi/currency/{$base['id']}")
            ->assertStatus(409)->assertJsonPath('error_code', 'base_currency_protected');
        $this->deleteJson("/api/organization/fi/currency/{$currency['id']}")
            ->assertStatus(204);
    }

    public function test_rate_history_reverse_lookup_and_cross_rate_calculation(): void
    {
        Sanctum::actingAs(User::factory()->create());
        foreach (['UZS', 'USD', 'EUR'] as $code) {
            Currency::factory()->create(['code' => $code]);
        }

        $first = $this->postJson('/api/organization/currency-rate', [
            'to_currency' => 'usd',
            'value' => 0.00008,
            'main_value' => 1,
            'begin_date' => '2025-01-01 00:00:00',
        ])->assertCreated()->json('data');

        $second = $this->postJson('/api/organization/currency-rate', [
            'to_currency' => 'USD',
            'value' => 0.000075,
            'main_value' => 1,
            'begin_date' => '2025-02-01 00:00:00',
        ])->assertCreated()->json('data');

        $this->postJson('/api/organization/currency-rate', [
            'to_currency' => 'EUR',
            'value' => 0.00007,
            'main_value' => 1,
            'begin_date' => '2025-01-01 00:00:00',
        ])->assertCreated();

        $this->putJson("/api/organization/currency-rate/{$second['id']}", [
            'to_currency' => 'USD',
            'value' => 0.000075,
            'main_value' => 1,
            'begin_date' => '2025-03-01 00:00:00',
        ])->assertOk();

        $this->assertSame(
            '2025-02-28 23:59:59',
            CurrencyRate::query()->findOrFail($first['id'])->end_date->format('Y-m-d H:i:s')
        );
        $this->assertNull(CurrencyRate::query()->findOrFail($second['id'])->end_date);

        $this->postJson('/api/organization/currency-rate', [
            'to_currency' => 'USD',
            'value' => 0.00007,
            'main_value' => 1,
            'begin_date' => '2025-03-01 00:00:00',
        ])->assertStatus(409)->assertJsonPath('error_code', 'currency_rate_date_exists');

        $this->getJson(
            '/api/organization/last-exchange-rate?from_currency=USD&to_currency=UZS&date=2025-03-15'
        )->assertOk()
            ->assertJsonPath('data.from_amount', 0.000075)
            ->assertJsonPath('data.to_amount', 1);

        $rate = app(CurrencyRateService::class)->rate('USD', 'EUR', '2025-03-15');
        $this->assertEqualsWithDelta(0.93333333, $rate, 0.00000001);

        $this->deleteJson("/api/organization/currency-rate/{$second['id']}")->assertStatus(204);
        $this->assertNull(CurrencyRate::query()->findOrFail($first['id'])->end_date);
    }

    public function test_missing_rate_and_same_currency_configuration_fail_safely(): void
    {
        Sanctum::actingAs(User::factory()->create());
        foreach (['UZS', 'USD', 'EUR'] as $code) {
            Currency::factory()->create(['code' => $code]);
        }

        $this->postJson('/api/organization/currency-rate', [
            'to_currency' => 'UZS',
            'value' => 1,
            'main_value' => 1,
        ])->assertStatus(409)->assertJsonPath('error_code', 'same_currency');

        $this->expectException(DomainConflictException::class);
        app(CurrencyRateService::class)->rate('USD', 'EUR', '2025-03-01');
    }
}
