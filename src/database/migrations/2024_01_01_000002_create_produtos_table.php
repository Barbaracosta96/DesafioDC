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
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->string('nome');
            $table->string('codigo_sku')->unique()->nullable();
            $table->text('descricao')->nullable();
            $table->decimal('preco_custo', 10, 2)->default(0);
            $table->decimal('preco_venda', 10, 2)->default(0);
            $table->integer('quantidade_estoque')->default(0);
            $table->integer('estoque_minimo')->default(5);
            $table->string('unidade')->default('un');
            $table->string('imagem')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
