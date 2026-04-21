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
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            // Esta linha liga o produto à tabela de categorias
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->integer('estoque_atual')->default(0);
            $table->integer('estoque_minimo')->default(5);
            $table->string('unidade_medida')->default('un'); // un, resma, caixa
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
