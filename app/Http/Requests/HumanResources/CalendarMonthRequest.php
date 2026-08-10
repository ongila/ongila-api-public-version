<?php

namespace App\Http\Requests\HumanResources;

use Illuminate\Foundation\Http\FormRequest;

class CalendarMonthRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(['date' => $this->route('date')]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['date' => ['required', 'date_format:Y-m-d']];
    }
}
