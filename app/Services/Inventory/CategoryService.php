<?php

namespace App\Services\Inventory;

use App\Models\Inventory\Category;
use App\Support\DomainConflictException;
use App\Support\QueryOptions;
use App\Support\TranslationSyncService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    public function __construct(
        private QueryOptions $queryOptions,
        private TranslationSyncService $translations
    ) {
    }

    public function index(array $options)
    {
        $query = Category::query()
            ->with(['translations', 'parent.translations'])
            ->withCount(['children', 'products']);

        $this->queryOptions->apply(
            $query,
            $options,
            [],
            ['parent_id'],
            ['id', 'parent_id', 'created_at', 'updated_at'],
            ['translations' => ['name']]
        );

        return $this->queryOptions->result($query, $options);
    }

    public function create(array $data): Category
    {
        return DB::transaction(function () use ($data) {
            $category = Category::query()->create(Arr::except($data, 'translations'));
            $this->translations->sync($category, $data['translations']);

            return $this->load($category);
        });
    }

    public function show(int $id): Category
    {
        return $this->load(Category::query()->findOrFail($id));
    }

    public function update(int $id, array $data): Category
    {
        return DB::transaction(function () use ($id, $data) {
            $category = Category::query()->findOrFail($id);
            $parentId = $data['parent_id'] ?? null;

            if ($parentId && $this->createsCycle($category, (int) $parentId)) {
                throw new DomainConflictException(
                    'A category cannot be moved below itself or one of its descendants.',
                    'category_cycle'
                );
            }

            $category->update(Arr::except($data, 'translations'));
            $this->translations->sync($category, $data['translations']);

            return $this->load($category);
        });
    }

    public function delete(int $id): void
    {
        $category = Category::query()->findOrFail($id);

        if ($category->children()->exists() || $category->products()->exists()) {
            throw new DomainConflictException(
                'The category has child categories or products and cannot be deleted.',
                'category_in_use'
            );
        }

        $category->delete();
    }

    private function createsCycle(Category $category, int $candidateParentId): bool
    {
        if ($category->id === $candidateParentId) {
            return true;
        }

        $parent = Category::query()->find($candidateParentId);

        while ($parent) {
            if ($parent->id === $category->id) {
                return true;
            }

            $parent = $parent->parent_id
                ? Category::query()->find($parent->parent_id)
                : null;
        }

        return false;
    }

    private function load(Category $category): Category
    {
        return $category->refresh()->load(['translations', 'parent.translations'])
            ->loadCount(['children', 'products']);
    }
}
