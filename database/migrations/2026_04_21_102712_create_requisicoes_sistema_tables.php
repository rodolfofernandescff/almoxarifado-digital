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
        // 1. Criando a tabela de Requisições (Cabeçalho do pedido)
        Schema::create('requisicoes', function (Blueprint $table) {
            $table->id();
            // Liga o pedido ao usuário que está logado
            $table->foreignId('user_id')->constrained('users');
            
            // Status do pedido para o fluxo de aprovação
            $table->enum('status', ['pendente', 'aprovado', 'recusado', 'finalizado'])->default('pendente');
            
            $table->text('justificativa')->nullable(); // Por que o usuário quer isso?
            $table->text('observacao_admin')->nullable(); // Por que o admin recusou ou aprovou?
            
            // Guarda quem foi o administrador que aprovou
            $table->foreignId('aprovado_por')->nullable()->constrained('users');
            
            $table->timestamps();
        });

        // 2. Criando a tabela de Itens (Os produtos de cada pedido)
        Schema::create('item_requisicao', function (Blueprint $table) {
            $table->id();
            // Se a requisição for deletada, os itens dela também serão (cascade)
            $table->foreignId('requisicao_id')->constrained('requisicoes')->onDelete('cascade');
            // Liga ao produto que está sendo solicitado
            $table->foreignId('produto_id')->constrained('produtos');
            
            $table->integer('quantidade_pedida'); // Quantidade que o usuário quer
            $table->integer('quantidade_entregue')->nullable(); // Quantidade que o almoxarife entregou
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ordem inversa para não dar erro de chave estrangeira ao desfazer
        Schema::dropIfExists('item_requisicao');
        Schema::dropIfExists('requisicoes');
    }
};