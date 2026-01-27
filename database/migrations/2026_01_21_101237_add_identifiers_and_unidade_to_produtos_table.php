<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            if (!Schema::hasColumn('produtos', 'public_id')) {
                $table->ulid('public_id')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('produtos', 'codigo')) {
                $table->string('codigo', 30)->nullable()->unique()->after('public_id');
            }
            if (!Schema::hasColumn('produtos', 'unidade_medida')) {
                $table->enum('unidade_medida', ['kg', 'g', 'unidade', 'saco', 'conjunto'])
                    ->default('unidade')
                    ->after('estoque');
            }
            if (!Schema::hasColumn('produtos', 'unidade_quantidade')) {
                $table->decimal('unidade_quantidade', 10, 3)->default(1)->after('unidade_medida');
            }
        });

        DB::table('produtos')
            ->whereNull('public_id')
            ->orderBy('id')
            ->chunkById(100, function ($produtos) {
                foreach ($produtos as $produto) {
                    $codigo = $produto->codigo ?: sprintf('PRD-%06d', $produto->id);

                    DB::table('produtos')
                        ->where('id', $produto->id)
                        ->update([
                            'public_id' => (string) Str::ulid(),
                            'codigo' => $codigo,
                            'unidade_medida' => $produto->unidade_medida ?? 'unidade',
                            'unidade_quantidade' => $produto->unidade_quantidade ?? 1,
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            if (Schema::hasColumn('produtos', 'public_id')) {
                $table->dropUnique(['public_id']);
                $table->dropColumn('public_id');
            }
            if (Schema::hasColumn('produtos', 'codigo')) {
                $table->dropUnique(['codigo']);
                $table->dropColumn('codigo');
            }
            if (Schema::hasColumn('produtos', 'unidade_medida')) {
                $table->dropColumn('unidade_medida');
            }
            if (Schema::hasColumn('produtos', 'unidade_quantidade')) {
                $table->dropColumn('unidade_quantidade');
            }
        });
    }
};
