<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ClickBotones;

use Throwable;

class GetAllEstacionesController extends Controller
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
            $usuario = $request->user();
            $filtros = $request->filtros;
            $estaciones =  DB::table('muestras as m')
            ->select(DB::raw(
                "e.id as id,
                e.nombre_estacion AS estacion,
                e.alias_estacion AS alias_estacion,
                p.nombre_proyecto AS nombre_proyecto,
                tm.nombre_tipo_muestra,
                m.fecha_muestreo AS fecha_muestreo
                "
            ))
            ->leftjoin('estaciones AS e', 'm.id_estacion', '=', 'e.id')            
            ->leftjoin('tipo_muestras AS tm', 'tm.id', '=', 'm.id_tipo_muestra')
            ->leftjoin('proyectos AS p', 'p.id', '=', 'm.id_proyecto')
            ->where('m.activo', '=', 'S')
            ->where('tm.activo', '=', 'S')
            ->distinct()->orderBy('fecha_muestreo', 'desc');
            
            $estaciones = filtroMuestrasQuery($estaciones,$usuario);
            if ($filtros != null) {
                foreach ($filtros as $filtro) {                
                    $pre_cabecera = $filtro['cabecera'];
                    $condicion = $filtro['condicion'];
                    $valor = $filtro['valor'];
                    switch ($pre_cabecera) {
                        case mb_strtolower(trans('texto.Estacion'), 'UTF-8'):
                            $estaciones = $this->filtros($estaciones, 'e.nombre_estacion', $condicion, $valor);
                            break;
                        case mb_strtolower(trans('texto.alias_estacion'), 'UTF-8'):
                            $estaciones = $this->filtros($estaciones, 'e.alias_estacion', $condicion, $valor);
                            break;
                        case strtolower(trans('texto.Proyecto')):
                            $estaciones = $this->filtros($estaciones, 'p.nombre_proyecto', $condicion, $valor);
                            break;
                        case strtolower(trans('texto.Tipo_Muestra')):
                            $estaciones = $this->filtros($estaciones, 'tm.nombre_tipo_muestra', $condicion, $valor);
                            break;
                        case strtolower(trans('texto.Fecha_Muestreo')):
                            $estaciones = $this->filtros($estaciones, 'm.fecha_muestreo', $condicion, $valor);
                            break;                   
                    }
                }
            }
            $estaciones = $estaciones->paginate(20);

            $resultado = [];
            foreach ($estaciones as $estacione) {
                $pre_resultado = [];
                $render = [];                
                    $pre_resultado['data'][] = "".$estacione->estacion;
                    $pre_resultado['data'][] = $estacione->alias_estacion;
                    $pre_resultado['data'][] = $estacione->nombre_proyecto;
                    $pre_resultado['data'][] = $estacione->nombre_tipo_muestra;
                    $pre_resultado['data'][] = $estacione->fecha_muestreo;
                    $pre_resultado['render']['color'] = null;
                    $pre_resultado['render']['flag'] = false;
                    $pre_resultado['render']['con_documentos'] = false;                    
                    $pre_resultado['id'] = $estacione->id;
                
                array_push($resultado, $pre_resultado);
            }

            $rpta['cabecera'] = $cabecera = [
                trans('texto.Estacion'),
                trans('texto.alias_estacion'),
                trans('texto.Proyecto'),
                trans('texto.Tipo_Muestra'),
                trans('texto.Fecha_Muestreo'),
            ];

            $rpta['format'] = ['','','','','','','','','',''];

            $rpta['pagina']['current_page'] = $estaciones->currentPage();
            $rpta['pagina']['data'] = $resultado;
            $rpta['pagina']['first_page_url'] = $estaciones->url(1);
            $rpta['pagina']['from'] = $estaciones->firstItem();
            $rpta['pagina']['last_page'] = $estaciones->lastPage();
            $rpta['pagina']['last_page_url'] = $estaciones->url($estaciones->lastPage());
            $rpta['pagina']['links'] = $estaciones->toArray()['links'];
            $rpta['pagina']['next_page_url'] = $estaciones->nextPageUrl();
            $rpta['pagina']['path'] = $estaciones->path();
            $rpta['pagina']['per_page'] = intval($estaciones->perPage());
            $rpta['pagina']['prev_page_url'] = $estaciones->previousPageUrl();
            $rpta['pagina']['to'] = $estaciones->lastItem();
            $rpta['pagina']['total'] = $estaciones->total();
        } 
        catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }

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
