<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('produtos', 'codigo_barras')) {
            Schema::table('produtos', function (Blueprint $table) {
                $table->string('codigo_barras', 50)->nullable()->after('codigo');
                $table->unique('codigo_barras');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('produtos', 'codigo_barras')) {
            Schema::table('produtos', function (Blueprint $table) {
                $table->dropUnique('produtos_codigo_barras_unique');
                $table->dropColumn('codigo_barras');
            });
        }
    }
};
