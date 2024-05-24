<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TelemetriaEstadoResultadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('telemetria_estado_resultados')->insert([
            'id' => 1,
            'nombre_estado' => 'Pendiente',
        ]);
        DB::table('telemetria_estado_resultados')->insert([
            'id' => 2,
            'nombre_estado' => 'Valido',
        ]);
        DB::table('telemetria_estado_resultados')->insert([
            'id' => 3,
            'nombre_estado' => 'Rechazado',
        ]);
        DB::table('telemetria_estado_resultados')->insert([
            'id' => 4,
            'nombre_estado' => 'No Válido',
        ]);
    }
}
