<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MotivoMuestrasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('motivo_muestras')->insert([
            'id' => 1,
            'nombre_motivo_muestra' => 'Rotina'
        ]);

        DB::table('motivo_muestras')->insert([
            'id' => 2,
            'nombre_motivo_muestra' => 'Extraordinária'
        ]);

        DB::table('motivo_muestras')->insert([
            'id' => 3,
            'nombre_motivo_muestra' => 'Rush'
        ]);
        
        DB::table('motivo_muestras')->insert([
            'id' => 4,
            'nombre_motivo_muestra' => 'Rush 2 dias (100%)'
        ]);
        
        DB::table('motivo_muestras')->insert([
            'id' => 5,
            'nombre_motivo_muestra' => 'Rush 3 días (60%)'
        ]);
        
        DB::table('motivo_muestras')->insert([
            'id' => 6,
            'nombre_motivo_muestra' => 'Rush 4 días (40%)'
        ]);
        
        DB::table('motivo_muestras')->insert([
            'id' => 7,
            'nombre_motivo_muestra' => 'Rush 5 días (30%)'
        ]);
    }
}
