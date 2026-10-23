<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration { // -> para importar o csv
    public function up(): void
    {
        Schema::create('ocupacoes', function (Blueprint $t) {
            $t->id();
            $t->dateTime('data_inicio');
            $t->dateTime('data_fim');
            $t->string('tipo_ocupacao', 50);
            $t->string('codigo_disciplina', 20)->nullable();
            $t->string('nome_disciplina', 150)->nullable();
            $t->string('ciclo_disciplina', 30)->nullable();
            $t->string('descricao_reserva', 150)->nullable();
            $t->string('estado', 30);
            $t->string('pessoa_responsavel', 150)->nullable();
            $t->text('observacoes')->nullable();
            $t->foreignId('sala_id')->constrained('salas')->cascadeOnDelete();
            $t->index(['sala_id', 'data_inicio', 'data_fim']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ocupacoes');
    }
};
