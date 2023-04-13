<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rols')->insert([
            'rol' => 'webmaster'
        ]);

        DB::table('rols')->insert([
            'rol' => 'administrador'
        ]);

        DB::table('rols')->insert([
            'rol' => 'staff'
        ]);

        DB::table('rols')->insert([
            'rol' => 'cliente'
        ]);
    }
}
