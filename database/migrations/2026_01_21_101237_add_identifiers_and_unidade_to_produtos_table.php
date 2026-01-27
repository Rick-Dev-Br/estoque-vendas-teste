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
        });

        if (!Schema::hasColumn('produtos', 'unidade_medida')) {
            Schema::table('produtos', function (Blueprint $table) {
                $table->enum('unidade_medida', ['un', 'kg', 'l', 'm'])->default('un')->after('tipo_unidade');
            });
        }

        if (!Schema::hasColumn('produtos', 'unidade_quantidade')) {
            Schema::table('produtos', function (Blueprint $table) {
                $table->decimal('unidade_quantidade', 10, 2)->default(1)->after('unidade_medida');
            });
        }

            DB::table('produtos')
            ->whereNull('tipo_unidade')
            ->update(['tipo_unidade' => 'unidade']);

            DB::table('produtos')
            ->whereNull('unidade_medida')
            ->update(['unidade_medida' => 'un']);


        DB::table('produtos')
            ->whereNull('unidade_quantidade')
            ->update(['unidade_quantidade' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('produtos', 'unidade_quantidade')) {
            Schema::table('produtos', function (Blueprint $table) {
                $table->dropColumn('unidade_quantidade');
            });
        }

        if (Schema::hasColumn('produtos', 'unidade_medida')) {
            Schema::table('produtos', function (Blueprint $table) {
                $table->dropColumn('unidade_medida');
            });
        }

        Schema::table('produtos', function (Blueprint $table) {
            if (Schema::hasColumn('produtos', 'tipo_unidade')) {
                $table->dropColumn('tipo_unidade');
            }
        });
    }
};
