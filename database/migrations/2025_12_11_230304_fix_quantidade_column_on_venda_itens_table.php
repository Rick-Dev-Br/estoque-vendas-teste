<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venda_itens', function (Blueprint $table) {
            $table->renameColumn('quatidade', 'quantidade');
        });
    }

    public function down(): void
    {
        Schema::table('venda_itens', function (Blueprint $table) {
            $table->renameColumn('quantidade', 'quatidade');
        });
    }
};
