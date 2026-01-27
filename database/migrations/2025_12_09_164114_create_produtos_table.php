<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();

            $table->ulid('public_id')->unique();

            $table->string('codigo', 30)->unique();

            $table->string('nome', 100);
            $table->decimal('preco', 10, 2);
            $table->integer('estoque')->default(0);

            $table->string('tipo_unidade', 20)->default('unidade');
            $table->enum('unidade_medida', ['un', 'kg', 'l', 'm'])->default('un');
            $table->decimal('unidade_quantidade', 10, 2)->default(1);

            $table->enum('status', ['ativo', 'inativo'])->default('ativo');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
