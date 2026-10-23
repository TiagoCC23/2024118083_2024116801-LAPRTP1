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
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('eliminado_em')->nullable();
            $table->string('tipo_de_uso', 100);
            $table->foreignId('sala_id')->constrained('salas')->restrictOnDelete();
            $table->foreignId('utilizador_id')->constrained('utilizadores')->restrictOnDelete();
            $table->index(['sala_id', 'data']);
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
