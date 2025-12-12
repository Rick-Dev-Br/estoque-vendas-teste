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
        Schema::table('produtos', function (Blueprint $table) {
            if (!Schema::hasColumn('produtos', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('clientes', function (Blueprint $table) {
            if (!Schema::hasColumn('clientes', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('vendas', function (Blueprint $table) {
            if (!Schema::hasColumn('vendas', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('vendas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
