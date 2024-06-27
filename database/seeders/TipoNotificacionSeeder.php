<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class TipoNotificacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_notificacions')->insert([
            'id' => 1,
            'nombre_tipo_notificacion' => 'Condiciones de Operación',
        ]);
        DB::table('tipo_notificacions')->insert([
            'id' => 2,
            'nombre_tipo_notificacion' => 'Validación de dato',
        ]); 
    }
}
