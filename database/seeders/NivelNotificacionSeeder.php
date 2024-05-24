<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class NivelNotificacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('nivel_notificacions')->insert([
            'id' => 1,
            'nombre_nivel_notificacion' => 'Informativo',
        ]);
        DB::table('nivel_notificacions')->insert([
            'id' => 2,
            'nombre_nivel_notificacion' => 'Advertencia',
        ]);
        DB::table('nivel_notificacions')->insert([
            'id' => 3,
            'nombre_nivel_notificacion' => 'Alerta',
        ]);

    }
}
