<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'email')) {
                $table->string('email')->nullable()->after('cpf');
            }

            if (!Schema::hasColumn('students', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('email');
            }

            if (!Schema::hasColumn('students', 'status')) {
                // pre-inscrito | inscrito | confirmado | certificado_emitido
                $table->string('status')->default('inscrito')->after('birth_date');
            }

            if (!Schema::hasColumn('students', 'frequency')) {
                $table->decimal('frequency', 5, 2)->nullable()->after('status')
                    ->comment('Frequência em % (0-100)');
            }

            if (!Schema::hasColumn('students', 'grade')) {
                $table->decimal('grade', 5, 2)->nullable()->after('frequency')
                    ->comment('Nota obtida');
            }

            if (!Schema::hasColumn('students', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('grade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $cols = ['email', 'birth_date', 'status', 'frequency', 'grade', 'approved_at'];
            $existing = array_values(array_filter(
                $cols,
                static fn(string $c): bool => Schema::hasColumn('students', $c)
            ));

            if ($existing !== []) {
                $table->dropColumn($existing);
            }
        });
    }
};
