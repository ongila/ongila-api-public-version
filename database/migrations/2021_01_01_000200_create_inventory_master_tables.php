<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryMasterTables extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('wh_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('wh_categories')->restrictOnDelete();
            $table->boolean('double_unit')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('wh_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('object_id')->constrained('wh_categories')->cascadeOnDelete();
            $table->string('language_code', 10);
            $table->string('name', 150);
            $table->unique(['object_id', 'language_code']);
        });

        Schema::create('wh_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('wh_unit_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('object_id')->constrained('wh_units')->cascadeOnDelete();
            $table->string('language_code', 10);
            $table->string('name', 100);
            $table->string('short_name', 25);
            $table->unique(['object_id', 'language_code']);
        });

        Schema::create('wh_warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->string('name', 150);
            $table->unsignedTinyInteger('type')->default(1);
            $table->boolean('is_market_visible')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wh_warehouses');
        Schema::dropIfExists('wh_unit_translations');
        Schema::dropIfExists('wh_units');
        Schema::dropIfExists('wh_category_translations');
        Schema::dropIfExists('wh_categories');
        Schema::dropIfExists('companies');
    }
}
