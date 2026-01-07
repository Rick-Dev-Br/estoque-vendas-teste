<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->dateTime('data_compra')->nullable()->after('status');

            $table->string('forma_pagamento', 30)->nullable()->after('data_compra');

            $table->string('endereco_entrega', 255)->nullable()->after('forma_pagamento');
            $table->string('numero', 20)->nullable()->after('endereco_entrega');
            $table->string('complemento', 100)->nullable()->after('numero');
            $table->string('bairro', 100)->nullable()->after('complemento');
            $table->string('cidade', 100)->nullable()->after('bairro');
            $table->string('estado', 2)->nullable()->after('cidade');
            $table->string('cep', 15)->nullable()->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->dropColumn([
                'data_compra', 'forma_pagamento', 'endereco_entrega', 'numero',
                'complemento', 'bairro', 'cidade', 'estado', 'cep'
            ]);
        });
    }
};
