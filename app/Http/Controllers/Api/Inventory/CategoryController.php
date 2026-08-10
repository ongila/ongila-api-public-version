<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\Inventory\CategoryRequest;
use App\Http\Resources\Inventory\CategoryResource;
use App\Services\Inventory\CategoryService;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $service)
    {
    }

    public function index(IndexRequest $request)
    {
        return CategoryResource::collection($this->service->index($request->validated()));
    }

    public function store(CategoryRequest $request)
    {
        return CategoryResource::make($this->service->create($request->validated()))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $category): CategoryResource
    {
        return CategoryResource::make($this->service->show($category));
    }

    public function update(CategoryRequest $request, int $category): CategoryResource
    {
        return CategoryResource::make($this->service->update($category, $request->validated()));
    }

    public function destroy(int $category): Response
    {
        $this->service->delete($category);

        return response()->noContent();
    }
}
