<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ParametroGrupoParametros;
use App\Models\Parametros;
use App\Models\GrupoParametros;

class SetConsolidarParametrosController extends Controller
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
        ini_set('max_execution_time', 520); //3 minutes
        $datos = $request->all();
        //return $datos;
        try {
            foreach($datos as $dato) {
                $nombre_grupo = $dato['nombre_parametro'];
                $id_parametro = $dato['id_parametro'];
                $idaux_metodo = $dato['idaux_metodo'];
                //return $idaux_metodo;
                //creo el grupo si no existe
                $grupo_parametro = GrupoParametros::updateOrCreate(
                    [
                        'grupo_parametros' => $nombre_grupo
                    ],
                    [
                        'grupo_parametros' => $nombre_grupo
                    ]
                );

                $pgp = ParametroGrupoParametros::updateOrCreate(
                    [
                        'id_grupo_parametro' => $grupo_parametro->id,
                        'id_parametro' => $id_parametro,
                        'idaux_metodo' => $idaux_metodo
                    ],
                    [
                        'id_grupo_parametro' => $grupo_parametro->id,
                        'id_parametro' => $id_parametro,
                        'idaux_metodo' => $idaux_metodo
                    ]
                );

                $rpta["success"] = "Ok";
                $rpta["mensaje"] = "Ok";
                //return $pgp->id;
            }
        } catch (\Throwable $e) {
            report($e);
            $rpta["error"] = "error";
            $rpta["mensaje"] = $e->getMessage();
        }

        return $rpta;
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
