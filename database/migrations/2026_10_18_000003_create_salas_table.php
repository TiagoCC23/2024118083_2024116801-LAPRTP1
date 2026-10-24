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
        Schema::create('salas', function (Blueprint $table) {
            $table->id();
            $table->string('nome_numero', 50);
            $table->unsignedSmallInteger('capacidade_maxima');
            $table->string('tipo_sala');
            $table->integer('piso');
            $table->enum('disponibilidade', ['disponivel', 'pendente', 'ocupado'])->default('disponivel');
            $table->foreignId('edificio_id')->constrained('edificios')->restrictOnDelete();
            $table->unique(['edificio_id', 'nome_numero']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salas');
    }
};
