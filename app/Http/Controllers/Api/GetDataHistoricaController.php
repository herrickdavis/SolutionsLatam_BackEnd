<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ClickBotones;
use Throwable;

class GetDataHistoricaController extends Controller
{
    protected $idauxempresas = [];
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

            $analytic_click = new ClickBotones;
            $analytic_click->id_user = $usuario->id;
            $analytic_click->id_boton = 22;
            $analytic_click->save();

            $hoy = new \DateTime();
            $fecha_inicio = "2019-01-01";//$request->fecha_inicio;
            $fecha_fin = $hoy->format('Y-m-d');//$request->fecha_fin;
            $id_tipo_muestra = str_replace("TA", "", $request->id_tipo_muestra);
            $id_proyecto = $request->id_proyecto;
            $estaciones = $request->estaciones;
            $parametros = $request->parametros;
            $id_limite = $request->id_limite;

            $id_estaciones = [];

            $id_parametros = [];
            $id_grupo_parametros = [];

            $colors = [
                "#63b598", "#ce7d78", "#ea9e70", "#a48a9e", "#c6e1e8", "#648177" ,"#0d5ac1" ,
                "#f205e6" ,"#1c0365" ,"#14a9ad" ,"#4ca2f9" ,"#a4e43f" ,"#d298e2" ,"#6119d0",
                "#d2737d" ,"#c0a43c" ,"#f2510e" ,"#651be6" ,"#79806e" ,"#61da5e" ,"#cd2f00" ,
                "#9348af" ,"#01ac53" ,"#996635","#b11573" ,"#4bb473" ,"#75d89e" , "#2f3f94" ,
                "#2f7b99" ,"#da967d" ,"#34891f" ,"#b0d87b" ,"#ca4751" ,"#7e50a8" , "#c4d647",
                "#11dec1" ,"#289812" ,"#566ca0" ,"#2f1179" , "#935b6d" ,"#916988" ,"#513d98",
                "#aead3a", "#9e6d71", "#4b5bdc", "#0cd36d", "#cb5bea", "#228916", "#ac3e1b",
                "#df514a", "#539397", "#880977", "#f697c1", "#679c9d", "#c6c42c", "#5d2c52",
                "#48b41b", "#e1cf3b", "#5be4f0", "#57c4d8", "#a4d17a", "#be608b", "#96b00c",
                "#088baf", "#e145ba", "#05d371", "#5426e0", "#802234", "#6749e8", "#0971f0",
                "#8fb413", "#c9a941", "#41d158", "#fb21a3", "#51aed9", "#5bb32d", "#21538e",
                "#89d534", "#d36647", "#0023b8", "#3b8c2a", "#986b53", "#f50422", "#983f7a",
                "#ea24a3", "#79352c", "#521250", "#e33e52", "#b2be57", "#fa06ec", "#1bb699",
                "#6b2e5f", "#64820f", "#21538e", "#89d534", "#d36647", "#0023b8", "#3b8c2a",
                "#986b53", "#f50422", "#983f7a", "#ea24a3", "#79352c", "#521250", "#e33e52",
                "#b2be57", "#fa06ec", "#1bb699", "#6b2e5f", "#64820f", "#9cb64a", "#996c48",
                "#9ab9b7", "#06e052", "#e3a481", "#0eb621", "#fc458e", "#b2db15", "#aa226d",
                "#792ed8", "#73872a", "#520d3a", "#a5b3d9", "#7d1d85", "#c4fd57", "#f1ae16",
                "#8fe22a", "#ef6e3c", "#243eeb", "#dd93fd", "#3f8473",
                "#421f79", "#7a3d93", "#635f6d", "#93f2d7", "#9b5c2a", "#15b9ee", "#0f5997",
                "#409188", "#911e20", "#1350ce", "#10e5b1", "#cb2582", "#ce00be",
                "#32d5d6", "#608572", "#c79bc2", "#77772a", "#6995ba",
                "#fc6b57", "#f07815", "#8fd883", "#060e27", "#96e591", "#21d52e", "#d00043",
                "#b47162", "#4f0f6f", "#1d1d58", "#947002", "#bde052", "#e08c56",
                "#28fcfd", "#36486a", "#d02e29", "#1ae6db", "#3e464c", "#a84a8f",
                "#911e7e", "#3f16d9", "#0f525f", "#ac7c0a", "#b4c086", "#c9d730", "#30cc49",
                "#3d6751", "#fb4c03", "#640fc1", "#62c03e", "#d3493a", "#88aa0b", "#406df9",
                "#615af0", "#2a3434", "#4a543f", "#79bca0", "#00efd4",
                "#7ad236", "#7260d8", "#1deaa7", "#06f43a", "#823c59", "#e3d94c", "#dc1c06",
                "#f53b2a", "#b46238", "#2dfff6", "#a82b89", "#1a8011", "#436a9f", "#1a806a",
                "#4cf09d", "#c188a2", "#67eb4b", "#b308d3", "#fc7e41", "#af3101",
                "#71b1f4", "#a2f8a5", "#e23dd0", "#d3486d", "#00f7f9", "#474893", "#3cec35",
                "#1c65cb", "#5d1d0c", "#2d7d2a", "#ff3420", "#5cdd87", "#a259a4", "#e4ac44",
                "#1bede6", "#8798a4", "#d7790f", "#b2c24f", "#de73c2", "#d70a9c",
                "#88e9b8", "#c2b0e2", "#86e98f", "#ae90e2", "#1a806b", "#436a9e", "#0ec0ff",
                "#f812b3", "#b17fc9", "#8d6c2f", "#d3277a", "#2ca1ae", "#9685eb", "#8a96c6",
                "#dba2e6", "#76fc1b", "#608fa4", "#20f6ba", "#07d7f6", "#dce77a", "#77ecca"
            ];


