<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class TelemetriaTipoParametroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('telemetria_tipo_parametros')->insert([
            'id' => 1,
            'nombre_tipo_parametro' => 'Parametro Crudo',
        ]);

        DB::table('telemetria_tipo_parametros')->insert([
            'id' => 2,
            'nombre_tipo_parametro' => 'Parametro Procesado',
        ]);
    }
}
