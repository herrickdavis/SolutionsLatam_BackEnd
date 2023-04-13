<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Throwable;

class GetParametrosReporteEstacionesController extends Controller
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
        try {
            $usuario = $request->user();

            $id_tipo_muestra = str_replace("TA", "", $request->id_tipo_muestra);
            $sql_parametros = DB::table('muestras as m')
                                ->select(DB::raw(
                                    "case when gp.grupo_parametros is null then CONCAT('P',p.id) else CONCAT('G',gp.id) end as id,
                                     case when gp.grupo_parametros is null then p.nombre_parametro else gp.grupo_parametros end as nombre_parametro
                                    "
                                ))
                                ->join('muestra_parametros as mp', 'mp.id_muestra', '=', 'm.id')
                                ->leftjoin('metodos as me', 'me.id', '=', 'mp.id_metodo')
                                ->join('parametros AS p', 'mp.id_parametro', '=', 'p.id')
                                ->leftjoin('parametro_grupo_parametros AS pgp', function ($join) {
                                    $join->on('pgp.id_parametro', '=', 'p.id')
                                        ->on('pgp.idaux_metodo', '=', 'me.idaux_metodo');
                                })
                                ->leftjoin('grupo_parametros AS gp', 'gp.id', '=', 'pgp.id_grupo_parametro')
                                ->where('m.id_tipo_muestra', '=', $id_tipo_muestra)
                                ->where('m.id_estado', '=', 3)
                                ->distinct()->orderBy('nombre_parametro', 'ASC');
            
            $sql_parametros = filtroMuestrasQuery($sql_parametros,$usuario);
            $sql_parametros = $sql_parametros->get();
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return $sql_parametros;
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
