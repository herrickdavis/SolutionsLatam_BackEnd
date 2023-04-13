<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BotonesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('botones')->insert([
            'id' => 1,
            'nombre_boton' => 'muestras'
        ]);

        DB::table('botones')->insert([            
            'id' => 2,
            'nombre_boton' => 'get_data_muestra'
        ]);

        DB::table('botones')->insert([            
            'id' => 3,
            'nombre_boton' => 'change_normativa'
        ]);

        DB::table('botones')->insert([
            'id' => 4,
            'nombre_boton' => 'filtro_contratante'
        ]);

        DB::table('botones')->insert([            
            'id' => 5,
            'nombre_boton' => 'filtro_estacion'
        ]);

        DB::table('botones')->insert([            
            'id' => 6,
            'nombre_boton' => 'filtro_estado'
        ]);

        DB::table('botones')->insert([            
            'id' => 7,
            'nombre_boton' => 'filtro_fecha_muestreo'
        ]);

        DB::table('botones')->insert([            
            'id' => 8,
            'nombre_boton' => 'filtro_proyecto'
        ]);

        DB::table('botones')->insert([            
            'id' => 9,
            'nombre_boton' => 'filtro_solicitante'
        ]);

        DB::table('botones')->insert([            
            'id' => 10,
            'nombre_boton' => 'filtro_tipo_muestra'
        ]);

        DB::table('botones')->insert([
            'id' => 11,
            'nombre_boton' => 'filtro_codigo_muestra'
        ]);

        DB::table('botones')->insert([
            'id' => 12,
            'nombre_boton' => 'filtro_numero_grupo'
        ]);

        DB::table('botones')->insert([
            'id' => 13,
            'nombre_boton' => 'filtro_numero_muestra'
        ]);

        DB::table('botones')->insert([
            'id' => 14,
            'nombre_boton' => 'change_get_columnas'
        ]);

        DB::table('botones')->insert([
            'id' => 15,
            'nombre_boton' => 'change_posicion_columna'
        ]);

        DB::table('botones')->insert([            
            'id' => 16,
            'nombre_boton' => 'get_documentos_muestra'
        ]);

        DB::table('botones')->insert([            
            'id' => 17,
            'nombre_boton' => 'download_documento'
        ]);

        DB::table('botones')->insert([            
            'id' => 18,
            'nombre_boton' => 'download_all'
        ]);

        DB::table('botones')->insert([
            'id' => 19,
            'nombre_boton' => 'historico'
        ]);

        DB::table('botones')->insert([
            'id' => 20,
            'nombre_boton' => 'change_proyecto_historico'
        ]);

        DB::table('botones')->insert([
            'id' => 21,
            'nombre_boton' => 'change_normativa_historico'
        ]);

        DB::table('botones')->insert([
            'id' => 22,
            'nombre_boton' => 'generar_historico'
        ]);

        DB::table('botones')->insert([
            'id' => 23,
            'nombre_boton' => 'descargar_historico'
        ]);

        DB::table('botones')->insert([
            'id' => 24,
            'nombre_boton' => 'fuera_limite'
        ]);

        DB::table('botones')->insert([
            'id' => 25,
            'nombre_boton' => 'generar_fuera_limite'
        ]);

        DB::table('botones')->insert([
            'id' => 26,
            'nombre_boton' => 'descargar_fuera_limite'
        ]);

        DB::table('botones')->insert([
            'id' => 27,
            'nombre_boton' => 'estacion_parametro'
        ]);

        DB::table('botones')->insert([
            'id' => 28,
            'nombre_boton' => 'generar_estacion_parametro'
        ]);

        DB::table('botones')->insert([
            'id' => 29,
            'nombre_boton' => 'ver_etiquetas_estacion_parametro'
        ]);
    }
}
