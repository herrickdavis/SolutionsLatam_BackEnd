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
            //$id_empresa_sol = $request->user()->id_empresa;
            $usuario = $request->user();

            $id_tipo_muestra = str_replace("TA", "", $request->id_tipo_muestra);
            $fecha_inicio = $request->fecha_inicio;
            $fecha_fin = $request->fecha_fin;
            $fuera_limite = $request->fuera_limite;
            $id_proyecto = $request->id_proyecto;


            if ($fuera_limite) {
                $sql_estaciones = DB::table('muestras as m')
                        ->select(DB::raw(
                            /*"case when ge.grupo_estacion is null then CONCAT('E',e.id) else CONCAT('G',ge.id) end as id,
                            case when ge.grupo_estacion is null then e.nombre_estacion else ge.grupo_estacion end as nombre_estacion
                            "*/
                            "
                            case when e.alias_estacion is null then e.nombre_estacion else e.alias_estacion end as id,
                            case when e.alias_estacion is null then e.nombre_estacion else e.alias_estacion end as nombre_estacion
                            "
                        ))
                        ->join('muestra_parametros AS mp', 'mp.id_muestra', '=', 'm.id')
                        ->leftjoin('estaciones AS e', 'm.id_estacion', '=', 'e.id')
                        /*->leftjoin('estacion_grupo_estaciones AS ege', 'ege.id_estacion', '=', 'e.id')
                        ->leftjoin('grupo_estaciones AS ge', 'ge.id', '=', 'ege.id_grupo_estacion')*/
                        ->leftjoin('proyectos AS p', 'p.id', '=', 'm.id_proyecto')
                        ->leftjoin('tipo_muestras AS tm', 'tm.id', '=', 'm.id_tipo_muestra')
                        ->where('mp.id_parecer', '=', 3)
                        ->where('m.fecha_muestreo', '>', $fecha_inicio)
                        ->where('m.fecha_muestreo', '<', $fecha_fin)
                        ->where('m.id_tipo_muestra', '=', $id_tipo_muestra)
                        ->where('m.activo', '=', 'S')
                        ->where('tm.activo', '=', 'S')
                        ->distinct('id')->orderBy('nombre_estacion');
            //->get();
            } else {
                $sql_estaciones = DB::table('muestras as m')
                        ->select(DB::raw(
                            /*"case when ge.grupo_estacion is null then CONCAT('E',e.id) else CONCAT('G',ge.id) end as id,
                            case when ge.grupo_estacion is null then e.nombre_estacion else ge.grupo_estacion end as nombre_estacion
                            "*/
                            "
                            case when e.alias_estacion is null then e.nombre_estacion else e.alias_estacion end as id,
                            case when e.alias_estacion is null then e.nombre_estacion else e.alias_estacion end as nombre_estacion
                            "
                        ))
                        ->leftjoin('estaciones AS e', 'm.id_estacion', '=', 'e.id')
                        /*->leftjoin('estacion_grupo_estaciones AS ege', 'ege.id_estacion', '=', 'e.id')
                        ->leftjoin('grupo_estaciones AS ge', 'ge.id', '=', 'ege.id_grupo_estacion')*/
                        ->leftjoin('proyectos AS p', 'p.id', '=', 'm.id_proyecto')
                        ->leftjoin('tipo_muestras AS tm', 'tm.id', '=', 'm.id_tipo_muestra')
                        ->whereIn('m.id_estado', [3,4])
                        ->where('m.id_tipo_muestra', '=', $id_tipo_muestra)
                        ->where('m.activo', '=', 'S')
                        ->where('tm.activo', '=', 'S')
                        ->distinct('id')->orderBy('nombre_estacion');
            }

            $sql_estaciones = filtroMuestrasQuery($sql_estaciones,$usuario);

            if ($id_proyecto) {
                $analytic_click = new ClickBotones;
                $analytic_click->id_user = $usuario->id;
                $analytic_click->id_boton = 20;
                $analytic_click->save();
                /*foreach ($id_proyecto as $key => $value) {
                    $id_proyecto[$key] = str_replace("P", "", $value);
                }*/
                
                $sql_estaciones = $sql_estaciones->whereIn('p.nombre_proyecto', $id_proyecto)
                                                ->orWhere(function ($query) use ($id_proyecto) {
                                                    $query->whereIn('p.alias_proyecto', $id_proyecto);
                                                });
            }

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
