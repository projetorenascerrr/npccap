<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_user_id')->constrained('student_users')->cascadeOnDelete();
            $table->string('status')->default('inscrito');
            $table->decimal('frequency', 5, 2)->nullable()->comment('Frequência em % (0-100)');
            $table->decimal('grade', 5, 2)->nullable()->comment('Nota obtida');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
