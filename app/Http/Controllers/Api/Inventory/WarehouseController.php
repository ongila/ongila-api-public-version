<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\Inventory\WarehouseRequest;
use App\Http\Resources\Inventory\WarehouseResource;
use App\Services\Inventory\WarehouseService;
use Illuminate\Http\Response;

class WarehouseController extends Controller
{
    public function __construct(private WarehouseService $service)
    {
    }

    public function index(IndexRequest $request)
    {
        return WarehouseResource::collection($this->service->index($request->validated()));
    }

    public function store(WarehouseRequest $request)
    {
        return WarehouseResource::make($this->service->create($request->validated()))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $warehouse): WarehouseResource
    {
        return WarehouseResource::make($this->service->show($warehouse));
    }

    public function update(WarehouseRequest $request, int $warehouse): WarehouseResource
    {
        return WarehouseResource::make($this->service->update($warehouse, $request->validated()));
    }

    public function destroy(int $warehouse): Response
    {
        $this->service->delete($warehouse);

        return response()->noContent();
    }
}
