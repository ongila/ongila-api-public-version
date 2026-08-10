<?php

namespace App\Services\Inventory;

use App\Models\Inventory\Product;
use App\Support\DomainConflictException;
use App\Support\QueryOptions;

class ProductService
{
    private const RELATIONS = [
        'category.translations',
        'unit.translations',
        'currency.translations',
    ];

    public function __construct(private QueryOptions $queryOptions)
    {
    }

    public function index(array $options)
    {
        $query = Product::query()
            ->with(self::RELATIONS)
            ->withSum('stocks', 'stock')
            ->withSum('stocks', 'reserved');

        $this->queryOptions->apply(
            $query,
            $options,
            ['name', 'name_eng', 'model', 'code', 'article', 'description'],
            ['category_id', 'unit_id', 'currency_code', 'is_published'],
            ['id', 'name', 'model', 'code', 'price', 'buy_price', 'created_at', 'updated_at']
        );

        return $this->queryOptions->result($query, $options);
    }

    public function create(array $data): Product
    {
        return $this->load(Product::query()->create($this->normalize($data)));
    }

    public function show(int $id): Product
    {
        return $this->load(Product::query()->findOrFail($id));
    }

    public function update(int $id, array $data): Product
    {
        $product = Product::query()->findOrFail($id);
        $product->update($this->normalize($data));

        return $this->load($product);
    }

    public function delete(int $id): void
    {
        $product = Product::query()->findOrFail($id);
        $hasStock = $product->stocks()
            ->where(function ($query) {
                $query->where('stock', '>', 0)->orWhere('reserved', '>', 0);
            })
            ->exists();

        if ($hasStock) {
            throw new DomainConflictException(
                'The product has stock or reservations and cannot be deleted.',
                'product_has_stock'
            );
        }

        $product->delete();
    }

    private function normalize(array $data): array
    {
        $data['currency_code'] = strtoupper($data['currency_code']);
        $data['code'] = isset($data['code']) ? strtoupper($data['code']) : null;
        $data['buy_price'] = $data['buy_price'] ?? 0;

        return $data;
    }

    private function load(Product $product): Product
    {
        return $product->refresh()->load(self::RELATIONS)
            ->loadSum('stocks', 'stock')
            ->loadSum('stocks', 'reserved');
    }
}
