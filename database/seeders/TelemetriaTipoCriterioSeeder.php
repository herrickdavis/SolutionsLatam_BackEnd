<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class TelemetriaTipoCriterioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('telemetria_tipo_criterios_validacions')->insert([
            'id' => 1,
            'nombre_tipo_criterio' => 'Condiciones de Operación',
        ]);

        DB::table('telemetria_tipo_criterios_validacions')->insert([
            'id' => 2,
            'nombre_tipo_criterio' => 'Alerta',
        ]);

        DB::table('telemetria_tipo_criterios_validacions')->insert([
            'id' => 3,
            'nombre_tipo_criterio' => 'Descarte',
        ]);
    }
}
