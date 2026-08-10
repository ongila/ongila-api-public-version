<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;

class CategoryTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'wh_category_translations';

    protected $fillable = ['language_code', 'name'];
}
