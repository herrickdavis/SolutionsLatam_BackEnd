<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class GetAllProyectosController extends Controller
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
            $proyectos =  DB::table('muestras as m')
            ->select(DB::raw(
                "p.id as id,
                p.nombre_proyecto AS nombre_proyecto,
                p.alias_proyecto AS alias_proyecto
                "
            ))
            ->leftjoin('proyectos AS p', 'p.id', '=', 'm.id_proyecto')
            ->whereIn('m.id_estado', [3,4])
            ->where('m.activo', '=', 'S')
            ->distinct()->orderBy('fecha_muestreo', 'desc');
            
            $proyectos = filtroMuestrasQuery($proyectos,$usuario);
            if ($filtros != null) {
                foreach ($filtros as $filtro) {                
                    $pre_cabecera = $filtro['cabecera'];
                    $condicion = $filtro['condicion'];
                    $valor = $filtro['valor']; 
                    \Log::info('Showing the user profile for user: '.$pre_cabecera.' '.mb_strtolower(trans('texto.Estacion'), 'UTF-8'));               
                    switch ($pre_cabecera) {
                        case mb_strtolower(trans('texto.Proyecto'), 'UTF-8'):
                            \Log::info("Filtramos");
                            $proyectos = $this->filtros($proyectos, 'p.nombre_proyecto', $condicion, $valor);
                            break;
                        case strtolower(trans('texto.alias_proyecto')):
                            $proyectos = $this->filtros($proyectos, 'p.alias_proyecto', $condicion, $valor);
                            break;
                    }
                }
            }
            $proyectos = $proyectos->paginate(20);

            $resultado = [];
            foreach ($proyectos as $proyecto) {
                $pre_resultado = [];
                $render = [];                
                    $pre_resultado['data'][] = "".$proyecto->nombre_proyecto;
                    $pre_resultado['data'][] = $proyecto->alias_proyecto;
                    $pre_resultado['render']['color'] = null;
                    $pre_resultado['render']['flag'] = false;
                    $pre_resultado['render']['con_documentos'] = false;                    
                    $pre_resultado['id'] = $proyecto->id;
                
                array_push($resultado, $pre_resultado);
            }

            $rpta['cabecera'] = $cabecera = [
                trans('texto.Proyecto'),
                trans('texto.alias_proyecto')
            ];

            $rpta['format'] = ['',''];

            $rpta['pagina']['current_page'] = $proyectos->currentPage();
            $rpta['pagina']['data'] = $resultado;
            $rpta['pagina']['first_page_url'] = $proyectos->url(1);
            $rpta['pagina']['from'] = $proyectos->firstItem();
            $rpta['pagina']['last_page'] = $proyectos->lastPage();
            $rpta['pagina']['last_page_url'] = $proyectos->url($proyectos->lastPage());
            $rpta['pagina']['links'] = $proyectos->toArray()['links'];
            $rpta['pagina']['next_page_url'] = $proyectos->nextPageUrl();
            $rpta['pagina']['path'] = $proyectos->path();
            $rpta['pagina']['per_page'] = intval($proyectos->perPage());
            $rpta['pagina']['prev_page_url'] = $proyectos->previousPageUrl();
            $rpta['pagina']['to'] = $proyectos->lastItem();
            $rpta['pagina']['total'] = $proyectos->total();
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
