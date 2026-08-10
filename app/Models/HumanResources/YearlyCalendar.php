<?php

namespace App\Models\HumanResources;

use App\Models\Concerns\TracksActor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YearlyCalendar extends Model
{
    use HasFactory;
    use TracksActor;

    protected $table = 'yearly_calendars';

    protected $fillable = [
        'calendar_date',
        'holiday_id',
        'is_weekend',
        'is_workday',
        'workday_sequence',
    ];

    protected $casts = [
        'calendar_date' => 'date:Y-m-d',
        'is_weekend' => 'boolean',
        'is_workday' => 'boolean',
    ];

    public function holiday(): BelongsTo
    {
        return $this->belongsTo(Holiday::class);
    }
}
