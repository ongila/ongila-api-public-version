<?php

namespace Tests\Feature;

use App\Models\Finance\Currency;
use App\Models\Inventory\Category;
use App\Models\Inventory\Company;
use App\Models\Inventory\ProductStock;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InventoryCatalogApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_crud_supports_normalization_search_and_field_selection(): void
    {
        Sanctum::actingAs(User::factory()->create());
        [$category, $unit] = $this->catalogReferences();

        $response = $this->postJson('/api/organization/wh/product', [
            'name' => 'Ergonomic Chair',
            'model' => 'CHAIR-01',
            'code' => ' prd-001 ',
            'expiration_days' => 0,
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'currency_code' => 'uzs',
            'price' => 1500000,
            'buy_price' => 1100000,
            'min_stock' => 2,
            'max_stock' => 20,
            'is_published' => true,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.code', 'PRD-001')
            ->assertJsonPath('data.currency_code', 'UZS');

        $list = $this->getJson('/api/organization/wh/product?pagination=0&s=Chair&only=id,name,price')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure(['data' => [['id', 'name', 'price']]]);
        $this->assertCount(3, $list->json('data.0'));
        $this->assertArrayNotHasKey('currency_code', $list->json('data.0'));

        $invalid = [
            'name' => 'Invalid Stock Limits',
            'expiration_days' => 0,
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'currency_code' => 'UZS',
            'price' => 100,
            'min_stock' => 20,
            'max_stock' => 2,
        ];
        $this->postJson('/api/organization/wh/product', $invalid)
            ->assertStatus(422)
            ->assertJsonStructure(['errors' => ['max_stock']]);
    }

    public function test_category_cycles_and_stocked_item_deletions_are_blocked(): void
    {
        Sanctum::actingAs(User::factory()->create());
        [$category, $unit] = $this->catalogReferences();
        $child = Category::factory()->create(['parent_id' => $category->id]);

        $this->putJson("/api/organization/wh/category/{$category->id}", [
            'parent_id' => $child->id,
            'double_unit' => false,
            'translations' => [['language_code' => 'en', 'name' => 'Parent']],
        ])->assertStatus(409)->assertJsonPath('error_code', 'category_cycle');

        $company = Company::factory()->create();
        $warehouse = Warehouse::factory()->create(['company_id' => $company->id]);
        $product = \App\Models\Inventory\Product::factory()->create([
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'currency_code' => 'UZS',
        ]);
        ProductStock::factory()->create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'stock' => 4,
            'reserved' => 1,
        ]);

        $this->deleteJson("/api/organization/wh/product/{$product->id}")
            ->assertStatus(409)->assertJsonPath('error_code', 'product_has_stock');
        $this->deleteJson("/api/organization/wh/warehouse/{$warehouse->id}")
            ->assertStatus(409)->assertJsonPath('error_code', 'warehouse_has_stock');
    }

    public function test_inventory_endpoints_require_authentication(): void
    {
        $this->getJson('/api/organization/wh/product')->assertUnauthorized();
    }

    private function catalogReferences(): array
    {
        Currency::factory()->create(['code' => 'UZS']);

        return [Category::factory()->create(), Unit::factory()->create()];
    }
}
