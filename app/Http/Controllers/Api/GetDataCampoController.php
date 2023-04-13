<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\DataCampo;

class GetDataCampoController extends Controller
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
        $idauxempresa = $usuario->id_empresa;
        $numero_fila = $request->rowPage;

        $filtros = $request->filtros;
        $datos = DataCampo::select(DataCampo::raw(
            "id_muestra,
            numero_grupo, 
            id_empresa_sol,
            id_empresa_con,
            nombre_proyecto,
            nombre_estacion,
            tipo_muestra,
            info,
            valor,
            observacion,
            DATE_FORMAT(fecha_muestreo,'%d-%m-%Y %H:%i') as fecha_muestreo"
        ));
        
        if ($usuario->ver_empresa_con == 'S') {
            $datos = $datos->where('id_empresa_con', '=', $idauxempresa);
        } else {
            $datos = $datos->where('id_empresa_sol', '=', $idauxempresa);
        }
        //return $request;
        if ($filtros != null) {
            foreach ($filtros as $filtro) {
                //var_dump($filtro);
                $cabecera = $filtro['cabecera'];
                $condicion = $filtro['condicion'];
                $valor = $filtro['valor'];
                switch ($cabecera) {
                    case strtolower(trans('texto.codigo_muestra')):
                        $datos = $this->filtros($datos, 'id_muestra', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.numero_grupo')):
                        $datos = $this->filtros($datos, 'numero_grupo', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.Proyecto')):
                        $datos = $this->filtros($datos, 'nombre_proyecto', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.Estacion')):
                        $datos = $this->filtros($datos, 'nombre_estacion', $condicion, $valor);
                        break;
                    case strtolower(trans('texto.Fec_Muestreo')):
                        $datos = $this->filtros($datos, 'fecha_muestreo', $condicion, $valor);
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        
        
        $datos = $datos->get();
        //creo matriz con los parametros reportados
        $parametros = [];
        $muestras = [];
        foreach ($datos as $data) {
            $parametros[] = $data->info;
        }
        $parametros = array_unique($parametros);
        sort($parametros);

        foreach ($datos as $data) {
            $muestras[$data->id_muestra]['id_muestra'] = $data->id_muestra."";
            $muestras[$data->id_muestra]['numero_grupo'] = ($data->numero_grupo == null) ? '---':$data->numero_grupo;
            $muestras[$data->id_muestra]['proyecto'] = $data->nombre_proyecto;
            $muestras[$data->id_muestra]['estacion'] = $data->nombre_estacion;
            $muestras[$data->id_muestra]['fecha_muestro'] = $data->fecha_muestreo;
            $muestras[$data->id_muestra]['tipo_muestra'] = $data->tipo_muestra;
            //$muestras[$data->id_muestra][$data->info] = str_replace(",", ".", $data->valor);
            foreach ($parametros as $parametro) {
                $muestras[$data->id_muestra][$parametro] = null;
            }
        }

        foreach ($datos as $data) {
            $muestras[$data->id_muestra]['observacion'] = $data->observacion;
        }

        foreach ($datos as $data) {
            foreach ($parametros as $parametro) {
                if ($data->info == $parametro) {
                    $muestras[$data->id_muestra][$data->info] = str_replace(",", ".", ($data->valor == null) ? "---":$data->valor);
                }
            }
        }

        $data = [];
        foreach ($muestras as $muestra) {
            $pre_data = [];
            foreach ($muestra as $key => $value) {
                $pre_data[] = $value;
            }
            array_push($data, $pre_data);
        }
        //return $data;
        array_unshift($parametros, trans('texto.codigo_muestra'), trans('texto.numero_grupo'), trans('texto.Proyecto'), trans('texto.Estacion'), trans('texto.Fec_Muestreo'), trans('texto.Tipo_Muestra'));
        array_push($parametros, trans('texto.Observación'));
        
        if ($numero_fila == null) {
            $numero_fila = 20;
        }
        $data_paginada = $this->paginate($data,$numero_fila);
        $rpta['cabecera'] = $parametros;
        $rpta['pagina'] = $data_paginada;
        
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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function paginate($items, $perPage = 20, $page = null, $options = [])
    {
        $collection = new Collection($items);

        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        $currentPageResults = $collection->slice(($page - 1) * $perPage, $perPage)->values();

        //return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
        return new LengthAwarePaginator($currentPageResults, $items->count(), intval($perPage), $page, $options);
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
