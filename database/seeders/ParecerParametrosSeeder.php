<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParecerParametrosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('parecer_parametros')->insert([
            'nombre_parecer' => 'Sin Limites'
        ]);

        DB::table('parecer_parametros')->insert([
            'nombre_parecer' => 'Conforme'
        ]);

        DB::table('parecer_parametros')->insert([
            'nombre_parecer' => 'No Conforme'
        ]);
    }
}
