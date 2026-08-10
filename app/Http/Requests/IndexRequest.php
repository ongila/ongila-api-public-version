<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $normalized = [];

        foreach (['code', 'currency_code', 'from_currency', 'to_currency'] as $field) {
            if ($this->has($field)) {
                $normalized[$field] = strtoupper(trim((string) $this->input($field)));
            }
        }

        if ($this->has('language_code')) {
            $normalized['language_code'] = strtolower(trim((string) $this->input('language_code')));
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
            'pagination' => ['nullable', 'boolean'],
            'page' => ['nullable', 'integer', 'min:1'],
            'rows' => ['nullable', 'integer', 'min:1', 'max:100'],
            's' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'string', 'max:64'],
            'language_code' => ['nullable', 'string', 'max:10'],
            'code' => ['nullable', 'string', 'max:100'],
            'only' => ['nullable', 'string', 'max:500'],
            'except' => ['nullable', 'string', 'max:500'],
            'parent_id' => ['nullable', 'integer'],
            'category_id' => ['nullable', 'integer'],
            'unit_id' => ['nullable', 'integer'],
            'currency_code' => ['nullable', 'string', 'max:8'],
            'company_id' => ['nullable', 'integer'],
            'type' => ['nullable', 'integer'],
            'is_market_visible' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'status_id' => ['nullable', 'integer'],
            'from_currency' => ['nullable', 'string', 'max:8'],
            'to_currency' => ['nullable', 'string', 'max:8'],
        ];
    }
}
