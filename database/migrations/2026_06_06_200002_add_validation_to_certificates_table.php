<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Novos campos no certificado
        Schema::table('certificates', function (Blueprint $table) {
            if (!Schema::hasColumn('certificates', 'validation_code')) {
                $table->string('validation_code', 20)->unique()->nullable()->after('issue_date')
                    ->comment('Código único ex: CERT-2026-000001');
            }

            if (!Schema::hasColumn('certificates', 'qr_code')) {
                $table->text('qr_code')->nullable()->after('validation_code')
                    ->comment('Conteúdo SVG/PNG do QR Code');
            }

            if (!Schema::hasColumn('certificates', 'reissue_count')) {
                $table->unsignedSmallInteger('reissue_count')->default(0)->after('qr_code');
            }
        });

        // Log de ações (RN016, RN017)
        if (!Schema::hasTable('certificate_logs')) {
            Schema::create('certificate_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('certificate_id')->constrained()->cascadeOnDelete();
                $table->string('action');   // issued | reissued | cancelled | approved
                $table->text('notes')->nullable();
                $table->string('performed_by')->nullable();
                $table->timestamp('performed_at')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_logs');

        Schema::table('certificates', function (Blueprint $table) {
            $cols = ['validation_code', 'qr_code', 'reissue_count'];
            $existing = array_values(array_filter(
                $cols,
                static fn(string $c): bool => Schema::hasColumn('certificates', $c)
            ));

            if ($existing !== []) {
                $table->dropColumn($existing);
            }
        });
    }
};
