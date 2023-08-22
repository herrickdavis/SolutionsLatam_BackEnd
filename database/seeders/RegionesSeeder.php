<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('regiones')->insert([
            'id' => '1',
            'nombre_region' => 'Perú'
        ]);
        DB::table('regiones')->insert([
            'id' => '2',
            'nombre_region' => 'Argentina'
        ]);
        DB::table('regiones')->insert([
            'id' => '3',
            'nombre_region' => 'Brasil'
        ]);
        DB::table('regiones')->insert([
            'id' => '4',
            'nombre_region' => 'Chile'
        ]);
        DB::table('regiones')->insert([
            'id' => '5',
            'nombre_region' => 'Ecuador'
        ]);
        DB::table('regiones')->insert([
            'id' => '6',
            'nombre_region' => 'República Dominicana'
        ]);
        DB::table('regiones')->insert([
            'id' => '7',
            'nombre_region' => 'México'
        ]);
        DB::table('regiones')->insert([
            'id' => '8',
            'nombre_region' => 'Colombia'
        ]);
    }
}
