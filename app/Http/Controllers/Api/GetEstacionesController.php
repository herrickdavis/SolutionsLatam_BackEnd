<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ClickBotones;
use Throwable;

class GetEstacionesController extends Controller
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
            $fecha_inicio = $request->fecha_inicio;
            $fecha_fin = $request->fecha_fin;
            $fuera_limite = $request->fuera_limite;
            $id_proyecto = $request->id_proyecto;


            if ($fuera_limite) {
                $sql_estaciones = DB::table('muestras as m')
                        ->select(DB::raw(
                            "
                            case when e.alias_estacion is null then e.nombre_estacion else e.alias_estacion end as id,
                            case when e.alias_estacion is null then e.nombre_estacion else e.alias_estacion end as nombre_estacion
                            "
                        ))
                        ->join('muestra_parametros AS mp', 'mp.id_muestra', '=', 'm.id')
                        ->leftjoin('estaciones AS e', 'm.id_estacion', '=', 'e.id')
                        ->leftjoin('proceso_muestras AS pm', 'pm.id_muestra','=','m.id')
                        ->leftjoin('tipo_muestras AS tm', 'tm.id', '=', 'm.id_tipo_muestra')
                        ->where('mp.id_parecer', '=', 3)
                        ->where('m.fecha_muestreo', '>', $fecha_inicio)
                        ->where('m.fecha_muestreo', '<', $fecha_fin)
                        ->where('m.id_tipo_muestra', '=', $id_tipo_muestra)
                        ->where('m.activo', '=', 'S')
                        ->where('tm.activo', '=', 'S')
                        ->distinct('id')->orderBy('nombre_estacion');
            } else {
                $sql_estaciones = DB::table('muestras as m')
                        ->select(DB::raw(
                            "
                            case when e.alias_estacion is null then e.nombre_estacion else e.alias_estacion end as id,
                            case when e.alias_estacion is null then e.nombre_estacion else e.alias_estacion end as nombre_estacion
                            "
                        ))
                        ->leftjoin('estaciones AS e', 'm.id_estacion', '=', 'e.id')
                        ->leftjoin('proceso_muestras AS pm', 'pm.id_muestra','=','m.id')
                        ->leftjoin('tipo_muestras AS tm', 'tm.id', '=', 'm.id_tipo_muestra')
                        ->whereIn('m.id_estado', [3,4])
                        ->where('m.id_tipo_muestra', '=', $id_tipo_muestra)
                        ->where('m.activo', '=', 'S')
                        ->where('tm.activo', '=', 'S')
                        ->distinct('id')->orderBy('nombre_estacion');
            }

            if ($id_proyecto) {
                $analytic_click = new ClickBotones;
                $analytic_click->id_user = $usuario->id;
                $analytic_click->id_boton = 20;
                $analytic_click->save();
                $sql_procesos = DB::table('proceso_proyectos as pp')->select(DB::raw("pp.id_proceso"))->whereIn('pp.nombre_proyecto', $id_proyecto)
                ->orWhere(function ($query) use ($id_proyecto) {
                    $query->whereIn('pp.alias_proyecto', $id_proyecto);
                })->distinct()->pluck('id_proceso')->toArray();

                $sql_estaciones = $sql_estaciones->whereIn('pm.id_proceso', $sql_procesos);
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
