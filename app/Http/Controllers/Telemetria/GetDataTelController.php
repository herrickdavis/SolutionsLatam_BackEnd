<?php

namespace App\Http\Controllers\Telemetria;

use App\Http\Controllers\Controller;
use App\Models\TelemetriaAbreviaturaProcesamiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TelemetriaEstacion;
use App\Models\TelemetriaMuestra;
use App\Models\TelemetriaParametro;
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
        $id_estacion = $request->id_estacion;
        $id_parametro = $request->id_parametro;

        try {
            $sql_data = DB::table('telemetria_resultados as tr')
                                ->select(DB::raw(
                                    "
                                    tm.fecha_muestreo as fecha_muestreo,
                                    tr.resultado as resultado
                                    "
                                ))
                                ->join('telemetria_muestras as tm', 'tm.id', '=', 'tr.muestra_id')
                                ->join('telemetria_estacions as te', 'te.id', '=', 'tm.estacion_id')
                                ->where('te.id', $id_estacion)
                                ->where('tr.parametro_id', $id_parametro)
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
                                ->where('tm.nombre_archivo',$nombre_archivo)
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

        try {
            $t_estacion = TelemetriaEstacion::firstOrCreate(
                ['nombre_estacion' => $nombre_estacion]
            );

        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($t_estacion);
    }

    public function getAllSample(Request $request)
    {
        set_time_limit(4800);
        if(count($request->all()) > 0) {
            $id_estacion = $request->all()[0]['estacion_id'];
            $nombre_archivo = $request->all()[0]['nombre_archivo'];
        }
        
        $ids_estacion = [];
        try {
            foreach ($request->all() as $value) {
                $fecha_muestreo = $value['fecha_muestreo'];
                $fecha_muestreo = Carbon::createFromTimestamp($fecha_muestreo / 1000)->format('Y-m-d H:i:s');
                $t_muestra = TelemetriaMuestra::firstOrCreate(
                    [
                        'estacion_id' => $id_estacion,
                        'nombre_archivo' => $nombre_archivo,
                        'fecha_muestreo' => $fecha_muestreo
                    ]
                );
                $data = [
                    'estacion_id' => $id_estacion,
                    'nombre_archivo' => $nombre_archivo,
                    'fecha_muestreo' => $fecha_muestreo,
                    'muestra_id' => $t_muestra->id
                ];

                array_push($ids_estacion,$data);
            }

        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($ids_estacion);
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
}
