<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'start_date')) {
                $table->date('start_date')->nullable()->after('course_date');
            }

            if (!Schema::hasColumn('courses', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }

            if (!Schema::hasColumn('courses', 'responsible')) {
                $table->string('responsible')->nullable()->after('end_date');
            }

            if (!Schema::hasColumn('courses', 'minimum_frequency')) {
                $table->decimal('minimum_frequency', 5, 2)->default(75.00)->after('responsible')
                    ->comment('Frequência mínima em % para aprovação');
            }

            if (!Schema::hasColumn('courses', 'minimum_grade')) {
                $table->decimal('minimum_grade', 5, 2)->nullable()->after('minimum_frequency')
                    ->comment('Nota mínima para aprovação (null = sem avaliação)');
            }

            if (!Schema::hasColumn('courses', 'status')) {
                // ativo | encerrado | cancelado
                $table->string('status')->default('ativo')->after('minimum_grade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $cols = ['start_date', 'end_date', 'responsible', 'minimum_frequency', 'minimum_grade', 'status'];
            $existing = array_values(array_filter(
                $cols,
                static fn(string $c): bool => Schema::hasColumn('courses', $c)
            ));

            if ($existing !== []) {
                $table->dropColumn($existing);
            }
        });
    }
};
