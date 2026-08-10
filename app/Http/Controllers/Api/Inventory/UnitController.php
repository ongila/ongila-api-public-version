<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\Inventory\UnitRequest;
use App\Http\Resources\Inventory\UnitResource;
use App\Services\Inventory\UnitService;
use Illuminate\Http\Response;

class UnitController extends Controller
{
    public function __construct(private UnitService $service)
    {
    }

    public function index(IndexRequest $request)
    {
        return UnitResource::collection($this->service->index($request->validated()));
    }

    public function store(UnitRequest $request)
    {
        return UnitResource::make($this->service->create($request->validated()))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $unit): UnitResource
    {
        return UnitResource::make($this->service->show($unit));
    }

    public function update(UnitRequest $request, int $unit): UnitResource
    {
        return UnitResource::make($this->service->update($unit, $request->validated()));
    }

    public function destroy(int $unit): Response
    {
        $this->service->delete($unit);

        return response()->noContent();
    }
}
