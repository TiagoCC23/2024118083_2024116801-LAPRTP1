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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('email')->unique();
            $table->timestamp('verificado_em')->nullable();
            $table->string('password_hash');
            $table->enum('tipo_user', ['estudante', 'admin'])->default('estudante');
            $table->foreignId('edificio_id')->nullable()->constrained('edificios')->nullOnDelete();
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('eliminado_em')->nullable(); // vai usar o softDelete()
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
