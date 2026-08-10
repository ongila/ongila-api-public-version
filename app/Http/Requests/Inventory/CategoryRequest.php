<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('wh_categories', 'id')->whereNull('deleted_at'),
            ],
            'double_unit' => ['nullable', 'boolean'],
            'translations' => ['required', 'array', 'min:1'],
            'translations.*.id' => ['nullable', 'integer'],
            'translations.*.language_code' => ['required', 'string', 'max:10', 'distinct'],
            'translations.*.name' => ['required', 'string', 'max:150'],
        ];
    }
}
