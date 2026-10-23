<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('salas', function (Blueprint $t) {
            $t->string('unidade_organica', 20)->nullable()->after('piso');
        });
    }

    public function down(): void
    {
        Schema::table('salas', function (Blueprint $t) {
            $t->dropColumn('unidade_organica');
        });
    }
};
