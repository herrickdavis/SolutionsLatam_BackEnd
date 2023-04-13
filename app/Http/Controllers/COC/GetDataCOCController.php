<?php

namespace App\Http\Controllers\COC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
<<<<<<< HEAD
use Illuminate\Support\Facades\DB;
=======
>>>>>>> 3cae835 (Archivos Subidos)
use GuzzleHttp\Client;

class GetDataCOCController extends Controller
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
<<<<<<< HEAD
        $filtros = $request->filtros;

        $id_cadena = 1;

        //Terminamos de leer datos
        $cadenas = DB::table("cadenas as c");

        if ($filtros != null) {
            foreach ($filtros as $filtro) {
                $pre_cabecera = $filtro['cabecera'];
                $condicion = $filtro['condicion'];
                $valor = $filtro['valor'];
                
                switch ($pre_cabecera) {
                    case mb_strtolower(trans('texto.Estacion'), 'UTF-8'):
                        $estaciones = $this->filtros($cadenas, 'e.nombre_estacion', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.Fecha_Muestreo')):
                        $estaciones = $this->filtros($cadenas, 'ge.grupo_estacion', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.Matriz')):
                        $estaciones = $this->filtros($cadenas, 'p.nombre_proyecto', $condicion, $valor);
                        break;
                }
            }
        }

        $cadenas = $cadenas->paginate(20);

        $resultado = [];
        foreach ($cadenas as $cadena) {
            $pre_resultado = [];
            $render = [];
            $pre_resultado['data'][] = "" . $cadena->codigo_laboratorio;
            $pre_resultado['data'][] = $cadena->numero_grupo;
            $pre_resultado['data'][] = $cadena->numero_proceso;
            $pre_resultado['data'][] = $cadena->estacion;
            $pre_resultado['data'][] = $cadena->fecha_inicio;
            $pre_resultado['data'][] = $cadena->tipo_muestra;
            $pre_resultado['render']['color'] = null;
            $pre_resultado['render']['flag'] = false;
            $pre_resultado['render']['con_documentos'] = false;
            $pre_resultado['id'] = $cadena->id;

            array_push($resultado, $pre_resultado);
        }
        $rpta['cabecera'] = $cabecera = [
            trans('texto.codigo_muestra'),
            trans('texto.numero_grupo'),
            trans('texto.Numero_Proceso'),
            trans('texto.Estacion'),
            trans('texto.Fecha_Muestreo'),
            trans('texto.Tipo_Muestra'),
        ];
        $rpta['format'] = ['', '', '', '', '', ''];

        $rpta['pagina']['current_page'] = $cadenas->currentPage();
        $rpta['pagina']['data'] = $resultado;
        $rpta['pagina']['first_page_url'] = $cadenas->url(1);
        $rpta['pagina']['from'] = $cadenas->firstItem();
        $rpta['pagina']['last_page'] = $cadenas->lastPage();
        $rpta['pagina']['last_page_url'] = $cadenas->url($cadenas->lastPage());
        $rpta['pagina']['links'] = $cadenas->toArray()['links'];
        $rpta['pagina']['next_page_url'] = $cadenas->nextPageUrl();
        $rpta['pagina']['path'] = $cadenas->path();
        $rpta['pagina']['per_page'] = intval($cadenas->perPage());
        $rpta['pagina']['prev_page_url'] = $cadenas->previousPageUrl();
        $rpta['pagina']['to'] = $cadenas->lastItem();
        $rpta['pagina']['total'] = $cadenas->total();

        return $rpta; //['muestras']['next_page_url'];
=======
        $fecha = $request->fecha;
        $tipo_muestra = $request->tipo_muestra;
        if (!isset($fecha)) {
            return [];
        }

        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        }
        
        $client = new \GuzzleHttp\Client(['base_uri' => 'http://onlinedata.alslatam.com/wsmylims/public/']);
        
        if (isset($page)) {
            if (isset($tipo_muestra)) {
                $response = $client->request('POST', 'muestras_coc', ['query' => ['page' => $page],'json' => ['fecha' => $fecha, 'tipo_muestra' => $tipo_muestra]]);
            } else {
                $response = $client->request('POST', 'muestras_coc', ['query' => ['page' => $page],'json' => ['fecha' => $fecha]]);
            }
        } else {
            if (isset($tipo_muestra)) {
                $response = $client->request('POST', 'muestras_coc', ['json' => ['fecha' => $fecha, 'tipo_muestra' => $tipo_muestra]]);
            } else {
                $response = $client->request('POST', 'muestras_coc', ['json' => ['fecha' => $fecha]]);
            }
        }

        $respuesta = json_decode($response->getBody()->getContents(), true);
        $respuesta['muestras']['next_page_url'] = str_replace('http://onlinedata.alslatam.com/wsmylims/public/muestras_coc', 'https://api-solutions.alslatam.com/api/GetDataCOC', $respuesta['muestras']['next_page_url']);
        $respuesta['muestras']['prev_page_url'] = str_replace('http://onlinedata.alslatam.com/wsmylims/public/muestras_coc', 'https://api-solutions.alslatam.com/api/GetDataCOC', $respuesta['muestras']['prev_page_url']);
        
        return $respuesta;//['muestras']['next_page_url'];
>>>>>>> 3cae835 (Archivos Subidos)
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

<<<<<<< HEAD
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

=======
>>>>>>> 3cae835 (Archivos Subidos)
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
