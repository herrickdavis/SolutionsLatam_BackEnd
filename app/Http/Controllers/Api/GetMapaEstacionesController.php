<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Throwable;

class GetMapaEstacionesController extends Controller
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

        $id_parametro = $request->id_parametro;
        $estaciones = $request->id_estaciones;

        try {
            foreach ($estaciones as $estacion) {
                $id_estacion = substr($estacion, 1);
                $data_estacion = DB::table('muestras as m')
                                ->select(DB::raw(
                                    "m.numero_muestra,
                                    es.nombre_estacion,
                                    mp.valor,
                                    m.fecha_muestreo
                                    "
                                ))
                                ->join('muestra_parametros AS mp', 'mp.id_muestra', '=', 'm.id')
                                ->join('empresas AS em', 'em.id', '=', 'm.id_empresa_sol')
                                ->join('estaciones AS es', 'es.id', '=', 'm.id_estacion')
                                ->where('es.id', '=', $id_estacion)
                                ->where('m.flativo', '=', 'S')
                                ->where('mp.id_parametro', '=', str_replace('P', '', $id_parametro))
                                ->orderBy('m.fecha_muestreo', 'DESC')->limit(1)->get();
            }
            $data_estacion = filtroMuestrasQuery($data_estacion,$usuario);
            $data_estacion = $data_estacion->get();
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return $data_estacion;
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
