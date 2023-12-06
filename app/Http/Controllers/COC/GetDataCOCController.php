<?php

namespace App\Http\Controllers\COC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

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
        $filtros = $request->filtros;

        //Terminamos de leer datos
        $id_pais = $request->id_pais;
        $id_empresas = $request->id_empresas;
        if($id_pais != null && $id_empresas != null) {
            $cadenas = DB::table("cadenas as c")->where('id_pais',$id_pais)->whereIn('id_empresa',$id_empresas);
        } else {
            $cadenas = DB::table("cadenas as c")->whereIn('id_empresa',[1]);
        }

        if ($filtros != null) {
            foreach ($filtros as $filtro) {
                $pre_cabecera = $filtro['cabecera'];
                $condicion = $filtro['condicion'];
                $valor = $filtro['valor'];
                
                switch ($pre_cabecera) {
                    case strtolower(trans('texto.codigo_muestra')):
                        $cadenas = $this->filtros($cadenas, 'c.codigo_laboratorio', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.numero_grupo')):
                        $cadenas = $this->filtros($cadenas, 'c.numero_grupo', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.numero_proceso')):
                        $cadenas = $this->filtros($cadenas, 'c.numero_proceso', $condicion, $valor);
                        break;
                    case mb_strtolower(trans('texto.Estacion'), 'UTF-8'):
                        $cadenas = $this->filtros($cadenas, 'c.estacion', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.Fecha_Muestreo')):
                        $cadenas = $this->filtros($cadenas, 'c.fecha_muestreo', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.Hora_Muestreo')):
                            $cadenas = $this->filtros($cadenas, 'c.hora_muestreo', $condicion, $valor);
                            break;
                    case strtolower(trans('texto.Tipo_Muestra')):
                        $cadenas = $this->filtros($cadenas, 'c.tipo_muestra', $condicion, $valor);
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
            $pre_resultado['data'][] = $cadena->fecha_muestreo;
            $pre_resultado['data'][] = substr($cadena->hora_muestreo, 0, 5);
            $pre_resultado['data'][] = $cadena->tipo_muestra;
            $pre_resultado['render']['color'] = null;
            $pre_resultado['render']['flag'] = false;
            $pre_resultado['render']['con_documentos'] = false;
            $pre_resultado['id'] = intval($cadena->codigo_laboratorio);

            array_push($resultado, $pre_resultado);
        }
        $rpta['cabecera'] = $cabecera = [
            trans('texto.codigo_muestra'),
            trans('texto.numero_grupo'),
            trans('texto.numero_proceso'),
            trans('texto.Estacion'),
            trans('texto.Fecha_Muestreo'),
            trans('texto.Hora_Muestreo'),
            trans('texto.Tipo_Muestra'),
        ];
        $rpta['format'] = ['', '', '', '', '', '', ''];

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
        return $rpta;
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
