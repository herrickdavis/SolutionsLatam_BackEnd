<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MatricesV2TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('matrices_v2')->insert([
            ['id' => 1, 'nombre_matriz' => 'Agua'],
            ['id' => 2, 'nombre_matriz' => 'Suelo, Sedimento, Lodo'],
            ['id' => 3, 'nombre_matriz' => 'Emisiones'],
            ['id' => 4, 'nombre_matriz' => 'Aire'],
            ['id' => 5, 'nombre_matriz' => 'Salud Ocupacional'],
            ['id' => 6, 'nombre_matriz' => 'Ruido Ambiental'],
            ['id' => 7, 'nombre_matriz' => 'Tejido Biológico'],
            ['id' => 8, 'nombre_matriz' => 'Vibraciones'],
            ['id' => 9, 'nombre_matriz' => 'Aceite Dieléctrico'],
            ['id' => 10, 'nombre_matriz' => 'Radiación Poblacional'],
            ['id' => 11, 'nombre_matriz' => 'Roca, Mineral, Relave'],
            ['id' => 12, 'nombre_matriz' => 'Matriz Sólida'],
            ['id' => 13, 'nombre_matriz' => 'Residuos Orgánicos'],
            ['id' => 14, 'nombre_matriz' => 'Petróleo Crudo'],
            ['id' => 15, 'nombre_matriz' => 'Extracto PECT'],
            ['id' => 16, 'nombre_matriz' => 'Biota'],
            ['id' => 17, 'nombre_matriz' => 'Alimentos'],
            ['id' => 18, 'nombre_matriz' => 'Sedimento Continental'],
            ['id' => 19, 'nombre_matriz' => 'Respel']
        ]);
    }
}
