<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\UsuarioEmpresas;

class SetRegistroUsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $sql_usuario_sol = User::firstOrCreate(
            ['email' => $email_usuario_sol],
            [
                'name' => $nombre_usuario_sol,
                'password' => Hash::make('12345678'),
                //'password' => Hash::make(Str::random(10)),
                'id_empresa' => $id_empresa_sol,
                'id_rol' => 4,
            ]
        );

        $sql_usuario_emp_sol = UsuarioEmpresas::firstOrCreate(
            [
                    'id_usuario' => $sql_usuario_sol->id,
                    'id_empresa' => $id_empresa_sol,
                ],
            []
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
