<?php

namespace App\Models\HumanResources;

use App\Models\Concerns\TracksActor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shift extends Model
{
    use HasFactory;
    use SoftDeletes;
    use TracksActor;

    protected $table = 'hr_shifts';

    protected $fillable = [
        'code',
        'name',
        'description',
        'status_id',
        'from',
        'to',
        'lunch_from',
        'lunch_to',
        'count_overtime',
    ];

    protected $casts = ['count_overtime' => 'boolean'];
}
