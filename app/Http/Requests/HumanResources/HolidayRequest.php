<?php

namespace App\Http\Requests\HumanResources;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HolidayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $holiday = $this->route('holiday');
        $holidayId = is_object($holiday) ? $holiday->getKey() : $holiday;

        return [
            'date' => ['required', 'date_format:m-d', Rule::unique('hr_holidays', 'date')->ignore($holidayId)],
            'status_id' => ['nullable', 'integer', 'in:0,1'],
            'translations' => ['required', 'array', 'min:1'],
            'translations.*.id' => ['nullable', 'integer'],
            'translations.*.language_code' => ['required', 'string', 'max:10', 'distinct'],
            'translations.*.name' => ['required', 'string', 'max:150'],
        ];
    }
}
