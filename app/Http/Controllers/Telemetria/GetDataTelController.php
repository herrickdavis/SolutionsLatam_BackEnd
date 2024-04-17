<?php

namespace App\Http\Controllers\Telemetria;

use App\Http\Controllers\Controller;
use App\Models\TelemetriaAbreviaturaProcesamiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TelemetriaEstacion;
use App\Models\TelemetriaGrupoParametro;
use App\Models\TelemetriaMuestra;
use App\Models\TelemetriaParametro;
use App\Models\TelemetriaProyecto;
use App\Models\TelemetriaUnidad;
use Carbon\Carbon;
use Throwable;

class GetDataTelController extends Controller
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
        $estacion = $request->nombre_estacion;
        $id_parametro = $request->id_parametros;

        try {
            $sql_data = DB::table('telemetria_resultados as tr')
                                ->select(DB::raw(
                                    "
                                    tr.parametro_id as parametro_id,
                                    tp.nombre_parametro,
                                    te.nombre_estacion,
                                    tm.fecha_muestreo as fecha_muestreo,
                                    tr.resultado as resultado
                                    "
                                ))
                                ->join('telemetria_muestras as tm', 'tm.id', '=', 'tr.muestra_id')
                                ->join('telemetria_estacions as te', 'te.id', '=', 'tm.estacion_id')
                                ->join('telemetria_parametros as tp', 'tp.id', '=', 'tr.parametro_id')
                                ->whereIn('te.nombre_estacion', $estacion)
                                ->whereIn('tr.parametro_id', $id_parametro)
                                ->orderBy('fecha_muestreo', 'ASC')
                                ->get();

        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return $sql_data;
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

    public function getLastSample(Request $request)
    {
        $nombre_estacion = $request->nombre_estacion;
        $nombre_archivo = $request->nombre_archivo;
        $nombre_parametro = $request->nombre_parametro;
        try {
            $sql_data = DB::table('telemetria_resultados as tr')
                                ->select(DB::raw(
                                    "
                                    tm.fecha_muestreo
                                    "
                                ))
                                ->leftJoin('telemetria_muestras as tm', 'tm.id','=','tr.muestra_id')
                                ->leftJoin('telemetria_estacions as te', 'te.id','=','tm.estacion_id')
                                ->leftJoin('telemetria_parametros as tp', 'tp.id','=','tr.parametro_id')
                                ->where('te.nombre_archivo',$nombre_archivo)
                                ->where('te.nombre_estacion',$nombre_estacion)
                                ->where('tp.nombre_parametro',$nombre_parametro)
                                ->orderBy('fecha_muestreo', 'DESC')
                                ->first();

        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($sql_data);
    }

    public function getCompareSample(Request $request)
    {
        $nombre_estacion = $request->nombre_estacion;
        $nombre_archivo = $request->nombre_archivo;

        try {
            $sql_data = DB::table('telemetria_muestras as tm')
                                ->select(DB::raw(
                                    "                                    
                                    tm.fecha_muestreo
                                    "
                                ))
                                ->leftJoin('telemetria_estacions as te', 'te.id','=','tm.estacion_id')
                                ->where('tm.nombre_archivo',$nombre_archivo)
                                ->where('te.nombre_estacion',$nombre_estacion)
                                ->orderBy('fecha_muestreo', 'DESC')
                                ->first();

        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($sql_data);
    }

    public function getStationByName(Request $request)
    {
        $nombre_estacion = $request->nombre_estacion;
        $id_empresa = $request->id_empresa;
        $id_proyecto = $request->id_proyecto;
        $nombre_archivo = $request->nombre_archivo;

        try {
            $t_estacion = TelemetriaEstacion::firstOrCreate(
                [
                    'nombre_estacion' => $nombre_estacion,
                    'nombre_archivo' => $nombre_archivo,
                    'id_empresa' => $id_empresa,
                    'id_proyecto_telemetria' => $id_proyecto
                ]
            );

        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($t_estacion);
    }

    public function getProjectByName(Request $request)
    {
        $nombre_proyecto = $request->nombre_proyecto;
        $id_empresa = $request->id_empresa;
        try {
            $t_proyecto = TelemetriaProyecto::firstOrCreate(
                [
                    'nombre_proyecto' => $nombre_proyecto,
                    'id_empresa' => $id_empresa
                ]
            );

        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($t_proyecto);
    }

    public function getAllSample(Request $request)
    {
        set_time_limit(4800);
        try {
            $data = [];
            foreach ($request->all() as $value) {
                $muestra = $value;
                $fecha_muestreo = $value['fecha_muestreo'];
                $fecha_muestreo = Carbon::createFromTimestamp($fecha_muestreo / 1000)->format('Y-m-d H:i:s');
                $muestra['fecha_muestreo'] = $fecha_muestreo;
                $fecha = substr($value['fecha_muestreo'], 0, -3);
                $id_estacion = str_pad($value['estacion_id'], 4, '0', STR_PAD_LEFT);
                $muestra['id'] = $fecha.$id_estacion;
                $muestra['created_at'] = now();
                $muestra['updated_at'] = now();
                array_push($data,$muestra);
            }
            $t_muestra = TelemetriaMuestra::insert($data);

        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($t_muestra);
    }

    public function getAllParameter(Request $request)
    {
        set_time_limit(2400);
        $ids_parametro = [];
        try {
            foreach ($request->all() as $value) {
                $nombre_parametro = $value['nombre_parametro'];
                $t_parametro = TelemetriaParametro::firstOrCreate(
                    [
                        'nombre_parametro' => $nombre_parametro
                    ]
                );
                $data = [
                    'nombre_parametro' => $nombre_parametro,
                    'parametro_id' => $t_parametro->id
                ];

                array_push($ids_parametro,$data);
            }

        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($ids_parametro);
    }

    public function getParameters(Request $request)
    {
        $parametros = [];
        try {
            $parametros = TelemetriaParametro::all();
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($parametros);
    }

    public function getAllGroup(Request $request)
    {
        $resultadoAgrupado = [];
        try {
            $grupo_parametros = DB::table('telemetria_grupo_parametros as tgp')
                                        ->select(DB::raw(
                                            "                                    
                                            tgp.id as id_grupo,
                                            tgp.nombre_grupo_parametro as nombre_grupo_parametro,
                                            tp.id as id_parametro,
                                            tp.nombre_parametro as nombre_parametro
                                            "
                                        ))
                                        ->leftJoin('telemetria_parametro_grupo_parametros as tpgp', 'tpgp.grupo_parametro_id','=','tgp.id')
                                        ->leftJoin('telemetria_parametros as tp', 'tp.id','=','tpgp.parametro_id')
                                        ->orderBy('nombre_grupo_parametro', 'ASC')
                                        ->orderBy('nombre_parametro', 'ASC')
                                        ->get();

            $coleccion = collect($grupo_parametros);

            // Agrupamos los resultados por 'id_grupo'
            $grupos = $coleccion->groupBy('id_grupo');

            $resultadoAgrupado = $grupos->map(function ($items, $idGrupo) {
                // Asumimos que todos los elementos de un grupo específico tienen el mismo nombre de grupo
                $nombreGrupoParametro = $items->first()->nombre_grupo_parametro;
                
                // Filtramos y estructuramos los parámetros de cada grupo
                $parametros = $items->map(function ($item) {
                    // Solo retornamos parámetros si el id_parametro no es nulo
                    return isset($item->id_parametro) ? [
                        'id_parametro' => $item->id_parametro,
                        'nombre_parametro' => $item->nombre_parametro
                    ] : null;
                })->filter()->values(); // Filtramos para eliminar los nulos y reindexamos
                
                return [
                    'id_grupo' => $idGrupo,
                    'nombre_grupo_parametro' => $nombreGrupoParametro,
                    'parametros' => $parametros
                ];
            })->values();;                
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($resultadoAgrupado);

    }

    public function getAllUnit(Request $request)
    {
        set_time_limit(2400);
        $ids_unit = [];
        try {
            foreach ($request->all() as $value) {
                $unidad = $value['unidad'];
                $t_unidad = TelemetriaUnidad::firstOrCreate(
                    [
                        'nombre_unidad' => $unidad
                    ]
                );
                $data = [
                    'unidad' => $unidad,
                    'unidad_id' => $t_unidad->id
                ];

                array_push($ids_unit,$data);
            }

        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($ids_unit);
    }

    public function getAllAbreviatura(Request $request)
    {
        set_time_limit(2400);
        $ids_abreviatura = [];
        try {
            foreach ($request->all() as $value) {
                $abreviatura = $value['abreviatura'];
                $t_abreviatura = TelemetriaAbreviaturaProcesamiento::firstOrCreate(
                    [
                        'nombre_abreviatura' => $abreviatura
                    ]
                );
                $data = [
                    'abreviatura' => $abreviatura,
                    'abreviatura_id' => $t_abreviatura->id
                ];

                array_push($ids_abreviatura,$data);
            }

        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($ids_abreviatura);
    }

    public function getAllStation(Request $request)
    {
        $t_estaciones = [];
        try {
            $t_estaciones = TelemetriaEstacion::select('nombre_estacion')->distinct()->get();
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($t_estaciones);
    }
}
