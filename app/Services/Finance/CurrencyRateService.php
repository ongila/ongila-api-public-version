<?php

namespace App\Services\Finance;

use App\Models\Finance\Currency;
use App\Models\Finance\CurrencyRate;
use App\Support\DomainConflictException;
use App\Support\QueryOptions;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class CurrencyRateService
{
    public function __construct(private QueryOptions $queryOptions)
    {
    }

    public function index(array $options)
    {
        $query = CurrencyRate::query();

        $this->queryOptions->apply(
            $query,
            $options,
            ['from_currency', 'to_currency'],
            ['from_currency', 'to_currency'],
            ['id', 'from_currency', 'to_currency', 'value', 'begin_date', 'end_date', 'created_at']
        );

        return $this->queryOptions->result($query, $options);
    }

    public function create(array $data): CurrencyRate
    {
        return DB::transaction(function () use ($data) {
            $attributes = $this->normalize($data);
            $this->assertStartAvailable($attributes);
            $rate = CurrencyRate::query()->create($attributes);
            $this->rebuildPairPeriods($attributes['from_currency'], $attributes['to_currency']);

            return $rate->refresh();
        });
    }

    public function show(int $id): CurrencyRate
    {
        return CurrencyRate::query()->findOrFail($id);
    }

    public function update(int $id, array $data): CurrencyRate
    {
        return DB::transaction(function () use ($id, $data) {
            $rate = CurrencyRate::query()->findOrFail($id);
            $oldPair = [$rate->from_currency, $rate->to_currency];
            $attributes = $this->normalize($data);
            $this->assertStartAvailable($attributes, $rate->id);
            $rate->update($attributes);
            $this->rebuildPairPeriods($oldPair[0], $oldPair[1]);
            $this->rebuildPairPeriods($attributes['from_currency'], $attributes['to_currency']);

            return $rate->refresh();
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $rate = CurrencyRate::query()->findOrFail($id);
            $pair = [$rate->from_currency, $rate->to_currency];
            $rate->delete();
            $this->rebuildPairPeriods($pair[0], $pair[1]);
        });
    }

    public function latest(string $from, string $to, ?string $date = null): array
    {
        $from = strtoupper($from);
        $to = strtoupper($to);
        $rate = $this->findPairRate($from, $to, $date ? Carbon::parse($date) : now());

        if (! $rate) {
            return [
                'from_currency' => $from,
                'from_amount' => null,
                'to_currency' => $to,
                'to_amount' => null,
            ];
        }

        $isDirect = $rate->from_currency === $from;

        return [
            'from_currency' => $from,
            'from_amount' => $isDirect ? 1.0 : (float) $rate->value,
            'to_currency' => $to,
            'to_amount' => $isDirect ? (float) $rate->value : 1.0,
        ];
    }

    public function currentForBase(): array
    {
        $base = strtoupper((string) config('erp.base_currency'));
        $codes = CurrencyRate::query()
            ->where('begin_date', '<=', now())
            ->where(function ($active) {
                $active->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->where(function ($pair) use ($base) {
                $pair->where('from_currency', $base)->orWhere('to_currency', $base);
            })
            ->get(['from_currency', 'to_currency'])
            ->flatMap(fn (CurrencyRate $rate) => [$rate->from_currency, $rate->to_currency])
            ->reject(fn (string $code) => $code === $base)
            ->unique()
            ->values();

        return $codes->map(function (string $code) use ($base) {
            $current = $this->rate($base, $code);
            $previous = $this->previousRate($base, $code);

            return [
                'from_currency' => $base,
                'to_currency' => $code,
                'value' => $current,
                'increase_value' => $previous === null ? null : round($current - $previous, 8),
            ];
        })->all();
    }

    public function rate(string $from, string $to, $date = null): float
    {
        $from = strtoupper(trim($from));
        $to = strtoupper(trim($to));
        $date = $date instanceof CarbonInterface ? $date : Carbon::parse($date ?? now());

        if ($from === $to) {
            return 1.0;
        }

        $pair = $this->findPairRate($from, $to, $date);

        if ($pair) {
            return $pair->from_currency === $from
                ? (float) $pair->value
                : (float) (1 / $pair->value);
        }

        $base = strtoupper((string) config('erp.base_currency'));

        if ($from !== $base && $to !== $base) {
            $toBase = $this->findPairRate($from, $base, $date);
            $fromBase = $this->findPairRate($base, $to, $date);

            if ($toBase && $fromBase) {
                return $this->pairValue($toBase, $from) * $this->pairValue($fromBase, $base);
            }
        }

        throw new DomainConflictException(
            "No exchange rate is available for {$from}/{$to} on {$date->toDateString()}.",
            'currency_rate_not_found'
        );
    }

    private function normalize(array $data): array
    {
        $base = strtoupper((string) config('erp.base_currency'));
        $toCurrency = strtoupper($data['to_currency']);
        $value = (float) $data['value'];
        $mainValue = isset($data['main_value']) ? (float) $data['main_value'] : null;

        if (! Currency::query()->where('code', $base)->exists()) {
            throw new DomainConflictException(
                'The configured base currency does not exist.',
                'base_currency_missing'
            );
        }

        if ($toCurrency === $base) {
            throw new DomainConflictException(
                'The target currency must differ from the base currency.',
                'same_currency'
            );
        }

        if ($value === 1.0 && $mainValue && $mainValue > 0) {
            return [
                'from_currency' => $toCurrency,
                'to_currency' => $base,
                'value' => $mainValue,
                'begin_date' => Carbon::parse($data['begin_date'] ?? now()),
                'end_date' => null,
            ];
        }

        if ($mainValue === 1.0 || $mainValue === null) {
            return [
                'from_currency' => $base,
                'to_currency' => $toCurrency,
                'value' => $value,
                'begin_date' => Carbon::parse($data['begin_date'] ?? now()),
                'end_date' => null,
            ];
        }

        throw new DomainConflictException(
            'Either value or main_value must equal one.',
            'invalid_currency_ratio'
        );
    }

    private function assertStartAvailable(array $attributes, ?int $exceptId = null): void
    {
        $query = $this->pairQuery($attributes['from_currency'], $attributes['to_currency']);

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        if ($query->where('begin_date', $attributes['begin_date'])->exists()) {
            throw new DomainConflictException(
                'A rate for this currency pair already starts at the submitted time.',
                'currency_rate_date_exists'
            );
        }
    }

    private function rebuildPairPeriods(string $from, string $to): void
    {
        $rates = $this->pairQuery($from, $to)->oldest('begin_date')->get();

        foreach ($rates as $index => $rate) {
            $next = $rates->get($index + 1);
            $endDate = $next ? $next->begin_date->copy()->subSecond() : null;

            if ($rate->end_date?->getTimestamp() !== $endDate?->getTimestamp()) {
                $rate->end_date = $endDate;
                $rate->save();
            }
        }
    }

    private function pairQuery(string $from, string $to)
    {
        return CurrencyRate::query()->where(function ($pair) use ($from, $to) {
            $pair->where(function ($direct) use ($from, $to) {
                $direct->where('from_currency', $from)->where('to_currency', $to);
            })->orWhere(function ($reverse) use ($from, $to) {
                $reverse->where('from_currency', $to)->where('to_currency', $from);
            });
        });
    }

    private function findPairRate(string $from, string $to, CarbonInterface $date): ?CurrencyRate
    {
        return CurrencyRate::query()
            ->where(function ($pair) use ($from, $to) {
                $pair->where(function ($direct) use ($from, $to) {
                    $direct->where('from_currency', $from)->where('to_currency', $to);
                })->orWhere(function ($reverse) use ($from, $to) {
                    $reverse->where('from_currency', $to)->where('to_currency', $from);
                });
            })
            ->where('begin_date', '<=', $date)
            ->where(function ($active) use ($date) {
                $active->whereNull('end_date')->orWhere('end_date', '>=', $date);
            })
            ->latest('begin_date')
            ->first();
    }

    private function pairValue(CurrencyRate $rate, string $requestedFrom): float
    {
        return $rate->from_currency === $requestedFrom
            ? (float) $rate->value
            : (float) (1 / $rate->value);
    }

    private function previousRate(string $from, string $to): ?float
    {
        $current = $this->findPairRate($from, $to, now());

        if (! $current) {
            return null;
        }

        $previous = $this->pairQuery($from, $to)
            ->where('begin_date', '<', $current->begin_date)
            ->latest('begin_date')
            ->first();

        return $previous ? $this->pairValue($previous, $from) : null;
    }
}
