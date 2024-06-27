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
use App\Models\TelemetriaResultado;
use App\Models\TelemetriaUnidad;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
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
        set_time_limit(300);
        if($request->user()->id_empresa != '947') {
            return response()->json(['message' => 'Sin autorizacion'], 400);
        }
        $estacion = $request->nombre_estacion;
        $id_parametro = $request->id_parametros;
        $id_limite = $request->id_limite;
        $tipo_data = $request->tipo_data;
        try {
            $query = DB::table('telemetria_resultados as tr')
                    ->select(DB::raw(
                        "tr.parametro_id as parametro_id,
                        tp.nombre_parametro,
                        te.nombre_estacion,
                        tm.fecha_muestreo as fecha_muestreo,
                        tr.resultado as resultado"
                    ))
                    ->join('telemetria_muestras as tm', 'tm.id', '=', 'tr.muestra_id')
                    ->join('telemetria_estacions as te', 'te.id', '=', 'tm.estacion_id')
                    ->join('telemetria_parametros as tp', 'tp.id', '=', 'tr.parametro_id')
                    ->whereIn('te.nombre_estacion', $estacion)
                    ->whereIn('tr.parametro_id', $id_parametro);
            if($tipo_data == 1){
                $query->where(function($query) {
                    $query->where('tr.estado_id', '!=', '3')
                          ->orWhereNull('tr.estado_id');
                });
            }
            if ($id_limite) {
                $query->leftJoin('telemetria_limite_parametros as tlp', function($join) use ($id_limite) {
                    $join->on('tlp.parametro_id', '=', 'tr.parametro_id')
                            ->where('tlp.limite_id', '=', $id_limite);
                });
                $query->addSelect(DB::raw("tlp.limite_inferior, tlp.limite_superior"));
            }
            $sql_data = $query->orderBy('fecha_muestreo', 'ASC')->get();
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
            $t_muestra = '';
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
            $tamañoDelChunk = 2000;
            foreach (array_chunk($data, $tamañoDelChunk) as $chunk) {
                $t_muestra = TelemetriaMuestra::insert($chunk);
            }
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($t_muestra);
    }

    public function getAllSampleByStation(Request $request)
    {
        set_time_limit(4800);
        $id_estacion = $request->id_estacion;
        try {
            $id_muestras = DB::table('telemetria_muestras as tm')->where('tm.estacion_id',$id_estacion)->select('tm.id')->get();
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($id_muestras);
    }

    public function getAllSampleID(Request $request)
    {
        $id_estacion = $request->id_estacion;
        $id_parametro = $request->id_parametro;
        set_time_limit(4800);
        ini_set('memory_limit', '2024M');
        try {
            $id_muestras = DB::table('telemetria_resultados as tr')
                                ->select(DB::raw(
                                    "                                    
                                    tm.id
                                    "
                                ))
                                ->leftJoin('telemetria_muestras as tm', 'tm.id','=','tr.muestra_id')
                                ->leftJoin('telemetria_estacions as te', 'te.id','=','tm.estacion_id')
                                ->where('tr.parametro_id', $id_parametro)
                                ->where('te.id', $id_estacion)
                                ->orderBy('fecha_muestreo', 'DESC')
                                ->distinct()
                                ->get();
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($id_muestras);
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
        if($request->user()->id_empresa != '947') {
            return response()->json(['message' => 'Sin autorizacion'], 400);
        }
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
        if($request->user()->id_empresa != '947') {
            return response()->json(['message' => 'Sin autorizacion'], 400);
        }
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
                                        ->where('tpgp.estado', 'S')
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
        if($request->user()->id_empresa != '947') {
            return response()->json(['message' => 'Sin autorizacion'], 400);
        }
        $t_estaciones = [];
        try {
            $t_estaciones = TelemetriaEstacion::select('nombre_estacion')->distinct()->get();
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($t_estaciones);
    }

    public function getAllLimites(Request $request)
    {
        if($request->user()->id_empresa != '947') {
            return response()->json(['message' => 'Sin autorizacion'], 400);
        }
        try {
            $limites = DB::table('telemetria_limites as tl')
                                        ->select(DB::raw(
                                            "                                    
                                            tl.id as id_limite,
                                            tl.nombre_limite as nombre_limite,
                                            tlp.parametro_id as id_parametro,
                                            tp.nombre_parametro as nombre_parametro,
                                            tlp.limite_inferior as limite_inferior,
                                            tlp.limite_superior as limite_superior
                                            "
                                        ))
                                        ->leftJoin('telemetria_limite_parametros as tlp', 'tlp.limite_id','=','tl.id')
                                        ->leftJoin('telemetria_parametros as tp', 'tp.id','=','tlp.parametro_id')
                                        ->orderBy('nombre_limite', 'ASC')
                                        ->get();

            $resultados = [];

            foreach ($limites as $limite) {
                if (!isset($resultados[$limite->id_limite])) {
                    $resultados[$limite->id_limite] = [
                        'id_limite' => $limite->id_limite,
                        'nombre_limite' => $limite->nombre_limite,
                        'parametros' => []
                    ];
                }
                if ($limite->id_parametro && $limite->nombre_parametro) {
                    $resultados[$limite->id_limite]['parametros'][] = [
                        'parametro_id' => $limite->id_parametro,
                        'nombre_parametro' => $limite->nombre_parametro,
                        'limite_inferior' => $limite->limite_inferior,
                        'limite_superior' => $limite->limite_superior
                    ];
                }
            }
            $resultados = array_values($resultados);

        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($resultados);
    }

    public function getDataWindRose(Request $request) 
    {
        $id_parametros = $request->id_parametros;
        $nombre_estacion = $request->nombre_estacion;

        $cacheKey = 'windrose_data_' . implode('_', $id_parametros) . '_' . implode('_', $nombre_estacion).rand(1, 1000);;
        try {
            $sql_parametro = Cache::remember($cacheKey, 30 * 60, function() use ($id_parametros, $nombre_estacion) {
                $subqueryDireccionViento = DB::table('telemetria_resultados as tr')
                    ->select('tm.fecha_muestreo', 'tr.resultado as direccion_viento')
                    ->leftJoin('telemetria_muestras as tm', 'tm.id', '=', 'tr.muestra_id')
                    ->leftJoin('telemetria_estacions as te', 'te.id', '=', 'tm.estacion_id')
                    ->where('tr.parametro_id', 14)
                    ->whereIn('te.nombre_estacion', $nombre_estacion)
                    ->where('te.nombre_archivo', 'Ruido_10min')
                    ->distinct();
    
                return DB::table('telemetria_resultados as tr')
                    ->select(DB::raw(
                        "
                        tr.parametro_id,
                        tp.nombre_parametro,
                        te.nombre_estacion,
                        tm.fecha_muestreo,
                        tr.resultado,
                        dv.direccion_viento as WindDir_D1_WVT,
                        tr.resultado as PM25_Avg
                        "
                    ))
                    ->leftJoin('telemetria_muestras as tm', 'tm.id','=','tr.muestra_id')
                    ->leftJoin('telemetria_estacions as te', 'te.id','=','tm.estacion_id')
                    ->leftJoin('telemetria_parametros as tp', 'tp.id','=','tr.parametro_id')
                    ->joinSub($subqueryDireccionViento, 'dv', function($join) {
                        $join->on('tm.fecha_muestreo', '=', 'dv.fecha_muestreo');
                    })
                    ->whereIn('tr.parametro_id', $id_parametros)
                    ->whereIn('te.nombre_estacion', $nombre_estacion)
                    ->where('tm.fecha_muestreo','>','2024-01-01')
                    ->distinct()
                    ->get();
            });
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($sql_parametro);
    }

    public function getParametroByName(Request $request)
    {
        $nombre_parametro = $request->nombre_parametro;
        try {
            $parametro = DB::table('telemetria_parametros as tp')
                ->where('nombre_parametro', $nombre_parametro)->first();    

        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($parametro);
    }

    public function getResultadoPorValidar(Request $request)
    {
        $parametro_id = $request->parametro_id;
        try {
            $resultados = DB::table('telemetria_resultados as tr')
            ->select('tm.fecha_muestreo', 'tr.muestra_id', 'te.nombre_estacion', 'tr.resultado')
            ->leftJoin('telemetria_muestras as tm', 'tm.id', '=', 'tr.muestra_id')
            ->leftJoin('telemetria_estacions as te', 'te.id', '=', 'tm.estacion_id')
            ->where(function ($query) {
                $query->where('estado_id', '=', '1')
                    ->orWhereNull('estado_id');
            })
            ->where('parametro_id', $parametro_id)->limit(5000)->get();    

        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($resultados);
    }

    public function getCriterioValidacion(Request $request)
    {
        try {
            $criterios_validacion = DB::table('telemetria_criterios_validacions as tcv')
            ->select('aplicacion','tipo_estado')->orderBy('tipo_estado', 'desc')->get();
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($criterios_validacion);
    }
    public function getAllCriterioValidacion(Request $request)
    {
        try {
            $criterios_validacion = DB::table('telemetria_criterios_validacions as tcv')
            ->orderBy('tipo_estado', 'desc')->get();
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($criterios_validacion);
    }
}