            foreach ($parametros as $parametro) {
                if (substr($parametro, 0, 1) == "G") {
                    array_push($id_grupo_parametros, substr($parametro, 1));
                } else {
                    array_push($id_parametros, substr($parametro, 1));
                }
            }

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
            $id_param = [];
            foreach ($sql_id_parametros as $valor) {
                $id_parametros[$valor->nombre_parametro]['nombre'] = $valor->nombre_parametro;
                $id_parametros[$valor->nombre_parametro]['id'][] = $valor->id;
                array_push($id_param, $valor->id);
            }

            $resultado = [];

            #Primero obtengo las estaciones
            $id_estaciones = DB::table('estaciones as e')->select(DB::raw("e.id"))->whereIn('e.nombre_estacion', $estaciones)
            ->orWhere(function ($query) use ($estaciones) {
                $query->whereIn('e.alias_estacion', $estaciones);
            })->distinct()->pluck('id')->toArray();

            //OBTENGO LA MINIMA FECHA CADASTRADA
            $sql_fecha_muestreo = DB::table('muestras as m')
                                ->select(DB::raw(
                                    'DATE_FORMAT(m.fecha_muestreo,"%d-%m-%Y") as date
                                    '
                                ))
                                ->leftjoin('muestra_parametros as mp', 'mp.id_muestra', '=', 'm.id')
                                ->leftjoin('estaciones AS e', 'm.id_estacion', '=', 'e.id')
                                ->whereIn('m.id_estado', [3,4])
                                ->where('m.activo', '=', 'S')
                                ->where('m.id_tipo_muestra', '=', $id_tipo_muestra)
                                ->whereIn('e.id',$id_estaciones)
                                ->whereIn('mp.id_parametro', $id_param)
                                ->orderBy('m.fecha_muestreo', 'ASC')
                                ->first();
            $fecha_inicio = $sql_fecha_muestreo->date;

