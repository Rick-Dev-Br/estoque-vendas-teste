<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;



return new class extends Migration {
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('nome_completo', 150)->nullable()->after('nome');
            $table->string('cpf', 14)->nullable()->unique()->after('email');

            $table->string('telefone', 20)->nullable()->after('cpf');

            $table->string('endereco', 255)->nullable()->after('telefone');
            $table->string('numero', 20)->nullable()->after('endereco');
            $table->string('complemento', 100)->nullable()->after('numero');
            $table->string('bairro', 100)->nullable()->after('complemento');
            $table->string('cidade', 100)->nullable()->after('bairro');
            $table->string('estado', 2)->nullable()->after('cidade');
            $table->string('cep', 15)->nullable()->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropUnique(['cpf']);
            $table->dropColumn([
                'nome_completo', 'cpf', 'telefone', 'endereco',
                'numero', 'complemento', 'bairro', 'cidade', 'estado', 'cep'
            ]);
        });
    }
};
