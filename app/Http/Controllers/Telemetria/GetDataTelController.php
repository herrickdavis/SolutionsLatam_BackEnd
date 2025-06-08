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
use Illuminate\Pagination\Paginator;
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

    public function store(Request $request) 
    {
        if ($request->user()->id_empresa != '947') {
            return response()->json(['message' => 'Sin autorización'], 400);
        }

        $estacion = $request->nombre_estacion;
        $id_parametro = $request->id_parametro;
        $tipo_data = $request->tipo_data;
        $fecha_inicio = $request->fecha_inicio;
        $fecha_fin = $request->fecha_fin;
        $id_limite = $request->id_limite;

        $id_estaciones = DB::table('telemetria_estacions as te')
                                ->whereIn('te.nombre_estacion', $estacion)
                                ->pluck('id')
                                ->toArray();

        $id_tipo_parametro = DB::table('telemetria_parametros as tp')->where('tp.id', $id_parametro)->value('id_tipo_parametro');
        
        if ($id_tipo_parametro == '2') {
            $query = DB::table('telemetria_data_procesadas as tr')
                ->select(
                    'tr.parametro_id as parametro_id',
                    'tp.nombre_parametro',
                    'te.nombre_estacion',
                    DB::raw("concat(tr.fecha_muestreo, ' 12:00:00') as fecha_muestreo"),
                    'tr.resultado as resultado',
                    'tu.nombre_unidad as unidad',
                    'tr.estado_id'
                )
                ->leftJoin('telemetria_estacions as te', 'te.id', '=', 'tr.estacion_id')
                ->leftJoin('telemetria_parametros as tp', 'tp.id', '=', 'tr.parametro_id')
                ->leftJoin('telemetria_unidads as tu', 'tu.id', '=', 'tr.unidad_id')
                ->whereIn('tr.estacion_id', $id_estaciones)
                ->where('tr.parametro_id', $id_parametro)
                ->where('tr.fecha_muestreo', '>', $fecha_inicio)
                ->where('tr.fecha_muestreo', '<', $fecha_fin);
            
            if ($id_limite) {
                // Hacemos leftJoin con la tabla de límites si $id_limite está definido
                $query = $query->leftJoin('telemetria_limite_parametros as tlp', function($join) use ($id_limite, $id_parametro) {
                    $join->on('tlp.parametro_id', '=', 'tr.parametro_id')
                        ->where('tlp.limite_id', '=', $id_limite);
                });

                // Seleccionamos también los límites en el select
                $query->addSelect('tlp.limite_inferior', 'tlp.limite_superior');
            }

            return $query->orderBy('tr.fecha_muestreo', 'ASC')->get();
        } else {
            // Si no hay límites definidos ($id_limite), no incluimos límites en la consulta
            $query = DB::table('telemetria_muestras as tm')
                ->select(
                    'tr.parametro_id',
                    'tp.nombre_parametro',
                    'te.nombre_estacion',
                    'tm.fecha_muestreo as fecha_muestreo',
                    'tr.resultado as resultado',
                    'tr.direccion_viento as WindDir_D1_WVT',
                    'tu.nombre_unidad as unidad',
                    'tr.estado_id'
                )
                ->leftJoin('telemetria_estacions as te', 'te.id', '=', 'tm.estacion_id')
                ->leftJoin('telemetria_resultados as tr', 'tm.id', '=', 'tr.muestra_id')
                ->leftJoin('telemetria_parametros as tp', 'tp.id', '=', 'tr.parametro_id')
                ->leftJoin('telemetria_unidads as tu', 'tu.id', '=', 'tr.unidad_id');

            if ($id_limite) {
                // Si $id_limite está definido, hacemos una left join con la tabla de límites
                $query = $query->leftJoin('telemetria_limite_parametros as tlp', function($join) use ($id_limite) {
                    $join->on('tlp.parametro_id', '=', 'tr.parametro_id')
                        ->where('tlp.limite_id', '=', $id_limite);
                });
                // También seleccionamos los límites
                $query->addSelect('tlp.limite_inferior', 'tlp.limite_superior');
            }

            $query = $query->whereIn('tm.estacion_id', $id_estaciones)
                ->where('tr.parametro_id', $id_parametro)
                ->where('tm.fecha_muestreo', '>', $fecha_inicio)
                ->where('tm.fecha_muestreo', '<', $fecha_fin)
                ->orderBy('tm.fecha_muestreo', 'ASC');

            return $query->get();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store2(Request $request)
    {
        set_time_limit(600);

        if ($request->user()->id_empresa != '947') {
            return response()->json(['message' => 'Sin autorización'], 400);
        }

        $estacion = $request->nombre_estacion;
        $id_parametro = $request->id_parametros;
        $id_limite = $request->id_limite;
        $tipo_data = $request->tipo_data;

        try {
            // Obtener los IDs de estaciones
            $id_estaciones = DB::table('telemetria_estacions as te')
                                ->whereIn('te.nombre_estacion', $estacion)
                                ->pluck('id')
                                ->toArray();

            $resultadosCombinados = [];

            // Cachear resultados para cada combinación de estación y parámetro
            foreach ($id_estaciones as $estacionId) {
                foreach ($id_parametro as $parametroId) {
                    // Obtener el id_tipo_parametro para este parámetro
                    $id_tipo_parametro = DB::table('telemetria_parametros as tp')->where('tp.id', $parametroId)->value('id_tipo_parametro');

                    // Generar una clave de caché única para cada combinación de estación y parámetro
                    $cacheKey = 'telemetria_estacion_' . $estacionId . '_parametro_' . $parametroId;

                    // Obtener datos cacheados si existen
                    $datosEstacionParametro = Cache::get($cacheKey, []);

                    // Comprobar si hay datos en caché
                    if (!empty($datosEstacionParametro)) {
                        // Convertir a colección y obtener la última fecha_muestreo
                        $datosEstacionParametro = collect($datosEstacionParametro);
                        $ultimaFecha = $datosEstacionParametro->max('fecha_muestreo');

                        // Obtener la fecha de la última actualización desde el caché
                        $cacheTimestamp = Cache::get($cacheKey . '_timestamp', now());

                        // Verificar si ha pasado más de 1 hora desde la última actualización
                        $intervalo = (new \DateTime($cacheTimestamp))->diff(new \DateTime());
                        if ($intervalo->h >= 1) {
                            // Obtener datos adicionales desde la última fecha_muestreo
                            $nuevosDatos = $this->obtenerDatosNuevos($estacionId, $parametroId, $id_tipo_parametro, $ultimaFecha);

                            // Combinar datos existentes con los nuevos
                            $datosEstacionParametro = $datosEstacionParametro->merge($nuevosDatos);

                            // Actualizar la caché con la nueva combinación de datos, usando Cache::forever()
                            Cache::forever($cacheKey, $datosEstacionParametro->toArray());
                            Cache::forever($cacheKey . '_timestamp', now());
                        }
                    } else {
                        // Si la caché está vacía, obtener todos los datos y almacenarlos en caché
                        $datosEstacionParametro = $this->obtenerDatosNuevos($estacionId, $parametroId, $id_tipo_parametro);
                        Cache::forever($cacheKey, $datosEstacionParametro->toArray());
                        Cache::forever($cacheKey . '_timestamp', now());
                    }

                    // Combinar los resultados cacheados de esta estación y parámetro con el resultado general
                    $resultadosCombinados = array_merge($resultadosCombinados, $datosEstacionParametro->toArray());
                }
            }

            // Convertir los resultados combinados a una colección para aplicar los filtros
            $sql_data = collect($resultadosCombinados);

            // Aplicar filtros adicionales después de recuperar el caché
            if ($tipo_data == 1) {
                $sql_data = $sql_data->filter(function ($item) {
                    return $item->estado_id != '3' || is_null($item->estado_id);
                });
            }

            // Aplicar límites si están definidos
            if ($id_limite) {
                // Obtener los límites desde la base de datos
                $limites = DB::table('telemetria_limite_parametros as tlp')
                    ->where('tlp.limite_id', $id_limite)
                    ->whereIn('tlp.parametro_id', $id_parametro)
                    ->select('tlp.parametro_id', 'tlp.limite_inferior', 'tlp.limite_superior')
                    ->get()
                    ->keyBy('parametro_id'); // Indexar por 'parametro_id' para fácil acceso

                // Agregar límites a los datos
                $sql_data = $sql_data->map(function ($item) use ($limites) {
                    if (isset($limites[$item->parametro_id])) {
                        $item->limite_inferior = $limites[$item->parametro_id]->limite_inferior;
                        $item->limite_superior = $limites[$item->parametro_id]->limite_superior;
                    } else {
                        $item->limite_inferior = null;
                        $item->limite_superior = null;
                    }
                    return $item;
                });
            }

        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return $sql_data;
    }

    // Método para obtener los datos nuevos desde la última fecha
    private function obtenerDatosNuevos($estacionId, $parametroId, $id_tipo_parametro, $ultimaFecha = null)
    {
        if ($id_tipo_parametro == '2') {
            $query = DB::table('telemetria_data_procesadas as tr')
                ->select(
                    'tr.parametro_id as parametro_id',
                    'tp.nombre_parametro',
                    'te.nombre_estacion',
                    DB::raw("concat(tr.fecha_muestreo, ' 12:00:00') as fecha_muestreo"),
                    'tr.resultado as resultado',
                    'tu.nombre_unidad as unidad',
                    'tr.estado_id'
                )
                ->join('telemetria_estacions as te', 'te.id', '=', 'tr.estacion_id')
                ->join('telemetria_parametros as tp', 'tp.id', '=', 'tr.parametro_id')
                ->join('telemetria_unidads as tu', 'tu.id', '=', 'tr.unidad_id')
                ->where('te.id', $estacionId)
                ->where('tr.parametro_id', $parametroId);

            if ($ultimaFecha) {
                $query->where('tr.fecha_muestreo', '>', $ultimaFecha);
            } else {
                $query->where('tr.fecha_muestreo', '>', '2024-01-01');
            }

            return $query->orderBy('tr.fecha_muestreo', 'ASC')->get();
        } else {
            $query = DB::table('telemetria_muestras as tm')
                ->select(
                    'tr.parametro_id as parametro_id',
                    'tp.nombre_parametro',
                    'te.nombre_estacion',
                    'tm.fecha_muestreo as fecha_muestreo',
                    'tr.resultado as resultado',
                    'tu.nombre_unidad as unidad',
                    'tr.estado_id'
                )
                ->leftJoin('telemetria_estacions as te', 'te.id', '=', 'tm.estacion_id')
                ->leftJoin('telemetria_resultados as tr', 'tm.id', '=', 'tr.muestra_id')
                ->leftJoin('telemetria_parametros as tp', 'tp.id', '=', 'tr.parametro_id')
                ->leftJoin('telemetria_unidads as tu', 'tu.id', '=', 'tr.unidad_id')
                ->where('te.id', $estacionId)
                ->where('tr.parametro_id', $parametroId);

            if ($ultimaFecha) {
                $query->where('tm.fecha_muestreo', '>', $ultimaFecha);
            } else {
                $query->where('tr.fecha_muestreo', '>', '2024-01-01');
            }

            return $query->orderBy('tm.fecha_muestreo', 'ASC')->get();
        }
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
                $nombre_archivo = $value['nombre_archivo'];
                $fecha_muestreo = $value['fecha_muestreo'];
                $fecha_muestreo = Carbon::createFromTimestamp($fecha_muestreo / 1000)->format('Y-m-d H:i:s');
                $muestra['fecha_muestreo'] = $fecha_muestreo;
                $fecha = substr($value['fecha_muestreo'], 0, -3);
                $id_estacion = str_pad($value['estacion_id'], 4, '0', STR_PAD_LEFT);
                
                $nombre_archivo_hash = 0;
                for ($i = 0; $i < strlen($nombre_archivo); $i++) {
                    $nombre_archivo_hash += ord($nombre_archivo[$i]);
                }
                $nombre_archivo_hash = $nombre_archivo_hash % 10000;
                $nombre_archivo_hash_str = sprintf('%05d', $nombre_archivo_hash);

                $muestra['id'] = $fecha.$id_estacion.$nombre_archivo_hash_str;
                $muestra['nombre_archivo'] = $nombre_archivo;
                $muestra['created_at'] = now();
                $muestra['updated_at'] = now();
                array_push($data,$muestra);
            }
            $tamañoDelChunk = 2000;
            foreach (array_chunk($data, $tamañoDelChunk) as $chunk) {
                $t_muestra = TelemetriaMuestra::insertOrIgnore($chunk);
            }
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($t_muestra);
    }

    public function getIDInformacion(Request $request) 
    {
        $this->insertIgnore('telemetria_unidads', $request->input('unidades'), 'nombre_unidad');
        $this->insertIgnore('telemetria_parametros', $request->input('parametros'), 'nombre_parametro');
        $this->insertIgnore('telemetria_estacions', $request->input('estaciones'), 'nombre_estacion');
        $this->insertIgnore('telemetria_abreviatura_procesamientos', $request->input('abreviaturas'), 'nombre_abreviatura');

        $id_unidades = DB::table('telemetria_unidads')->select('id', 'nombre_unidad')->get();
        $id_parametros = DB::table('telemetria_parametros')->select('id', 'nombre_parametro')->get();
        $id_estaciones = DB::table('telemetria_estacions')->select('id', 'nombre_estacion')->get();
        $id_abreviaturas = DB::table('telemetria_abreviatura_procesamientos')->select('id', 'nombre_abreviatura')->get();

        $resultados = [
            'unidades' => $id_unidades,
            'parametros' => $id_parametros,
            'estaciones' => $id_estaciones,
            'abreviaturas' => $id_abreviaturas
        ];

        return response()->json($resultados);
    }

    private function insertIgnore($table, $names, $name_field)
    {
        $insertValues = [];
        foreach ($names as $name) {
            if ($table == 'telemetria_estacions') {
                $insertValues[] = [
                    $name_field => $name,
                    'id_empresa' => 947,
                    'id_proyecto_telemetria' => 1,
                ];
            } else {
                $insertValues[] = [
                    $name_field => $name
                ];
            }
        }

        DB::table($table)->insertOrIgnore($insertValues);
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
                        'nombre_parametro' => $nombre_parametro,
                        'id_tipo_parametro' => 1
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
            // Realizamos la consulta con la lógica de concatenación condicional
            $parametros = TelemetriaParametro::select('id', 
                DB::raw("CASE WHEN id_tipo_parametro = 2 THEN CONCAT(nombre_parametro, ' (P)') ELSE nombre_parametro END as nombre_parametro"))
                ->get();
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json($parametros);
    }

    public function getParametersLocal(Request $request)
    {
        try {
            // Realizamos la consulta con la lógica de concatenación condicional
            $parametros = TelemetriaParametro::select('id', 'nombre_parametro')->get();
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
                                            CASE WHEN tp.id_tipo_parametro = 2 THEN CONCAT(tp.nombre_parametro, ' (P)') ELSE tp.nombre_parametro END as nombre_parametro
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

    public function getAllStationExternal(Request $request)
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

        // Generar una clave de caché única basada en estación y parámetros
        $cacheKey = 'windrose_data_' . implode('_', $id_parametros) . '_' . implode('_', $nombre_estacion);

        try {
            // Obtener datos cacheados si existen
            $datosCacheados = Cache::get($cacheKey, []);

            // Comprobar si hay datos en caché
            if (!empty($datosCacheados)) {
                // Convertir a colección y obtener la última fecha_muestreo
                $datosCacheados = collect($datosCacheados);
                $ultimaFecha = $datosCacheados->max('fecha_muestreo');

                // Obtener la fecha de la última actualización desde el caché
                $cacheTimestamp = Cache::get($cacheKey . '_timestamp', now());

                // Verificar si ha pasado más de 1 hora desde la última actualización
                $intervalo = (new \DateTime($cacheTimestamp))->diff(new \DateTime());
                if ($intervalo->h >= 1) {
                    // Obtener datos adicionales desde la última fecha_muestreo
                    $nuevosDatos = $this->obtenerDatosWindRose($id_parametros, $nombre_estacion, $ultimaFecha);

                    // Combinar datos existentes con los nuevos
                    $datosCacheados = $datosCacheados->merge($nuevosDatos);

                    // Actualizar la caché con la nueva combinación de datos, usando Cache::forever()
                    Cache::forever($cacheKey, $datosCacheados->toArray());
                    Cache::forever($cacheKey . '_timestamp', now());
                }
            } else {
                // Si la caché está vacía, obtener todos los datos y almacenarlos en caché
                $datosCacheados = $this->obtenerDatosWindRose($id_parametros, $nombre_estacion);
                Cache::forever($cacheKey, $datosCacheados->toArray());
                Cache::forever($cacheKey . '_timestamp', now());
            }
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json($datosCacheados);
    }

    // Método para obtener los datos (nuevos o completos)
    private function obtenerDatosWindRose($id_parametros, $nombre_estacion, $ultimaFecha = null)
    {
        if ($id_parametros[0] > 73) {
            $query = DB::table('telemetria_data_procesadas as tr')
                        ->select(
                            'tr.parametro_id',
                            'tp.nombre_parametro',
                            'te.nombre_estacion',
                            DB::raw("concat(tr.fecha_muestreo, ' 12:00:00') as fecha_muestreo"),
                            'tr.resultado',
                            'dv.direccion_viento as WindDir_D1_WVT',
                            'tr.resultado as PM25_Avg'
                        )
                        ->leftJoin('telemetria_estacions as te', 'te.id', '=', 'tr.estacion_id')
                        ->leftJoin('telemetria_parametros as tp', 'tp.id', '=', 'tr.parametro_id')
                        ->whereIn('tr.parametro_id', $id_parametros)
                        ->whereIn('te.nombre_estacion', $nombre_estacion);

            if ($ultimaFecha) {
                $query->where('tr.fecha_muestreo', '>', $ultimaFecha);
            } else {
                $query->where('tr.fecha_muestreo', '>', '2024-01-01');
            }

            return $query->get();
        } else {
            $query = DB::table('telemetria_resultados as tr')
                        ->select(DB::raw(
                            "
                            tr.parametro_id,
                            tp.nombre_parametro,
                            te.nombre_estacion,
                            tm.fecha_muestreo,
                            tr.resultado,
                            tr.direccion_viento as WindDir_D1_WVT,
                            tr.resultado as PM25_Avg
                            "
                        ))
                        ->leftJoin('telemetria_muestras as tm', 'tm.id', '=', 'tr.muestra_id')
                        ->leftJoin('telemetria_estacions as te', 'te.id', '=', 'tm.estacion_id')
                        ->leftJoin('telemetria_parametros as tp', 'tp.id', '=', 'tr.parametro_id')
                        ->whereIn('tr.parametro_id', $id_parametros)
                        ->whereIn('te.nombre_estacion', $nombre_estacion);

            if ($ultimaFecha) {
                $query->where('tm.fecha_muestreo', '>', $ultimaFecha);
            } else {
                $query->where('tm.fecha_muestreo', '>', '2024-01-01');
            }

            return $query->get();
        }
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
        $fecha_limite = Carbon::now()->subHours(12);
        $parametros_id = $request->parametros_id;
        $numero_registros = $request->registros;
        $page = $request->input('page', 1);
        // Establecer la página actual para el paginador
        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        try {
            $resultados = DB::table('telemetria_resultados as tr')
            ->select('tm.fecha_muestreo', 'tr.muestra_id', 'te.nombre_estacion', 'tr.parametro_id', 'tr.resultado')
            ->leftJoin('telemetria_muestras as tm', 'tm.id', '=', 'tr.muestra_id')
            ->leftJoin('telemetria_estacions as te', 'te.id', '=', 'tm.estacion_id')
            ->where(function ($query) {
                $query->where('estado_id', '=', '1')
                    ->orWhereNull('estado_id');
            })
            ->where('tm.fecha_muestreo', '>', $fecha_limite)
            ->paginate($numero_registros);

        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($resultados);
    }

    public function getResultadoPorProcesar(Request $request)
    {
        $parametro_id = $request->parametro_id;
        $nombre_estacion = $request->nombre_estacion;
        $fecha_limite = Carbon::now()->subDays(2);

        // Obtener fecha_inicio y fecha_fin del request
        $fecha_inicio = $request->fecha_inicio;
        $fecha_fin = $request->fecha_fin;

        try {
            if ($nombre_estacion == "") {
                $resultados = TelemetriaEstacion::select('nombre_estacion')->distinct()->get();
            } else {
                // Construir la consulta de manera incremental
                $query = DB::table('telemetria_resultados as tr')
                    ->select(
                        'tm.fecha_muestreo',
                        'tm.estacion_id',
                        'tr.muestra_id',
                        'te.nombre_estacion',
                        'tr.resultado',
                        'tr.unidad_id'
                    )
                    ->leftJoin('telemetria_muestras as tm', 'tm.id', '=', 'tr.muestra_id')
                    ->leftJoin('telemetria_estacions as te', 'te.id', '=', 'tm.estacion_id')
                    ->where('te.nombre_estacion', $nombre_estacion)
                    ->where(function ($query) {
                        $query->where('estado_id', '<>', '3')
                            ->orWhereNull('estado_id');
                    })
                    ->where('parametro_id', $parametro_id);

                // Aplicar filtros de fecha
                if ($fecha_inicio && $fecha_fin) {
                    $query->whereBetween('fecha_muestreo', [$fecha_inicio, $fecha_fin]);
                } else {
                    $query->where('fecha_muestreo', '>', $fecha_limite);
                }

                $resultados = $query->get();
            }
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json($resultados);
    }


    public function getResultadoPorProcesarRuido(Request $request)
    {
        $tipo = $request->tipo;
        $nombre_estacion = $request->nombre_estacion;

        // Fecha actual menos 2 días
        $fecha_limite = Carbon::now()->subDays(2);

        // Obtener fecha_inicio y fecha_fin del request
        $fecha_inicio = $request->fecha_inicio;
        $fecha_fin = $request->fecha_fin;

        try {
            if($nombre_estacion == "") {
                $resultados = TelemetriaEstacion::select('nombre_estacion')->distinct()->get();
            } else {
                $query = DB::table('telemetria_resultados as tr')
                    ->select('tm.fecha_muestreo', 'tm.estacion_id', 'te.nombre_estacion', 'tr.parametro_id', 'tp.nombre_parametro', 'tr.resultado', 'tr.unidad_id')
                    ->leftJoin('telemetria_muestras as tm', 'tm.id', '=', 'tr.muestra_id')
                    ->leftJoin('telemetria_estacions as te', 'te.id', '=', 'tm.estacion_id')
                    ->leftJoin('telemetria_parametros as tp', 'tp.id', '=', 'tr.parametro_id')
                    ->where('te.nombre_estacion', $nombre_estacion)
                    ->where(function ($query) {
                        $query->where('estado_id', '<>', '3')
                            ->orWhereNull('estado_id');
                    })
                    ->whereIn('tm.nombre_archivo', ['Percentiles', 'Ruido_10min']);

                // Aplicar filtros de fecha
                if ($fecha_inicio && $fecha_fin) {
                    $query->whereBetween('tm.fecha_muestreo', [$fecha_inicio." 00:10:00", $fecha_fin]);
                } else {
                    $query->where('tm.fecha_muestreo', '>', $fecha_limite);
                }

                if($tipo == "1") {
                    $query->whereIn('parametro_id', [7,8,9,10,11,12,13,14,15,16,17]);
                } else {
                    $query->whereIn('parametro_id', [1,2,3,4,5,6]);
                }

                $resultados = $query->get();
            }
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
            ->select('aplicacion','tipo_estado', 'tipo_criterio', 'variables')->orderBy('tipo_estado', 'desc')->get();
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($criterios_validacion);
    }

    public function getDataLastDay(Request $request) 
    {
        $fecha = $request->fecha_muestreo;
        $nombre_estacion = $request->nombre_estacion;

        $fechaCarbon = Carbon::createFromFormat('Y-m-d', $fecha);        
        $fechaInicio = $fechaCarbon->startOfDay()->addMinutes(10); // 2023-10-11 00:10:00
        $fechaFin = $fechaCarbon->copy()->addDay()->startOfDay(); // 2023-10-12 00:00:00

        $fechaMuestreo = $fechaInicio->format('Y-m-d H:i:s');

        $resultados = DB::table('telemetria_resultados as tr')
            ->select('tm.fecha_muestreo', 'tp.nombre_parametro', 'tr.resultado', DB::raw('1 as tipo'))
            ->leftJoin('telemetria_muestras as tm', 'tm.id', '=', 'tr.muestra_id')
            ->leftJoin('telemetria_estacions as te', 'te.id', '=', 'tm.estacion_id')
            ->leftJoin('telemetria_parametros as tp', 'tp.id', '=', 'tr.parametro_id')
            ->where('te.nombre_estacion', $nombre_estacion)
            ->where('tm.nombre_archivo', 'Tabla_Horaria_ECA')
            ->whereBetween('tm.fecha_muestreo', [$fechaInicio, $fechaFin])
            ->orderBy('tm.fecha_muestreo')
            ->get();

        // Convertir cada objeto en $resultados a un array
        $resultadosArray = $resultados->map(function ($item) {
            return (array) $item;
        });

        // Obtener una fila de la estación para sacar todas las columnas dinámicamente
        $estacion = DB::table('telemetria_estacions')
            ->where('nombre_estacion', $nombre_estacion)
            ->first();

        $parametrosEstacion = collect();

        if ($estacion) {
            // Convertir el objeto $estacion a un array
            $estacionArray = (array) $estacion;

            // Excluir columnas no deseadas (opcional)
            $columnasExcluir = ['id', 'nombre_estacion', 'created_at', 'updated_at'];

            // Recorrer todas las columnas de la estación y formatearlas
            foreach ($estacionArray as $columna => $valor) {
                if (!in_array($columna, $columnasExcluir)) {
                    $parametrosEstacion->push([
                        'fecha_muestreo' => $fechaMuestreo,  // Usar la fecha de búsqueda
                        'nombre_parametro' => $columna,      // El nombre de la columna como nombre del parámetro
                        'resultado' => $valor,                // El valor de la columna como resultado
                        'tipo' => 2
                    ]);
                }
            }
        }

        // Unir las colecciones
        $resultadosCombinados = $resultadosArray->concat($parametrosEstacion)->values();

        // Devolver la combinación de ambos conjuntos de datos
        return response()->json($resultadosCombinados);
    }



    public function getDataLastDayProcesada(Request $request) 
    {
        $fecha = $request->fecha_muestreo;
        $nombre_estacion = $request->nombre_estacion;

        $resultados = DB::table('telemetria_data_procesadas as tdp')
        ->select('tdp.fecha_muestreo', 'tp.nombre_parametro', 'tdp.resultado')
        ->leftJoin('telemetria_estacions as te', 'te.id', '=', 'tdp.estacion_id')
        ->leftJoin('telemetria_parametros as tp', 'tp.id', '=', 'tdp.parametro_id')
        ->where('te.nombre_estacion', $nombre_estacion)
        ->where('tdp.fecha_muestreo', $fecha)
        ->orderBy('tdp.fecha_muestreo')
        ->get();

        // Devolver los resultados (puedes ajustar esto según tus necesidades)
        return response()->json($resultados);
    }

    public function getDataResult(Request $request)
    {
        $nombre_estacion = $request->nombre_estacion;
        $nombre_parametro = $request->nombre_parametro;
        // Obtener el tamaño de la página y la página actual desde la solicitud
        $pageSize = $request->input('page_size', 1000); // Valor por defecto: 1000
        $page = $request->input('page', 1); // Valor por defecto: 1

        // Obtener los datos paginados desde la base de datos
        $resultados = DB::table('telemetria_resultados as tr')
        ->select('tr.muestra_id', 'tr.parametro_id')
        ->leftJoin('telemetria_muestras as tm', 'tm.id', '=', 'tr.muestra_id')
        ->leftJoin('telemetria_estacions as te', 'te.id', '=', 'tm.estacion_id')
        ->leftJoin('telemetria_parametros as tp', 'tp.id', '=', 'tr.parametro_id')
        ->where('te.nombre_estacion', $nombre_estacion)
        ->where('tp.nombre_parametro', $nombre_parametro)
        ->paginate($pageSize, ['*'], 'page', $page);

        // Devolver los datos en formato JSON
        return response()->json($resultados);
    }

    public function getAllCriterioValidacion(Request $request)
    {
        try {
            $criterios_validacion = DB::table('telemetria_criterios_validacions as tcv')->get();
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($criterios_validacion);
    }

    public function getAllData(Request $request)
    {
        ini_set('max_execution_time', 300);
        $parametros = $request->parametros;
        $fecha_inicio = $request->fecha_inicio;
        $fecha_fin = $request->fecha_fin;
        $estaciones = $request->estaciones;
        $tipo_data = $request->tipo_data;
        $resultados = DB::table('telemetria_resultados as tr')
            ->leftJoin('telemetria_muestras as tm', 'tm.id', '=', 'tr.muestra_id')
            ->leftJoin('telemetria_estacions as te', 'te.id', '=', 'tm.estacion_id')
            ->leftJoin('telemetria_parametros as tp', 'tp.id', '=', 'tr.parametro_id')
            ->leftJoin('telemetria_unidads as tu', 'tu.id', '=', 'tr.unidad_id')
            ->leftJoin('telemetria_estado_resultados as ter', 'ter.id', '=', 'tr.estado_id')
            ->select(
                'tm.fecha_muestreo',
                'tm.nombre_archivo',
                'te.nombre_estacion',
                'tp.nombre_parametro',
                'tr.resultado',
                'tu.nombre_unidad',
                'ter.nombre_estado'
            )
            ->when($parametros, function ($query) use ($parametros) {
                $query->whereIn('tr.parametro_id', $parametros);
            })
            ->when($estaciones, function ($query) use ($estaciones) {
                $query->whereIn('te.nombre_estacion', $estaciones);
            })
            ->when($fecha_inicio && $fecha_fin, function ($query) use ($fecha_inicio, $fecha_fin) {
                $query->whereBetween('tm.fecha_muestreo', [$fecha_inicio, $fecha_fin]);
            })
            ->when($tipo_data, function ($query) use ($tipo_data) {
                $query->where('tr.estado_id', $tipo_data);
            })
            ->orderBy('tm.fecha_muestreo', 'desc')
            ->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="resultados_telemetria.csv"',
        ];

        $columns = [
            'Fecha Muestreo',
            'Archivo',
            'Estacion',
            'Parametro',
            'Resultado',
            'Unidad',
            'Estado'
        ];

        $callback = function () use ($resultados, $columns) {
            $file = fopen('php://output', 'w');
            fwrite($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $columns);

            foreach ($resultados as $row) {
                fputcsv($file, [
                    $row->fecha_muestreo,
                    $row->nombre_archivo,
                    $row->nombre_estacion,
                    $row->nombre_parametro,
                    $row->resultado,
                    $row->nombre_unidad,
                    $row->nombre_estado
                ]);
            }

            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function getAllMuestras(Request $request)
    {
        $estacion_id = $request->estacion_id;
        $nombre_archivo = $request->nombre_archivo;

        // Obtener datos
        $resultados = DB::table('telemetria_muestras as tm')
            ->select('tm.id')
            ->where('tm.estacion_id', $estacion_id)
            ->where('tm.nombre_archivo', $nombre_archivo)
            ->get();

        $resultArray = json_decode(json_encode($resultados), true);
        \Log::info(count($resultArray));
        // Limpiar el nombre del archivo (sin espacios, tildes, etc.)
        $archivoSeguro = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nombre_archivo);
        $csvFilename = "estacion{$estacion_id}_{$archivoSeguro}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$csvFilename\"",
        ];

        $callback = function () use ($resultArray) {
            $handle = fopen('php://output', 'w');

            if (count($resultArray) > 0) {
                fputcsv($handle, array_keys($resultArray[0]));
            }

            foreach ($resultArray as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
