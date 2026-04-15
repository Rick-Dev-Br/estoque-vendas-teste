<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('vendas', 'canal_venda')) {
            Schema::table('vendas', function (Blueprint $table) {
                $table->enum('canal_venda', ['online', 'loja_fisica'])
                    ->default('online')
                    ->after('status');
            });
        }

        DB::table('vendas')
            ->whereNull('canal_venda')
            ->update(['canal_venda' => 'online']);
    }

    public function down(): void
    {
        if (Schema::hasColumn('vendas', 'canal_venda')) {
            Schema::table('vendas', function (Blueprint $table) {
                $table->dropColumn('canal_venda');
            });
        }
    }
};
