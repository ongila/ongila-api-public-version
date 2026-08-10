<?php

namespace Tests\Feature;

use App\Models\HumanResources\YearlyCalendar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class HumanResourcesCalendarApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_shift_holiday_and_yearly_calendar_workflow(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/organization/hr/shift', [
            'code' => 'DAY',
            'name' => 'Day Shift',
            'status_id' => 1,
            'from' => '09:00',
            'to' => '18:00',
            'lunch_from' => '13:00',
            'lunch_to' => '14:00',
            'count_overtime' => true,
        ])->assertCreated()->assertJsonPath('data.code', 'DAY');

        $holiday = $this->postJson('/api/organization/hr/holiday', [
            'date' => '01-01',
            'status_id' => 1,
            'translations' => [['language_code' => 'en', 'name' => 'New Year']],
        ])->assertCreated()->json('data');

        $this->putJson('/api/organization/hr/generate-calendar/2028')
            ->assertCreated()
            ->assertJsonCount(366, 'data')
            ->assertJsonPath('data.0.calendar_date', '2028-01-01')
            ->assertJsonPath('data.0.holiday_id', $holiday['id']);

        $this->putJson("/api/organization/hr/holiday/{$holiday['id']}", [
            'date' => '01-02',
            'status_id' => 1,
            'translations' => [['language_code' => 'en', 'name' => 'Moved Holiday']],
        ])->assertStatus(409)->assertJsonPath('error_code', 'holiday_date_in_use');

        $this->putJson('/api/organization/hr/generate-calendar/2028')
            ->assertStatus(409)
            ->assertJsonPath('error_code', 'calendar_exists');

        $summary = $this->getJson('/api/organization/hr/get-month/2028-01-15')
            ->assertOk()
            ->assertJsonPath('data.days', 31)
            ->assertJsonPath('data.holidays', 1)
            ->json('data');

        $this->assertSame($summary['days'], $summary['work_days'] + $summary['non_work_days']);

        $weekday = YearlyCalendar::query()
            ->whereYear('calendar_date', 2028)
            ->where('is_workday', true)
            ->firstOrFail();

        $this->putJson("/api/organization/hr/yearly-calendar/{$weekday->id}", [
            'is_workday' => false,
        ])->assertOk()->assertJsonCount(366, 'data');

        $this->assertDatabaseHas('yearly_calendars', [
            'id' => $weekday->id,
            'is_workday' => false,
            'workday_sequence' => null,
        ]);

        $this->getJson('/api/organization/hr/yearly-calendar/2028?month=13')
            ->assertStatus(422);
        $this->getJson('/api/organization/hr/get-month/not-a-date')
            ->assertStatus(422);
    }
}
