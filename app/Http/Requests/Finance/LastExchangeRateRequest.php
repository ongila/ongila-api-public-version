<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LastExchangeRateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $normalized = [];

        foreach (['from_currency', 'to_currency'] as $field) {
            if ($this->has($field)) {
                $normalized[$field] = strtoupper(trim((string) $this->input($field)));
            }
        }

        $this->merge($normalized);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_currency' => [
                'required',
                'string',
                'max:8',
                Rule::exists('fi_currencies', 'code')->whereNull('deleted_at'),
            ],
            'to_currency' => [
                'required',
                'string',
                'max:8',
                'different:from_currency',
                Rule::exists('fi_currencies', 'code')->whereNull('deleted_at'),
            ],
            'date' => ['nullable', 'date'],
        ];
    }
}
