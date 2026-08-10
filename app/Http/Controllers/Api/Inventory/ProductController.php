<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\Inventory\ProductRequest;
use App\Http\Resources\Inventory\ProductResource;
use App\Services\Inventory\ProductService;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function __construct(private ProductService $service)
    {
    }

    public function index(IndexRequest $request)
    {
        return ProductResource::collection($this->service->index($request->validated()));
    }

    public function store(ProductRequest $request)
    {
        return ProductResource::make($this->service->create($request->validated()))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $product): ProductResource
    {
        return ProductResource::make($this->service->show($product));
    }

    public function update(ProductRequest $request, int $product): ProductResource
    {
        return ProductResource::make($this->service->update($product, $request->validated()));
    }

    public function destroy(int $product): Response
    {
        $this->service->delete($product);

        return response()->noContent();
    }
}
