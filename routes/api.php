<?php

use App\Http\Controllers\Api\Finance\CurrencyController;
use App\Http\Controllers\Api\Finance\CurrencyRateController;
use App\Http\Controllers\Api\HumanResources\HolidayController;
use App\Http\Controllers\Api\HumanResources\ShiftController;
use App\Http\Controllers\Api\HumanResources\YearlyCalendarController;
use App\Http\Controllers\Api\Inventory\CategoryController;
use App\Http\Controllers\Api\Inventory\ProductController;
use App\Http\Controllers\Api\Inventory\UnitController;
use App\Http\Controllers\Api\Inventory\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('health', fn () => response()->json(['status' => 'ok']));

Route::middleware('auth:sanctum')->prefix('organization')->name('organization.')->group(function () {
    Route::prefix('wh')->name('wh.')->group(function () {
        Route::apiResource('category', CategoryController::class);
        Route::apiResource('unit', UnitController::class);
        Route::apiResource('warehouse', WarehouseController::class);
        Route::apiResource('product', ProductController::class);
    });

    Route::prefix('hr')->name('hr.')->group(function () {
        Route::apiResource('shift', ShiftController::class);
        Route::apiResource('holiday', HolidayController::class);
        Route::get('yearly-calendar', [YearlyCalendarController::class, 'index'])->name('calendar.index');
        Route::get('yearly-calendar/{year}', [YearlyCalendarController::class, 'show'])
            ->where('year', '[0-9]+')
            ->name('calendar.show');
        Route::get('get-month/{date}', [YearlyCalendarController::class, 'month'])->name('calendar.month');
        Route::put('yearly-calendar/{calendarDay}', [YearlyCalendarController::class, 'update'])
            ->where('calendarDay', '[0-9]+')
            ->name('calendar.update');
        Route::put('generate-calendar/{year}', [YearlyCalendarController::class, 'generate'])
            ->where('year', '[0-9]+')
            ->name('calendar.generate');
    });

    Route::prefix('fi')->name('fi.')->group(function () {
        Route::apiResource('currency', CurrencyController::class);
    });

    Route::apiResource('currency-rate', CurrencyRateController::class);
    Route::get('exchange-rate', [CurrencyRateController::class, 'current'])->name('exchange-rate');
    Route::get('last-exchange-rate', [CurrencyRateController::class, 'latest'])->name('last-exchange-rate');
});
