<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $normalized = [];

        if ($this->has('currency_code')) {
            $normalized['currency_code'] = strtoupper(trim((string) $this->input('currency_code')));
        }

        if ($this->filled('code')) {
            $normalized['code'] = strtoupper(trim((string) $this->input('code')));
        }

        $this->merge($normalized);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $product = $this->route('product');
        $productId = is_object($product) ? $product->getKey() : $product;

        return [
            'name' => ['required', 'string', 'max:255'],
            'name_eng' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:100'],
            'code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('wh_products', 'code')->ignore($productId),
            ],
            'article' => ['nullable', 'string', 'max:100'],
            'expiration_days' => ['required', 'integer', 'min:0'],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('wh_categories', 'id')->whereNull('deleted_at'),
            ],
            'unit_id' => [
                'required',
                'integer',
                Rule::exists('wh_units', 'id')->whereNull('deleted_at'),
            ],
            'currency_code' => [
                'required',
                'string',
                'max:8',
                Rule::exists('fi_currencies', 'code')->whereNull('deleted_at'),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'buy_price' => ['nullable', 'numeric', 'min:0'],
            'package_qty' => ['nullable', 'numeric', 'min:0'],
            'min_stock' => ['nullable', 'numeric', 'min:0'],
            'max_stock' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'dimensions' => ['nullable', 'string', 'max:100'],
            'capacity' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_detail' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('min_stock')
                && $this->filled('max_stock')
                && (float) $this->input('max_stock') < (float) $this->input('min_stock')) {
                $validator->errors()->add(
                    'max_stock',
                    'The max stock must be greater than or equal to the min stock.'
                );
            }
        });
    }
}
