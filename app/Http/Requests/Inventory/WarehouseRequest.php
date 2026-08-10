<?php

namespace App\Http\Requests\Inventory;

use App\Support\WarehouseType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'type' => ['nullable', 'integer', Rule::in(WarehouseType::values())],
            'is_market_visible' => ['nullable', 'boolean'],
        ];
    }
}
