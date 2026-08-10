<?php

namespace App\Http\Controllers\Api\HumanResources;

use App\Http\Controllers\Controller;
use App\Http\Requests\HumanResources\CalendarDayUpdateRequest;
use App\Http\Requests\HumanResources\CalendarMonthRequest;
use App\Http\Requests\HumanResources\CalendarViewRequest;
use App\Http\Resources\HumanResources\YearlyCalendarResource;
use App\Services\HumanResources\YearlyCalendarService;
use Illuminate\Http\JsonResponse;

class YearlyCalendarController extends Controller
{
    public function __construct(private YearlyCalendarService $service)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json(['data' => $this->service->years()]);
    }

    public function show(CalendarViewRequest $request, int $year)
    {
        $data = $request->validated();
        $month = isset($data['month'])
            ? (int) $data['month']
            : null;

        return YearlyCalendarResource::collection(
            $this->service->year($year, $month)
        );
    }

    public function month(CalendarMonthRequest $request, string $date): JsonResponse
    {
        return response()->json(['data' => $this->service->month($request->validated()['date'])]);
    }

    public function generate(int $year)
    {
        return YearlyCalendarResource::collection($this->service->generate($year))
            ->response()
            ->setStatusCode(201);
    }

    public function update(CalendarDayUpdateRequest $request, int $calendarDay)
    {
        return YearlyCalendarResource::collection(
            $this->service->updateDay($calendarDay, $request->validated())
        );
    }
}
