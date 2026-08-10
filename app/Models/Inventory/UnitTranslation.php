<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;

class UnitTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'wh_unit_translations';

    protected $fillable = ['language_code', 'name', 'short_name'];
}
