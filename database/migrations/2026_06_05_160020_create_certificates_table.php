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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('student_name');
            $table->string('cpf', 14);
            $table->string('course_name');
            $table->date('issue_date');
            $table->string('validation_code', 20)->unique()->nullable()->comment('Código único ex: CERT-2026-000001');
            $table->text('qr_code')->nullable()->comment('Conteúdo SVG/PNG do QR Code');
            $table->unsignedSmallInteger('reissue_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
