<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\CurrencyRateRequest;
use App\Http\Requests\Finance\LastExchangeRateRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Resources\Finance\CurrencyRateResource;
use App\Services\Finance\CurrencyRateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CurrencyRateController extends Controller
{
    public function __construct(private CurrencyRateService $service)
    {
    }

    public function index(IndexRequest $request)
    {
        return CurrencyRateResource::collection($this->service->index($request->validated()));
    }

    public function store(CurrencyRateRequest $request)
    {
        return CurrencyRateResource::make($this->service->create($request->validated()))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $currencyRate): CurrencyRateResource
    {
        return CurrencyRateResource::make($this->service->show($currencyRate));
    }

    public function update(CurrencyRateRequest $request, int $currencyRate): CurrencyRateResource
    {
        return CurrencyRateResource::make($this->service->update($currencyRate, $request->validated()));
    }

    public function destroy(int $currencyRate): Response
    {
        $this->service->delete($currencyRate);

        return response()->noContent();
    }

    public function latest(LastExchangeRateRequest $request): JsonResponse
    {
        $data = $request->validated();

        return response()->json([
            'data' => $this->service->latest(
                $data['from_currency'],
                $data['to_currency'],
                $data['date'] ?? null
            ),
        ]);
    }

    public function current(): JsonResponse
    {
        return response()->json(['data' => $this->service->currentForBase()]);
    }
}
