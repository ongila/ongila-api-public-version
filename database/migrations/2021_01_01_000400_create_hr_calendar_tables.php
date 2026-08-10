<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrCalendarTables extends Migration
{
    public function up(): void
    {
        Schema::create('hr_shifts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('status_id')->default(1);
            $table->time('from');
            $table->time('to');
            $table->time('lunch_from')->nullable();
            $table->time('lunch_to')->nullable();
            $table->boolean('count_overtime')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hr_holidays', function (Blueprint $table) {
            $table->id();
            $table->string('date', 5)->unique();
            $table->unsignedTinyInteger('status_id')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('hr_holiday_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('object_id')->constrained('hr_holidays')->cascadeOnDelete();
            $table->string('language_code', 10);
            $table->string('name', 150);
            $table->unique(['object_id', 'language_code']);
        });

        Schema::create('yearly_calendars', function (Blueprint $table) {
            $table->id();
            $table->date('calendar_date')->unique();
            $table->foreignId('holiday_id')->nullable()->constrained('hr_holidays')->nullOnDelete();
            $table->boolean('is_weekend');
            $table->boolean('is_workday');
            $table->unsignedInteger('workday_sequence')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yearly_calendars');
        Schema::dropIfExists('hr_holiday_translations');
        Schema::dropIfExists('hr_holidays');
        Schema::dropIfExists('hr_shifts');
    }
}
