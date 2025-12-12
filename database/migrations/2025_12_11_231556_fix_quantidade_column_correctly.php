<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('venda_itens', function (Blueprint $table) {
        $table->renameColumn('quatidade', 'quantidade');
    });
}

public function down()
{
    Schema::table('venda_itens', function (Blueprint $table) {
        $table->renameColumn('quantidade', 'quatidade');
    });
}
};
