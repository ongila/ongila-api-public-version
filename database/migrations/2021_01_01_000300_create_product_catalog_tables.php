<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCatalogTables extends Migration
{
    public function up(): void
    {
        Schema::create('wh_products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('name_eng', 255)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('code', 100)->nullable()->unique();
            $table->string('article', 100)->nullable();
            $table->unsignedInteger('expiration_days')->default(0);
            $table->foreignId('category_id')->constrained('wh_categories')->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('wh_units')->restrictOnDelete();
            $table->string('currency_code', 8);
            $table->decimal('price', 20, 4)->default(0);
            $table->decimal('buy_price', 20, 4)->default(0);
            $table->decimal('package_qty', 20, 4)->nullable();
            $table->decimal('min_stock', 20, 4)->nullable();
            $table->decimal('max_stock', 20, 4)->nullable();
            $table->decimal('weight', 20, 4)->nullable();
            $table->string('dimensions', 100)->nullable();
            $table->string('capacity', 100)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_detail')->default(false);
            $table->boolean('is_published')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['category_id', 'unit_id', 'currency_code']);
            $table->foreign('currency_code')->references('code')->on('fi_currencies')->restrictOnDelete();
        });

        Schema::create('wh_product_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('wh_products')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained('wh_warehouses')->restrictOnDelete();
            $table->decimal('stock', 20, 4)->default(0);
            $table->decimal('reserved', 20, 4)->default(0);
            $table->unique(['product_id', 'warehouse_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wh_product_stock');
        Schema::dropIfExists('wh_products');
    }
}
