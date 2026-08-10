<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class CurrencyTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'fi_currency_translations';

    protected $fillable = ['language_code', 'name'];
}
