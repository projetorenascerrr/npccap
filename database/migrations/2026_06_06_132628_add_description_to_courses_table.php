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
            $table->text('description')->nullable()->after('name');
            $table->unsignedInteger('hours')->nullable()->after('description');
            $table->date('course_date')->nullable()->after('hours');
            $table->string('ass1')->nullable()->after('course_date');
            $table->string('ass2')->nullable()->after('ass1');
            $table->string('image_path')->nullable()->after('ass2');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['description', 'hours', 'course_date', 'ass1', 'ass2', 'image_path']);
        });
    }
};
