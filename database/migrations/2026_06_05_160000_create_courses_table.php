<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('hours')->nullable();
            $table->date('course_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('responsible')->nullable();
            $table->decimal('minimum_frequency', 5, 2)->default(75.00)->comment('Frequência mínima em % para aprovação');
            $table->decimal('minimum_grade', 5, 2)->nullable()->comment('Nota mínima para aprovação (null = sem avaliação)');
            $table->string('status')->default('ativo');
            $table->boolean('active')->default(true);
            $table->string('verificador')->nullable();
            $table->string('crc')->nullable();
            $table->string('ass1')->nullable();
            $table->string('ass2')->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_bg')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
