<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PaisesSeeder::class);
        $this->call(ParecerParametrosSeeder::class);
        $this->call(EstadoMuestrasSeeder::class);
        $this->call(MotivoMuestrasSeeder::class);
        $this->call(RolSeeder::class);
        $this->call(BotonesSeeder::class);
    }
}
