<?php

namespace App\Http\Controllers\Api\HumanResources;

use App\Http\Controllers\Controller;
use App\Http\Requests\HumanResources\ShiftRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Resources\HumanResources\ShiftResource;
use App\Services\HumanResources\ShiftService;
use Illuminate\Http\Response;

class ShiftController extends Controller
{
    public function __construct(private ShiftService $service)
    {
    }

    public function index(IndexRequest $request)
    {
        return ShiftResource::collection($this->service->index($request->validated()));
    }

    public function store(ShiftRequest $request)
    {
        return ShiftResource::make($this->service->create($request->validated()))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $shift): ShiftResource
    {
        return ShiftResource::make($this->service->show($shift));
    }

    public function update(ShiftRequest $request, int $shift): ShiftResource
    {
        return ShiftResource::make($this->service->update($shift, $request->validated()));
    }

    public function destroy(int $shift): Response
    {
        $this->service->delete($shift);

        return response()->noContent();
    }
}
