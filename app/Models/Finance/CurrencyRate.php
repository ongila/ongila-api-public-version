<?php

namespace App\Models\Finance;

use App\Models\Concerns\TracksActor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model
{
    use HasFactory;
    use TracksActor;

    protected $fillable = [
        'from_currency',
        'to_currency',
        'value',
        'begin_date',
        'end_date',
    ];

    protected $casts = [
        'value' => 'float',
        'begin_date' => 'datetime',
        'end_date' => 'datetime',
    ];
}
