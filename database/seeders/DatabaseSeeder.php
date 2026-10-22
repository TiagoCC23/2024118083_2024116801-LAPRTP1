<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            EdificioSeeder::class,
            UserSeeder::class,
            SalaSeeder::class,
            HorarioSeeder::class,
            ReservaSeeder::class,
        ]);
    }
}
