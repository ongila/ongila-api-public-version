<?php

namespace App\Http\Requests\HumanResources;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CalendarDayUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_workday' => ['nullable', 'boolean', 'required_without:holiday_id'],
            'holiday_id' => [
                'nullable',
                'integer',
                Rule::exists('hr_holidays', 'id')->where('status_id', 1),
            ],
        ];
    }
}
