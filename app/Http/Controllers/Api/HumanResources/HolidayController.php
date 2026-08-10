<?php

namespace App\Http\Controllers\Api\HumanResources;

use App\Http\Controllers\Controller;
use App\Http\Requests\HumanResources\HolidayRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Resources\HumanResources\HolidayResource;
use App\Services\HumanResources\HolidayService;
use Illuminate\Http\Response;

class HolidayController extends Controller
{
    public function __construct(private HolidayService $service)
    {
    }

    public function index(IndexRequest $request)
    {
        return HolidayResource::collection($this->service->index($request->validated()));
    }

    public function store(HolidayRequest $request)
    {
        return HolidayResource::make($this->service->create($request->validated()))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $holiday): HolidayResource
    {
        return HolidayResource::make($this->service->show($holiday));
    }

    public function update(HolidayRequest $request, int $holiday): HolidayResource
    {
        return HolidayResource::make($this->service->update($holiday, $request->validated()));
    }

    public function destroy(int $holiday): Response
    {
        $this->service->delete($holiday);

        return response()->noContent();
    }
}
