<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'description')) {
                $table->text('description')->nullable()->after('name');
            }

            if (!Schema::hasColumn('courses', 'hours')) {
                $table->unsignedInteger('hours')->nullable()->after('description');
            }

            if (!Schema::hasColumn('courses', 'course_date')) {
                $table->date('course_date')->nullable()->after('hours');
            }

            if (!Schema::hasColumn('courses', 'ass1')) {
                $table->string('ass1')->nullable()->after('course_date');
            }

            if (!Schema::hasColumn('courses', 'ass2')) {
                $table->string('ass2')->nullable()->after('ass1');
            }

            if (!Schema::hasColumn('courses', 'image_path')) {
                $table->string('image_path')->nullable()->after('ass2');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $columns = ['description', 'hours', 'course_date', 'ass1', 'ass2', 'image_path'];
            $existingColumns = array_values(array_filter(
                $columns,
                static fn(string $column): bool => Schema::hasColumn('courses', $column)
            ));

            if ($existingColumns !== []) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};
