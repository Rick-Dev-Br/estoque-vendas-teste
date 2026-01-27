<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            if (!Schema::hasColumn('produtos', 'tipo_unidade')) {
                $table->string('tipo_unidade', 20)->default('unidade')->after('estoque');
            }
            if (!Schema::hasColumn('produtos', 'unidade_medida')) {
                $table->enum('unidade_medida', ['un', 'kg', 'l', 'm'])->default('un')->after('tipo_unidade');
            }
            if (!Schema::hasColumn('produtos', 'unidade_quantidade')) {
                $table->decimal('unidade_quantidade', 10, 2)->default(1)->after('unidade_medida');
            }
        });

        if (Schema::hasColumn('produtos', 'tipo_unidade')) {
            DB::table('produtos')
                ->whereNull('tipo_unidade')
                ->update(['tipo_unidade' => 'unidade']);
        }

        if (Schema::hasColumn('produtos', 'unidade_medida')) {
            DB::statement("UPDATE produtos SET tipo_unidade = CASE
                WHEN unidade_medida = 'saco' THEN 'saco'
                WHEN unidade_medida = 'conjunto' THEN 'pacote'
                WHEN unidade_medida = 'unidade' THEN 'unidade'
                ELSE tipo_unidade
            END");

            DB::statement("UPDATE produtos SET unidade_quantidade = unidade_quantidade / 1000 WHERE unidade_medida = 'g'");
            DB::statement("UPDATE produtos SET unidade_medida = 'kg' WHERE unidade_medida = 'g'");
            DB::statement("UPDATE produtos SET unidade_medida = 'un' WHERE unidade_medida IN ('unidade', 'saco', 'conjunto')");

            DB::statement("ALTER TABLE produtos MODIFY unidade_medida ENUM('un', 'kg', 'l', 'm') NOT NULL DEFAULT 'un'");
        }

        if (Schema::hasColumn('produtos', 'unidade_quantidade')) {
            DB::statement("ALTER TABLE produtos MODIFY unidade_quantidade DECIMAL(10, 2) NOT NULL DEFAULT 1");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('produtos', 'unidade_medida')) {
            DB::statement("ALTER TABLE produtos MODIFY unidade_medida ENUM('kg', 'g', 'unidade', 'saco', 'conjunto') NOT NULL DEFAULT 'unidade'");
        }

        if (Schema::hasColumn('produtos', 'unidade_quantidade')) {
            DB::statement("ALTER TABLE produtos MODIFY unidade_quantidade DECIMAL(10, 3) NOT NULL DEFAULT 1");
        }

        Schema::table('produtos', function (Blueprint $table) {
            if (Schema::hasColumn('produtos', 'tipo_unidade')) {
                $table->dropColumn('tipo_unidade');
            }
        });
    }
};
