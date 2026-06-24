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
            if (!Schema::hasColumn('courses', 'verificador')) {
                $table->string('verificador')->nullable()->after('active');
            }
            if (!Schema::hasColumn('courses', 'crc')) {
                $table->string('crc')->nullable()->after('verificador');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $cols = ['verificador', 'crc'];
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