            foreach ($id_parametros as $parametro) {
                $label = [];
                $sql_data_historica = [];

                $sql_data_historica = DB::table('muestras as m')
                                ->select(DB::raw(
                                    'DATE_FORMAT(m.fecha_muestreo,"%d-%m-%Y") as date,
                                    mp.valor as value,
                                    un.unidad as unidad,
                                    case
                                    when gp.grupo_parametros is null then p.nombre_parametro
                                    else gp.grupo_parametros end as nombre_parametro,
                                    case
                                    when e.alias_estacion is null then e.nombre_estacion
                                    else e.alias_estacion end as estacion
                                    '
                                ))
                                ->leftjoin('proceso_muestras AS pm', 'pm.id_muestra','=','m.id')
                                ->leftJoin('proyectos as pr','pr.id','=','m.id_proyecto')
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
                                ->whereIn('e.id',$id_estaciones)
                                ->whereIn('mp.id_parametro', $parametro['id'])
                                ->distinct()
                                ->orderBy('m.fecha_muestreo', 'ASC');
                
                if ($id_proyecto) {
                    $sql_procesos = DB::table('proceso_proyectos as pp')->select(DB::raw("pp.id_proceso"))->whereIn('pp.nombre_proyecto', $id_proyecto)
                    ->orWhere(function ($query) use ($id_proyecto) {
                        $query->whereIn('pp.alias_proyecto', $id_proyecto);
                    })->distinct()->pluck('id_proceso')->toArray();

                    $sql_data_historica = $sql_data_historica->whereIn('pm.id_proceso', $sql_procesos);
                }

                $sql_data_historica = $sql_data_historica->get();
                
                $contador = 1;
                $pre_pre_resultado = [];
                foreach ($sql_data_historica as $data_historica) {
                    $pre_resultado['titulo'] = $data_historica->nombre_parametro;
                    $pre_resultado['ejex'] = 'Fecha Muestreo';
                    $pre_resultado['ejey'] = $data_historica->unidad;
                   
                    $menor = strpos($data_historica->value, "<");
                    if ($menor === false) {
                        $valor = trim(str_replace(",", ".", $data_historica->value));
                        if (is_numeric($valor)) {
                            $pre_pre[$data_historica->date]= floatval($valor);
                            $pre_pre_resultado[$data_historica->estacion][$data_historica->date] = floatval($valor);
                        } else {
                            $pre_pre[$data_historica->date] = 0;
                            $pre_pre_resultado[$data_historica->estacion][$data_historica->date] = 0;
                        }
                    } else {
                        $pre_pre[$data_historica->date] = 0;
                        $pre_pre_resultado[$data_historica->estacion][$data_historica->date] = 0;
                    }
                    
                    $pre_pre_resultado[$data_historica->estacion]['nombre'] = $data_historica->estacion;
                    $fecha_fin = $data_historica->date;
                }

                $start = $month = strtotime($fecha_inicio);
                $end = strtotime($fecha_fin);
                while ($month <= $end) {
                    $label[] = date('d-m-Y', $month);
                    $month = strtotime("+1 day", $month);
                }
                $bresultado = [];
                $datos = [];
                foreach ($pre_pre_resultado as $value) {
                    //separa por estaciones
                    foreach ($label as $fecha) {
                        if (array_key_exists($fecha, $value)) {
                            array_push($datos, $value[$fecha]);
                        } else {
                            array_push($datos, null);
                        }
                    }
                    $value['datos'] = $datos;
                    $value['nombre'] = $value['nombre'];
                    $rpta['nombre'] = $value['nombre'];
                    $rpta['datos'] = $datos;
                    $rpta['color'] = $colors[random_int(0, 247)];
                    $rpta['showSymbol'] = true;
                    array_push($bresultado, $rpta);
                    $datos = [];
                }

                if ($id_limite != null) {
                    $analytic_click = new ClickBotones;
                    $analytic_click->id_user = $usuario->id;
                    $analytic_click->id_boton = 21;
                    $analytic_click->save();

                    $sql_limite = DB::table('limites as l')
                                ->select(DB::raw(
                                    'lp.maximo as maximo,
                                    lp.minimo as minimo
                                    '
                                ))
                                ->leftjoin('limite_parametros as lp', 'lp.id_limite', '=', 'l.id')
                                ->where('l.id', '=', $id_limite)
                                ->whereIn('lp.id_parametro', $parametro['id'])
                                ->first();
                    
                    // Inicializar límites como nulos por defecto
                    $limite_maximo = null;
                    $limite_minimo = null;
                    if ($sql_limite) { // Verificar si la consulta devolvió un resultado
                        $limite_maximo = $sql_limite->maximo;
                        $limite_minimo = $sql_limite->minimo;
                    }

                    $dato_limites_minimo = [];
                    foreach ($label as $fecha) {
                        array_push($dato_limites_minimo, floatval($limite_minimo));
                    }

                    $dato_limites_maximo = [];
                    foreach ($label as $fecha) {
                        array_push($dato_limites_maximo, floatval($limite_maximo));
                    }

                    $rpta_limite_minimo['nombre'] = "LI";
                    $rpta_limite_minimo['datos'] = $dato_limites_minimo;
                    $rpta_limite_minimo['color'] = "#FFC300";
                    $rpta_limite_minimo['showSymbol'] = false;

                    $rpta_limite_maximo['nombre'] = "LS";
                    $rpta_limite_maximo['datos'] = $dato_limites_maximo;
                    $rpta_limite_maximo['color'] = "#FFC300";
                    $rpta_limite_maximo['showSymbol'] = false;
                    
                    if (count($bresultado) > 0) {
                        array_push($bresultado, $rpta_limite_maximo);
                        array_push($bresultado, $rpta_limite_minimo);
                    }
                }
                
                $pre_resultado['series'] = $bresultado;
                if (count($bresultado) > 0) {
                    array_push($resultado, $pre_resultado);
                }

                $pre_pre_resultado = [];
                $pre_resultado = [];
            }

            $respuesta['dates'] = $label;
            $respuesta['graficas'] = $resultado;
            
            
            return $respuesta;
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
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
}
