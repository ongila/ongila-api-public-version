<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinanceReferenceTables extends Migration
{
    public function up(): void
    {
        Schema::create('fi_currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8)->unique();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('fi_currency_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('object_id')->constrained('fi_currencies')->cascadeOnDelete();
            $table->string('language_code', 10);
            $table->string('name', 100);
            $table->unique(['object_id', 'language_code']);
        });

        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->string('from_currency', 8);
            $table->string('to_currency', 8);
            $table->decimal('value', 20, 8);
            $table->dateTime('begin_date');
            $table->dateTime('end_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(
                ['from_currency', 'to_currency', 'begin_date'],
                'currency_rate_start_unique'
            );
            $table->foreign('from_currency')->references('code')->on('fi_currencies')->restrictOnDelete();
            $table->foreign('to_currency')->references('code')->on('fi_currencies')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currency_rates');
        Schema::dropIfExists('fi_currency_translations');
        Schema::dropIfExists('fi_currencies');
    }
}
