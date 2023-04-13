<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoMuestrasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('estado_muestras')->insert([
            'nombre_estado' => 'Recibida'
        ]);

        DB::table('estado_muestras')->insert([
            'nombre_estado' => 'En Proceso'
        ]);

        DB::table('estado_muestras')->insert([
            'nombre_estado' => 'Finalizada'
        ]);

        DB::table('estado_muestras')->insert([
            'nombre_estado' => 'Con Informe'
        ]);
    }
}
