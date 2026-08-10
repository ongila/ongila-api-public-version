<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CurrencyRequest extends FormRequest
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
        $currency = $this->route('currency');
        $currencyId = is_object($currency) ? $currency->getKey() : $currency;

        return [
            'code' => [
                'required',
                'string',
                'max:8',
                Rule::unique('fi_currencies', 'code')->ignore($currencyId),
            ],
            'translations' => ['required', 'array', 'min:1'],
            'translations.*.id' => ['nullable', 'integer'],
            'translations.*.language_code' => ['required', 'string', 'max:10', 'distinct'],
            'translations.*.name' => ['required', 'string', 'max:100'],
        ];
    }
}
