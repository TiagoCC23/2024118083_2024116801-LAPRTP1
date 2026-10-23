<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('administra', function (Blueprint $t) {
            $t->id();
            $t->foreignId('utilizador_id')->constrained('utilizadores')->cascadeOnDelete(); // user = admin
            $t->foreignId('sala_id')->constrained('salas')->cascadeOnDelete();
            $t->unique(['utilizador_id', 'sala_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administra');
    }
};
