<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Throwable;

class GetEstacionesReporteEstacionesController extends Controller
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
            //$id_parametro = str_replace("P", "", $request->id_parametro);
            
            $parametro = $request->id_parametro;
            
            if (substr($parametro, 0, 1) == "G") {
                $sql_id_parametros_grupo = DB::table('grupo_parametros as gp')
                                ->select(DB::raw(
                                    "
                                    pgp.id_parametro as id                                    
                                    "
                                ))
                                ->join('parametro_grupo_parametros AS pgp', 'pgp.id_grupo_parametro', '=', 'gp.id')
                                ->where('pgp.id_grupo_parametro','=', str_replace("G", "", $request->id_parametro));
                $sql_id_parametros_grupo = array_values($sql_id_parametros_grupo->get()->toArray());
                $id_parametro = [];
                foreach ($sql_id_parametros_grupo as $value) {
                    array_push($id_parametro,$value->id);
                }                
                $sql_estaciones = DB::table('muestras as m')
                                ->select(DB::raw(
                                    "CONCAT('E',e.id) as id,
                                    e.nombre_estacion as nombre_estacion"
                                ))
                                ->leftjoin('muestra_parametros as mp', 'mp.id_muestra', '=', 'm.id')
                                ->leftjoin('estaciones as e', 'e.id', '=', 'm.id_estacion')
                                ->where('m.id_tipo_muestra', '=', $id_tipo_muestra)
                                ->whereIn('mp.id_parametro', $id_parametro)
                                ->distinct();
            } else {
                $sql_estaciones = DB::table('muestras as m')
                                ->select(DB::raw(
                                    "CONCAT('E',e.id) as id,
                                    e.nombre_estacion as nombre_estacion"
                                ))
                                ->leftjoin('muestra_parametros as mp', 'mp.id_muestra', '=', 'm.id')
                                ->leftjoin('estaciones as e', 'e.id', '=', 'm.id_estacion')                                
                                ->where('m.id_tipo_muestra', '=', $id_tipo_muestra)
                                ->where('mp.id_parametro', '=', str_replace("P", "", $request->id_parametro))
                                ->distinct();
            }            
            $sql_estaciones = filtroMuestrasQuery($sql_estaciones,$usuario);
            $sql_estaciones = $sql_estaciones->get();
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return $sql_estaciones;
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
