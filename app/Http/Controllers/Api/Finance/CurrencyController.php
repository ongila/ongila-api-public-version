<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\CurrencyRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Resources\Finance\CurrencyResource;
use App\Services\Finance\CurrencyService;
use Illuminate\Http\Response;

class CurrencyController extends Controller
{
    public function __construct(private CurrencyService $service)
    {
    }

    public function index(IndexRequest $request)
    {
        return CurrencyResource::collection($this->service->index($request->validated()));
    }

    public function store(CurrencyRequest $request)
    {
        return CurrencyResource::make($this->service->create($request->validated()))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $currency): CurrencyResource
    {
        return CurrencyResource::make($this->service->show($currency));
    }

    public function update(CurrencyRequest $request, int $currency): CurrencyResource
    {
        return CurrencyResource::make($this->service->update($currency, $request->validated()));
    }

    public function destroy(int $currency): Response
    {
        $this->service->delete($currency);

        return response()->noContent();
    }
}
