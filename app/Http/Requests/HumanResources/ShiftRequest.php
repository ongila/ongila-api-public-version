<?php

namespace App\Http\Requests\HumanResources;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShiftRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge(['code' => strtoupper(trim((string) $this->input('code')))]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $shift = $this->route('shift');
        $shiftId = is_object($shift) ? $shift->getKey() : $shift;

        return [
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('hr_shifts', 'code')->ignore($shiftId),
            ],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'status_id' => ['nullable', 'integer', 'in:0,1'],
            'from' => ['required', 'date_format:H:i'],
            'to' => ['required', 'date_format:H:i', 'different:from'],
            'lunch_from' => ['nullable', 'date_format:H:i', 'required_with:lunch_to'],
            'lunch_to' => ['nullable', 'date_format:H:i', 'required_with:lunch_from', 'after:lunch_from'],
            'count_overtime' => ['nullable', 'boolean'],
        ];
    }
}
