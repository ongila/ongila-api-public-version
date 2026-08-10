<?php

namespace App\Services\Finance;

use App\Models\Finance\Currency;
use App\Models\Finance\CurrencyRate;
use App\Support\DomainConflictException;
use App\Support\QueryOptions;
use App\Support\TranslationSyncService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CurrencyService
{
    public function __construct(
        private QueryOptions $queryOptions,
        private TranslationSyncService $translations
    ) {
    }

    public function index(array $options)
    {
        $query = Currency::query()->with('translations')->withCount('products');

        $this->queryOptions->apply(
            $query,
            $options,
            ['code'],
            ['code'],
            ['id', 'code', 'created_at', 'updated_at'],
            ['translations' => ['name']]
        );

        return $this->queryOptions->result($query, $options);
    }

    public function create(array $data): Currency
    {
        return DB::transaction(function () use ($data) {
            $attributes = Arr::except($data, 'translations');
            $attributes['code'] = strtoupper($attributes['code']);
            $currency = Currency::query()->create($attributes);
            $this->translations->sync($currency, $data['translations']);

            return $this->load($currency);
        });
    }

    public function show(int $id): Currency
    {
        return $this->load(Currency::query()->findOrFail($id));
    }

    public function update(int $id, array $data): Currency
    {
        return DB::transaction(function () use ($id, $data) {
            $currency = Currency::query()->findOrFail($id);
            $oldCode = $currency->code;
            $newCode = strtoupper($data['code']);
            $base = strtoupper((string) config('erp.base_currency'));

            if ($oldCode === $base && $newCode !== $oldCode) {
                throw new DomainConflictException(
                    'The configured base currency cannot be renamed.',
                    'base_currency_protected'
                );
            }

            if ($oldCode !== $newCode
                && ($this->isReferenced($oldCode) || $currency->products()->withTrashed()->exists())) {
                throw new DomainConflictException(
                    'A referenced currency code cannot be changed.',
                    'currency_code_in_use'
                );
            }

            $currency->update(['code' => $newCode]);
            $this->translations->sync($currency, $data['translations']);

            return $this->load($currency);
        });
    }

    public function delete(int $id): void
    {
        $currency = Currency::query()->findOrFail($id);

        if ($currency->code === strtoupper((string) config('erp.base_currency'))) {
            throw new DomainConflictException(
                'The configured base currency cannot be deleted.',
                'base_currency_protected'
            );
        }

        if ($currency->products()->withTrashed()->exists() || $this->isReferenced($currency->code)) {
            throw new DomainConflictException(
                'The currency is referenced by products or exchange rates and cannot be deleted.',
                'currency_in_use'
            );
        }

        $currency->delete();
    }

    private function isReferenced(string $code): bool
    {
        return CurrencyRate::query()
            ->where('from_currency', $code)
            ->orWhere('to_currency', $code)
            ->exists();
    }

    private function load(Currency $currency): Currency
    {
        return $currency->refresh()->load('translations')->loadCount('products');
    }
}
