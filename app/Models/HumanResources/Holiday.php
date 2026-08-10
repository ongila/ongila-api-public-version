<?php

namespace App\Models\HumanResources;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\TracksActor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Holiday extends Model
{
    use HasFactory;
    use HasTranslations;
    use TracksActor;

    protected $table = 'hr_holidays';

    protected $fillable = ['date', 'status_id'];

    public function translationModel(): string
    {
        return HolidayTranslation::class;
    }

    public function calendarDays(): HasMany
    {
        return $this->hasMany(YearlyCalendar::class, 'holiday_id');
    }
}
