<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        if (Schema::hasColumn('venda_itens', 'quantidade')) {
            // Se já existe, não faça nada
            return;
        }


        if (Schema::hasColumn('venda_itens', 'quatidade')) {
            Schema::table('venda_itens', function (Blueprint $table) {
                $table->renameColumn('quatidade', 'quantidade');
            });
        } else {

            Schema::table('venda_itens', function (Blueprint $table) {
                $table->integer('quantidade')->after('produto_id');
            });
        }
    }

    public function down(): void
    {

        if (Schema::hasColumn('venda_itens', 'quantidade')) {
            Schema::table('venda_itens', function (Blueprint $table) {
                $table->renameColumn('quantidade', 'quatidade');
            });
        }
    }
};
