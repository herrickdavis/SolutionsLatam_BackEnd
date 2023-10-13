<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\DataHistoricaExport;
use App\Models\ClickBotones;
use Throwable;

class GetDataHistoricaExcelController extends Controller
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
            $hoy = new \DateTime();
            $usuario = $request->user();

            $analytic_click = new ClickBotones;
            $analytic_click->id_user = $usuario->id;
            $analytic_click->id_boton = 23;
            $analytic_click->save();

            $fecha_inicio = "2019-01-01";//$request->fecha_inicio;
            $fecha_fin = $hoy->format('Y-m-d');//$request->fecha_fin;
            $id_tipo_muestra = str_replace("TA", "", $request->id_tipo_muestra);
            $estaciones = $request->estaciones;
            $parametros = $request->parametros;
            $id_proyecto = $request->id_proyecto;

            $id_grupo_estaciones = [];

            $id_parametros = [];
            $id_grupo_parametros = [];

            foreach ($parametros as $parametro) {
                if (substr($parametro, 0, 1) == "G") {
                    array_push($id_grupo_parametros, substr($parametro, 1));
                } else {
                    array_push($id_parametros, substr($parametro, 1));
                }
            }

            $sql_id_estaciones_grupo = DB::table('grupo_estaciones as ge')
                                ->select(DB::raw(
                                    "ege.id_estacion as id
                                    "
                                ))
                                ->join('estacion_grupo_estaciones AS ege', 'ege.id_grupo_estacion', '=', 'ge.id')
                                ->whereIn('ege.id_grupo_estacion', $id_grupo_estaciones);
                
            $sql_id_parametros_grupo = DB::table('grupo_parametros as gp')
                                ->select(DB::raw(
                                    "
                                    'G' as tipo,
                                    pgp.id_parametro as id,
                                    gp.grupo_parametros as nombre_parametro
                                    "
                                ))
                                ->join('parametro_grupo_parametros AS pgp', 'pgp.id_grupo_parametro', '=', 'gp.id')
                                ->whereIn('pgp.id_grupo_parametro', $id_grupo_parametros);
                
            $sql_id_parametros = DB::table('parametros as p')
                                ->select(DB::raw(
                                    "
                                    'P' as tipo,
                                    p.id as id,
                                    p.nombre_parametro as nombre_parametro
                                    "
                                ))
                                ->whereIn('id', $id_parametros)
                                ->union($sql_id_parametros_grupo)
                                ->orderBy('nombre_parametro')
                                ->distinct()
                                ->get();

            $id_parametros = [];
            foreach ($sql_id_parametros as $valor) {
                $id_parametros[$valor->nombre_parametro]['nombre'] = $valor->nombre_parametro;
                $id_parametros[$valor->nombre_parametro]['id'][] = $valor->id;
            }

            $resultado = [];
            
            foreach ($id_parametros as $parametro) {
                $label = [];
                $sql_data_historica = [];

                $sql_data_historica = DB::table('muestras as m')
                                ->select(DB::raw(
                                    "DATE_FORMAT(m.fecha_muestreo,'%d/%m/%Y') as date,
                                    case
                                    when ge.grupo_estacion is null then e.nombre_estacion
                                    else ge.grupo_estacion end as estacion,
                                    case
                                    when gp.grupo_parametros is null then p.nombre_parametro
                                    else gp.grupo_parametros end as nombre_parametro,
                                    REPLACE(mp.valor,',','.') as value,
                                    un.unidad as unidad
                                    "
                                ))
                                ->leftjoin('muestra_parametros as mp', 'mp.id_muestra', '=', 'm.id')
                                ->leftjoin('metodos as me', 'me.id', '=', 'mp.id_metodo')
                                ->leftjoin('parametros as p', 'p.id', '=', 'mp.id_parametro')
                                ->leftjoin('parametro_grupo_parametros AS pgp', function ($join) {
                                    $join->on('pgp.id_parametro', '=', 'p.id')
                                     ->on('pgp.idaux_metodo', '=', 'me.idaux_metodo');
                                })
                                ->leftjoin('grupo_parametros AS gp', 'gp.id', '=', 'pgp.id_grupo_parametro')
                                ->leftjoin('unidades as un', 'un.id', '=', 'mp.id_unidad')
                                ->leftjoin('estaciones AS e', 'm.id_estacion', '=', 'e.id')
                                ->leftjoin('estacion_grupo_estaciones AS ege', 'ege.id_estacion', '=', 'e.id')
                                ->leftjoin('grupo_estaciones AS ge', 'ge.id', '=', 'ege.id_grupo_estacion')
                                ->whereIn('m.id_estado', [3,4])
                                ->where('m.activo', '=', 'S')
                                ->where('m.id_tipo_muestra', '=', $id_tipo_muestra)
                                ->whereIn('e.nombre_estacion', $estaciones)
                                ->whereIn('mp.id_parametro', $parametro['id'])
                                ->distinct()
                                ->orderBy('m.fecha_muestreo', 'ASC');
                if ($id_proyecto) {
                    foreach ($id_proyecto as $key => $value) {
                        $id_proyecto[$key] = str_replace("P", "", $value);
                    }

                    $sql_data_historica = $sql_data_historica->whereIn('m.id_proyecto', $id_proyecto);
                }

                $sql_data_historica = $sql_data_historica->get();

                $contador = 1;
                foreach ($sql_data_historica as $data_historica) {
                    array_push($resultado, $data_historica) ;
                }
            }

            $cabecera = ['Fecha', 'Estacion', ' Parametro', 'Resultado', 'Unidad'];
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        
        return \Excel::download(new DataHistoricaExport($resultado, $cabecera), 'Data Historica.xlsx');
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
