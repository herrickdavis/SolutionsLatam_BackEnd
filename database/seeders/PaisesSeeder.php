<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaisesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('paises')->insert([
            'id' => '9',
            'nombre_pais' => 'Argentina'
        ]);

        DB::table('paises')->insert([
            'id' => '20',
            'nombre_pais' => 'Brasil'
        ]);

        DB::table('paises')->insert([
            'id' => '26',
            'nombre_pais' => 'Chile'
        ]);

        DB::table('paises')->insert([
            'id' => '28',
            'nombre_pais' => 'Colombia'
        ]);

        DB::table('paises')->insert([
            'id' => '38',
            'nombre_pais' => 'Ecuador'
        ]);

        DB::table('paises')->insert([
            'id' => '92',
            'nombre_pais' => 'Peru'
        ]);

        DB::table('paises')->insert([
            'id' => '829',
            'nombre_pais' => 'República Dominicana'
        ]);
    }
}
