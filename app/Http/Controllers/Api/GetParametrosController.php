<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Throwable;

class GetParametrosController extends Controller
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
        DB::enableQueryLog();
        try {
            $usuario = $request->user();
            
            $id_tipo_muestra = str_replace("TA", "", $request->id_tipo_muestra);
            $id_proyecto = str_replace("P", "", $request->id_proyecto);
            $fecha_inicio = $request->fecha_inicio;
            $fecha_fin = $request->fecha_fin;
            $estaciones = $request->estaciones;
            $fuera_limite = $request->fuera_limite;
            $id_estaciones = [];
            $id_grupo_estaciones = [];
            
            /*foreach ($estaciones as $estacion) {
                if (substr($estacion, 0, 1) == "G") {
                    array_push($id_grupo_estaciones, substr($estacion, 1));
                } else {
                    array_push($id_estaciones, substr($estacion, 1));
                }
            }*/
            /*$sql_id_estaciones_grupo = DB::table('grupo_estaciones as ge')
                                    ->select(DB::raw(
                                        "ege.id_estacion as id
                                        "
                                    ))
                                    ->join('estacion_grupo_estaciones AS ege', 'ege.id_grupo_estacion', '=', 'ge.id')
                                    ->whereIn('ege.id_grupo_estacion', $id_grupo_estaciones);
                    
            $sql_id_estaciones = DB::table('estaciones as e')
                                    ->select(DB::raw(
                                        "e.id as id
                                        "
                                    ))
                                    ->whereIn('id', $id_estaciones)
                                    ->union($sql_id_estaciones_grupo)
                                    ->get();

            $sql_id_estaciones = json_decode(json_encode($sql_id_estaciones), true);*/
            
            if ($fuera_limite) {
                $sql_parametros = DB::table('muestras as m')
                            ->select(DB::raw(
                                "case when gp.grupo_parametros is null then CONCAT('P',p.id) else CONCAT('G',gp.id) end as id,
                                case when gp.grupo_parametros is null then p.nombre_parametro else gp.grupo_parametros end as nombre_parametro
                                "
                            ))
                            ->join('muestra_parametros AS mp', 'mp.id_muestra', '=', 'm.id')
                            ->join('parametros AS p', 'mp.id_parametro', '=', 'p.id')
                            ->leftjoin('proyectos AS pr', 'pr.id', '=', 'm.id_proyecto')
                            ->leftjoin('parametro_grupo_parametros AS pgp', 'pgp.id_parametro', '=', 'p.id')
                            ->leftjoin('grupo_parametros AS gp', 'gp.id', '=', 'pgp.id_grupo_parametro')
                            ->where('m.fecha_muestreo', '>', $fecha_inicio)
                            ->where('m.fecha_muestreo', '<', $fecha_fin)
                            ->where('mp.id_parecer', '=', 2)
                            ->where('m.id_tipo_muestra', '=', $id_tipo_muestra)
                            ->whereIn('m.id_estacion', $sql_id_estaciones)
                            ->where('m.activo', '=', 'S')
                            ->distinct()->orderBy('nombre_parametro');
            } else {
                $sql_parametros = DB::table('muestras as m')
                            ->select(DB::raw(
                                "case when gp.grupo_parametros is null then CONCAT('P',p.id) else CONCAT('G',gp.id) end as id,
                                case when gp.grupo_parametros is null then p.nombre_parametro else gp.grupo_parametros end as nombre_parametro
                                "
                            ))
                            ->leftjoin('proyectos AS pr', 'pr.id', '=', 'm.id_proyecto')
                            ->join('estaciones AS e', 'e.id', '=', 'm.id_estacion')
                            ->join('muestra_parametros AS mp', 'mp.id_muestra', '=', 'm.id')
                            ->leftjoin('metodos as me', 'me.id', '=', 'mp.id_metodo')
                            ->join('parametros AS p', 'mp.id_parametro', '=', 'p.id')
                            ->leftjoin('parametro_grupo_parametros AS pgp', function ($join) {
                                $join->on('pgp.id_parametro', '=', 'p.id')
                                     ->on('pgp.idaux_metodo', '=', 'me.idaux_metodo');
                            })
                            ->leftjoin('grupo_parametros AS gp', 'gp.id', '=', 'pgp.id_grupo_parametro')
                            ->where('m.id_tipo_muestra', '=', $id_tipo_muestra)
                            ->whereIn('e.nombre_estacion',$estaciones)
                            ->orWhere(function ($query) use ($estaciones) {
                                $query->whereIn('e.alias_estacion', $estaciones);
                            })
                            //->whereIn('m.id_estacion', $sql_id_estaciones)
                            ->whereIn('m.id_estado', [3,4])
                            ->where('m.activo', '=', 'S')
                            ->distinct()->orderBy('nombre_parametro');
            }
            //dd($sql_id_estaciones);
            $sql_parametros = filtroMuestrasQuery($sql_parametros,$usuario);

            if ($id_proyecto) {
                /*foreach ($id_proyecto as $key => $value) {
                    $id_proyecto[$key] = str_replace("P", "", $value);
                }*/

                $sql_parametros = $sql_parametros->where(function ($query) use ($id_proyecto) {
                    $query->whereIn('pr.nombre_proyecto', $id_proyecto)
                          ->orWhereIn('pr.alias_proyecto', $id_proyecto);
                });
            }

           

            $sql_parametros = $sql_parametros->get();

            $queries = DB::getQueryLog();
            // Recorre cada consulta SQL y mide su tiempo de ejecución
           foreach ($queries as $query) {
               $sql = $query['query'];
               $bindings = $query['bindings'];
               $time = $query['time'];
               $fullSql = vsprintf(str_replace('?', '%s', $sql), $bindings);
               \Log::info('Consulta SQL: ' . $fullSql . ' - Tiempo de ejecución: ' . $time . ' segundos');
           }
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
