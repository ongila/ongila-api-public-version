<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CurrencyRateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('to_currency')) {
            $this->merge([
                'to_currency' => strtoupper(trim((string) $this->input('to_currency'))),
            ]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'to_currency' => [
                'required',
                'string',
                'max:8',
                Rule::exists('fi_currencies', 'code')->whereNull('deleted_at'),
            ],
            'value' => ['required', 'numeric', 'gt:0'],
            'main_value' => ['nullable', 'numeric', 'gt:0'],
            'begin_date' => ['nullable', 'date'],
        ];
    }
}
