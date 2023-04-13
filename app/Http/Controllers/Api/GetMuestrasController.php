<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ClickBotones;

class GetMuestrasController extends Controller
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
        $usuario = $request->user();

        $filtros = $request->filtros;
        $numero_fila = $request->rowPage;
        
        $analytic_click = new ClickBotones;
        $analytic_click->id_user = $usuario->id;
        $analytic_click->id_boton = 1;
        $analytic_click->save();
        #Obtengo el orden
        $orden = DB::table('columnas_usuarios')->where('id_user', $usuario->id)->first();
        if ($orden != null) {
            $orden = $orden->orden;
            $orden = json_decode($orden);
            foreach ($orden as $columna) {
                $cabecera[] = trans($columna->texto);
            }
        } else {
            $cabecera = [
                trans('texto.codigo_muestra'),
                trans('texto.numero_muestra'),
                trans('texto.numero_grupo'),
                trans('texto.Estado'),
                trans('texto.Proyecto'),
                trans('texto.Estacion'),
                trans('texto.Tipo_Muestra'),
                trans('texto.Solicitante'),
                trans('texto.Contratante'),
                trans('texto.Fecha_Muestreo')
            ];
        }
        
        $sql_id_empresas = DB::table('usuario_empresas as ue')
                        ->where('id_usuario', $usuario->id)->where('ue.activo','S')->get();
        
        $muestras = DB::table('muestras AS m')
                ->select(DB::raw(
                    "m.id_parecer AS parecer,
                    m.con_data AS con_data,
                    m.id AS codigo_muestra,
                    m.numero_muestra AS numero_muestra,
                    CONCAT(gm.numero_grupo,'/',gm.anho_grupo) AS numero_grupo,
                    p.nombre_proyecto AS proyecto,
                    e.nombre_estacion AS estacion,
                    m.id_estado AS estado,
                    ta.nombre_tipo_muestra as tipo_muestra,
                    econ.nombre_empresa AS contratante,  
                    esol.nombre_empresa AS solicitante,
                    m.con_documentos AS con_documentos,
                    m.id_certificado AS id_certificado,
                    DATE_FORMAT(m.fecha_muestreo,'%d/%m/%Y %H:%i') AS fecha_muestreo"
                ))
                /*->leftjoin(
                    DB::raw(
                        '(SELECT id_muestra, MIN(id_grupo_muestra) id_grupo_muestra FROM muestra_grupo_muestras GROUP BY id_muestra) AS `mgm`'
                    ),
                    function ($join) {
                        $join->on('mgm.id_muestra', '=', 'm.id');
                    }
                )*/
                ->leftjoin('muestra_grupo_muestras AS mgm', 'mgm.id_muestra', '=', 'm.id')
                ->leftjoin('grupo_muestras AS gm', 'gm.id', '=', 'mgm.id_grupo_muestra')
                ->leftjoin('proyectos AS p', 'p.id', '=', 'm.id_proyecto')
                ->leftjoin('estaciones AS e', 'e.id', '=', 'm.id_estacion')
                ->leftjoin('tipo_muestras AS ta', 'ta.id', '=', 'm.id_tipo_muestra')
                ->leftjoin('empresas AS econ', 'econ.id', '=', 'm.id_empresa_con')
                ->leftjoin('empresas AS esol', 'esol.id', '=', 'm.id_empresa_sol')
                ->where('m.activo', '=', 'S')
                ->where('ta.activo', '=', 'S')
                ->orderBy('m.fecha_muestreo', 'DESC');
        
        $muestra = filtroMuestrasQuery($muestras,$usuario);
        
        if ($filtros != null) {
            foreach ($filtros as $filtro) {                
                $pre_cabecera = $filtro['cabecera'];
                $condicion = $filtro['condicion'];
                $valor = $filtro['valor'];                
                switch ($pre_cabecera) {
                    case strtolower(trans('texto.codigo_muestra')):
                        $analytic_click = new ClickBotones;
                        $analytic_click->id_user = $usuario->id;
                        $analytic_click->id_boton = 11;
                        $analytic_click->save();
                        $muestras = $this->filtros($muestras, 'm.id', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.numero_grupo')):
                        $analytic_click = new ClickBotones;
                        $analytic_click->id_user = $usuario->id;
                        $analytic_click->id_boton = 12;
                        $analytic_click->save();
                        $muestras = $this->filtros($muestras, 'numero_grupo', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.numero_muestra')):
                        $analytic_click = new ClickBotones;
                        $analytic_click->id_user = $usuario->id;
                        $analytic_click->id_boton = 13;
                        $analytic_click->save();
                        $muestras = $this->filtros($muestras, 'm.numero_muestra', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.Estado')):
                        $analytic_click = new ClickBotones;
                        $analytic_click->id_user = $usuario->id;
                        $analytic_click->id_boton = 6;
                        $analytic_click->save();
                        if ($valor == "recibida") {
                            $valor = 1;
                        } elseif ($valor == "en proceso") {
                            $valor = 2;
                        } elseif ($valor == "finalizada") {
                            $valor = 3;
                        } elseif ($valor == "con informe") {
                            $valor = 4;
                        }
                        $muestras = $this->filtros($muestras, 'm.id_estado', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.Proyecto')):
                        $analytic_click = new ClickBotones;
                        $analytic_click->id_user = $usuario->id;
                        $analytic_click->id_boton = 8;
                        $analytic_click->save();
                        $muestras = $this->filtros($muestras, 'p.nombre_proyecto', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.Estacion')):
                        $analytic_click = new ClickBotones;
                        $analytic_click->id_user = $usuario->id;
                        $analytic_click->id_boton = 5;
                        $analytic_click->save();
                        $muestras = $this->filtros($muestras, 'e.nombre_estacion', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.Tipo_Muestra')):
                        $analytic_click = new ClickBotones;
                        $analytic_click->id_user = $usuario->id;
                        $analytic_click->id_boton = 10;
                        $analytic_click->save();
                        $muestras = $this->filtros($muestras, 'ta.nombre_tipo_muestra', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.Solicitante')):
                        $analytic_click = new ClickBotones;
                        $analytic_click->id_user = $usuario->id;
                        $analytic_click->id_boton = 9;
                        $analytic_click->save();
                        $muestras = $this->filtros($muestras, 'esol.nombre_empresa', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.Contratante')):
                        $analytic_click = new ClickBotones;
                        $analytic_click->id_user = $usuario->id;
                        $analytic_click->id_boton = 4;
                        $analytic_click->save();
                        $muestras = $this->filtros($muestras, 'econ.nombre_empresa', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.Fecha_Muestreo')):
                        $analytic_click = new ClickBotones;
                        $analytic_click->id_user = $usuario->id;
                        $analytic_click->id_boton = 7;
                        $analytic_click->save();
                        $muestras = $this->filtros($muestras, 'm.fecha_muestreo', $condicion, $valor);
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        
        if ($numero_fila == null) {
            $numero_fila = 20;
        }
        $muestras = $muestras->paginate($numero_fila);
        
        $resultado = [];
        foreach ($muestras as $muestra) {
            $pre_resultado = [];
            $render = [];
            if ($orden != null) {
                foreach ($orden as $columna) {
                    if ($columna->texto == 'texto.Estado') {
                        if ($muestra->estado == "1") {
                            $pre_resultado['data'][] = "Recibida";
                        } elseif ($muestra->estado == "2") {
                            $pre_resultado['data'][] = "En Proceso";
                        } elseif ($muestra->estado == "3") {
                            $pre_resultado['data'][] = "Finalizada";
                        } else {
                            $pre_resultado['data'][] = "Con Informe";
                        }
                    } else {
                        $pre_resultado['data'][] = "".$muestra->{$columna->tabla};
                    }
                    $pre_resultado['id'] = $muestra->codigo_muestra;
                }
                switch ("".$muestra->parecer."") {
                    case "1":
                        $pre_resultado['render']['color'] = null;
                        break;
                    case "2":
                        $pre_resultado['render']['color'] = "bg-success";
                        break;
                    case "3":
                        $pre_resultado['render']['color'] = "bg-danger";
                        break;
                    default:
                        $pre_resultado['render']['color'] = null;
                        break;
                }
                if ($muestra->con_data == 'S') {
                    if ($muestra->estado == 2) {
                        $pre_resultado['render']['flag'] = true;
                    } else {
                        $pre_resultado['render']['flag'] = false;
                    }
                } else {
                    $pre_resultado['render']['flag'] = false;
                }
            
                if ($muestra->con_documentos == "S") {
                    $pre_resultado['render']['con_documentos'] = true;
                } else {
                    $pre_resultado['render']['con_documentos'] = false;
                }
            } else {
                $pre_resultado['data'][] = "".$muestra->codigo_muestra;
                $pre_resultado['data'][] = $muestra->numero_muestra;
                $pre_resultado['data'][] = $muestra->numero_grupo;
                if ($muestra->estado == "1") {
                    $pre_resultado['data'][] = "Recibida";
                } elseif ($muestra->estado == "2") {
                    $pre_resultado['data'][] = "En Proceso";
                } elseif ($muestra->estado == "3") {
                    $pre_resultado['data'][] = "Finalizada";
                } else {
                    $pre_resultado['data'][] = "Con Informe";
                }
                $pre_resultado['data'][] = $muestra->proyecto;
                $pre_resultado['data'][] = $muestra->estacion;
                switch ("".$muestra->parecer."") {
                    case "1":
                        $pre_resultado['render']['color'] = null;
                        break;
                    case "2":
                        $pre_resultado['render']['color'] = "bg-success";
                        break;
                    case "3":
                        $pre_resultado['render']['color'] = "bg-danger";
                        break;
                    default:
                        $pre_resultado['render']['color'] = null;
                        break;
                }
                if ($muestra->con_data == 'S') {
                    if ($muestra->estado == 2) {
                        $pre_resultado['render']['flag'] = true;
                    } else {
                        $pre_resultado['render']['flag'] = false;
                    }
                } else {
                    $pre_resultado['render']['flag'] = false;
                }
            
                if ($muestra->con_documentos == "S") {
                    $pre_resultado['render']['con_documentos'] = true;
                } else {
                    $pre_resultado['render']['con_documentos'] = false;
                }
            
                $pre_resultado['data'][] = $muestra->tipo_muestra;
                $pre_resultado['data'][] = $muestra->solicitante;
                $pre_resultado['data'][] = $muestra->contratante;
                $pre_resultado['data'][] = $muestra->fecha_muestreo;
                $pre_resultado['id'] = $muestra->codigo_muestra;
            }
            array_push($resultado, $pre_resultado);
        }
        //$resultado = $this->paginate($resultado);
        $rpta['cabecera'] = $cabecera;
        if ($orden != null) {
            foreach ($orden as $columna) {
                if ($columna->texto == 'texto.Estado') {
                    $rpta['format'][] = 'flag';
                } else {
                    $rpta['format'][] = '';
                }
            }
        } else {
            $rpta['format'] = ['','','','flag','','','','','',''];
        }
        $rpta['pagina']['current_page'] = $muestras->currentPage();
        $rpta['pagina']['data'] = $resultado;
        $rpta['pagina']['first_page_url'] = $muestras->url(1);
        $rpta['pagina']['from'] = $muestras->firstItem();
        $rpta['pagina']['last_page'] = $muestras->lastPage();
        $rpta['pagina']['last_page_url'] = $muestras->url($muestras->lastPage());
        $rpta['pagina']['links'] = $muestras->toArray()['links'];
        $rpta['pagina']['next_page_url'] = $muestras->nextPageUrl();
        $rpta['pagina']['path'] = $muestras->path();
        $rpta['pagina']['per_page'] = intval($muestras->perPage());
        $rpta['pagina']['prev_page_url'] = $muestras->previousPageUrl();
        $rpta['pagina']['to'] = $muestras->lastItem();
        $rpta['pagina']['total'] = $muestras->total();

        return $rpta;
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

    public function paginate($items, $perPage = 20, $page = null, $options = [])
    {
        $collection = new Collection($items);

        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        $currentPageResults = $collection->slice(($page - 1) * $perPage, $perPage)->values();

        //return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
        return new LengthAwarePaginator($currentPageResults, $items->count(), $perPage, $page, $options);
    }

    public function filtros($datos, $nombre_columna, $condicion, $valor)
    {
        switch ($condicion) {
            case 'contiene':
                $datos = $datos->where($nombre_columna, 'like', '%'.$valor.'%');
                break;
            
            case 'no contiene':
                $datos = $datos->where($nombre_columna, 'not like', '%'.$valor.'%');
                break;

            case 'igual a':
                if ($nombre_columna == "m.fecha_muestreo") {
                    $datos = $datos->where($nombre_columna, '>', $valor." 00:00:00")
                                ->where($nombre_columna, '<', $valor." 23:59:59");
                } else {
                    $datos = $datos->where($nombre_columna, '=', $valor);
                }
                break;

            case 'no igual a':
                if ($nombre_columna == "m.fecha_muestreo") {
                    $datos = $datos->where($nombre_columna, '>', $valor." 23:59:59")
                                ->where($nombre_columna, '<', $valor." 00:00:00");
                } else {
                    $datos = $datos->where($nombre_columna, '<>', $valor);
                }
                break;
                
            case 'vacio':
                $datos = $datos->whereNull($nombre_columna);
                break;

            case 'no vacio':
                $datos = $datos->whereNotNull($nombre_columna);
                break;

            case strtolower(trans('texto.mayor_que')):
                $datos = $datos->where($nombre_columna, '>', $valor." 00:00:00");
                break;
            
            case strtolower(trans('texto.menor_que')):
                $datos = $datos->where($nombre_columna, '<', $valor." 23:59:59");
                break;

            default:
                # code...
                break;
        }
        
        return $datos;
    }
}
