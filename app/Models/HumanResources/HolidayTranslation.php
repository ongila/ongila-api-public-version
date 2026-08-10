<?php

namespace App\Models\HumanResources;

use Illuminate\Database\Eloquent\Model;

class HolidayTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'hr_holiday_translations';

    protected $fillable = ['language_code', 'name'];
}
