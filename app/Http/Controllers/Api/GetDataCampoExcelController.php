<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataCampo;
use App\Exports\DataCampoExport;

use Throwable;
class GetDataCampoExcelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usuario = \Auth::user();
        $idauxempresa = $usuario->id_empresa;

        try {
            $datos = DataCampo::select(DataCampo::raw(
                "id_muestra, 
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

            $datos = $datos->get();
            $parametros = [];
            $muestras = [];
            //creo matriz con los parametros reportados
            foreach ($datos as $data) {
                $parametros[] = $data->info;
            }
            $parametros = array_unique($parametros);
            sort($parametros);

            foreach ($datos as $data) {
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
                        $muestras[$data->id_muestra][$data->info] = str_replace(",", ".", $data->valor);
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
            array_unshift($parametros, 'Proyecto', 'Estacion', 'Fec. Muestreo', 'Tip. Muestra');
            array_push($parametros, 'Observaciones');
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        
        return \Excel::download(new DataCampoExport($data, $parametros), 'Data Campo.xlsx');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $usuario = \Auth::user();
        $idauxempresa = $usuario->id_empresa;

        try {
            $datos = DataCampo::select(DataCampo::raw(
                "id_muestra, 
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

            $datos = $datos->get();
            $parametros = [];
            $muestras = [];
            //creo matriz con los parametros reportados
            foreach ($datos as $data) {
                $parametros[] = $data->info;
            }
            $parametros = array_unique($parametros);
            sort($parametros);

            foreach ($datos as $data) {
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
                        $muestras[$data->id_muestra][$data->info] = str_replace(",", ".", $data->valor);
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
            array_unshift($parametros, 'Proyecto', 'Estacion', 'Fec. Muestreo', 'Tip. Muestra');
            array_push($parametros, 'Observaciones');
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        
        return \Excel::download(new DataCampoExport($data, $parametros), 'Data Campo.xlsx');
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
