<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetInformesController extends Controller
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
        $filtros = $request->filtros;

        $cabecera = [
            trans('texto.id'),
            trans('texto.numero'),
            trans('texto.titulo_informe'),
        ];

        $usuario = $request->user();

        $sql_informes = DB::table('certificados AS c')
                ->select(DB::raw(
                    "c.id,
                    c.id_certificado,
                    c.identificacion_certificado,
                    c.titulo_certificado"
                ))
                ->leftjoin('muestras AS m', 'm.id_certificado', '=', 'c.id_certificado')
                ->where('m.activo', '=', 'S')
                ->orderBy('m.fecha_muestreo', 'DESC')->distinct();
        
        $sql_informes = filtroMuestrasQuery($sql_informes,$usuario);

        if ($filtros != null) {
            foreach ($filtros as $filtro) {
                //var_dump($filtro);
                $pre_cabecera = $filtro['cabecera'];
                $condicion = $filtro['condicion'];
                $valor = $filtro['valor'];
                switch ($pre_cabecera) {
                    case strtolower(trans('texto.codigo_muestra')):
                        $sql_informes = $this->filtros($sql_informes, 'm.id', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.numero_grupo')):
                        $sql_informes = $this->filtros($sql_informes, 'm.numero_grupo', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.numero_muestra')):
                        $sql_informes = $this->filtros($sql_informes, 'm.numero_muestra', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.Estado')):
                        if ($valor == "recibida") {
                            $valor = 1;
                        } elseif ($valor == "en proceso") {
                            $valor = 2;
                        } elseif ($valor == "finalizada") {
                            $valor = 3;
                        } elseif ($valor == "Con Informe") {
                            $valor = 4;
                        }
                        $sql_informes = $this->filtros($sql_informes, 'm.id_estado', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.Proyecto')):
                        $sql_informes = $this->filtros($sql_informes, 'p.nombre_proyecto', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.Estacion')):
                        $sql_informes = $this->filtros($sql_informes, 'e.nombre_estacion', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.Tipo_Muestra')):
                        $sql_informes = $this->filtros($sql_informes, 'ta.nombre_tipo_muestra', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.Solicitante')):
                        $sql_informes = $this->filtros($sql_informes, 'esol.nombre_empresa', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.Contratante')):
                        $sql_informes = $this->filtros($sql_informes, 'econ.nombre_empresa', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.Fecha_Muestreo')):
                        $sql_informes = $this->filtros($sql_informes, 'm.fecha_muestreo', $condicion, $valor);
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        
        $sql_informes = $sql_informes->paginate(20);
        
        $resultado = [];
        foreach ($sql_informes as $muestra) {
            $pre_resultado = [];
            $render = [];
            $pre_resultado['data'][] = "".$muestra->id;
            $pre_resultado['data'][] = $muestra->identificacion_certificado;
            $pre_resultado['data'][] = $muestra->titulo_certificado;
            $pre_resultado['render']['color'] = null;
            $pre_resultado['render']['flag'] = false;
            $pre_resultado['render']['con_documentos'] = true;
            
            array_push($resultado, $pre_resultado);
        }
        //$resultado = $this->paginate($resultado);
        $rpta['cabecera'] = $cabecera;
        $rpta['format'] = ['','',''];
        $rpta['pagina']['current_page'] = $sql_informes->currentPage();
        $rpta['pagina']['data'] = $resultado;
        $rpta['pagina']['first_page_url'] = $sql_informes->url(1);
        $rpta['pagina']['from'] = $sql_informes->firstItem();
        $rpta['pagina']['last_page'] = $sql_informes->lastPage();
        $rpta['pagina']['last_page_url'] = $sql_informes->url($sql_informes->lastPage());
        $rpta['pagina']['links'] = $sql_informes->toArray()['links'];
        $rpta['pagina']['next_page_url'] = $sql_informes->nextPageUrl();
        $rpta['pagina']['path'] = $sql_informes->path();
        $rpta['pagina']['per_page'] = $sql_informes->perPage();
        $rpta['pagina']['prev_page_url'] = $sql_informes->previousPageUrl();
        $rpta['pagina']['to'] = $sql_informes->lastItem();
        $rpta['pagina']['total'] = $sql_informes->total();

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
                if ($nombre_columna == "fecha_muestreo") {
                    $datos = $datos->where($nombre_columna, '>', $valor." 00:00:00")
                                ->where($nombre_columna, '<', $valor." 23:59:59");
                } else {
                    $datos = $datos->where($nombre_columna, '=', $valor);
                }
                break;

            case 'no igual a':
                if ($nombre_columna == "fecha_muestreo") {
                    $datos = $datos->where($nombre_columna, '>', $valor." 23:59:00")
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

            case 'mayor que':
                $datos = $datos->where($nombre_columna, '>', $valor);
                break;
            
            case 'menor que':
                $datos = $datos->where($nombre_columna, '<', $valor);
                break;

            default:
                # code...
                break;
        }
        
        return $datos;
    }
}
