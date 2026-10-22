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
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->date('data');
            $table->unsignedInteger('quantidade_pessoas');
            $table->enum('estado', ['pendente', 'confirmada', 'rejeitada', 'cancelada'])->default('pendente');
            $table->timestamps('criado_em')->useCurrent();
            $table->enum('tipo_de_uso', ['Exame', 'Trabalho de grupo', 'Leitura', 'Outro']);
            $table->foreignId('sala_id')->constrained('salas')->cascadeOnDelete();
            $table->foreignId('utilizador_id')->constrained('users')->cascadeOnDelete();
            $table->index('sala_id', 'data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
