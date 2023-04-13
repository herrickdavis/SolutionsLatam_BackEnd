<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Muestras;
use App\Models\Estaciones;

use Throwable;
class SetConsolidarEstacionesController extends Controller
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
        $id_muestra = $request->id_muestra;
        $id_empresa = $request->id_empresa;
        $nombre_estacion = trim($request->nombre_estacion);
        $latitud_n = trim($request->latitud_n);
        $longitud_e = trim($request->longitud_e);
        $zona = trim($request->zona);
        $procedencia = trim($request->procedencia);

        try {
            $estacion = Estaciones::firstOrCreate(
                [
                    'nombre_estacion' => $nombre_estacion,
                    'id_empresa_sol' => $id_empresa,
                ],
                [
                    'id_empresa_con' => $id_empresa,
                    'latitud_n' => $latitud_n,
                    'longitud_e' => $longitud_e,
                    'zona' => $zona,
                    'hemisferio' => 'S',
                    'procedencia' => $procedencia
                ]
            );

            $muestra = Muestras::where('id',$id_muestra)->update(
                ['id_estacion' => $estacion->id]
            );

            if($muestra != null) {
                $rpta['success'] = 'Ok';
                $rpta['mensaje'] = 'Ok';
            } else {
                $rpta['success'] = 'Ok';
                $rpta['mensaje'] = 'Muestra no existe';
            }
        } catch (Throwable $e) {
            report($e);
            $mensaje = $e->getMessage();
            $rpta['error'] = 'error';
            $rpta['id_muestra'] = $id_muestra;
            $rpta['mensaje'] = $mensaje;
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

    // $original = trim($dato['original']);
    //         $consolidada = trim($dato['consolidada']);
    //         $id_cliente = $dato['id_cliente'];

    //         $estacion_original = Estaciones::where('nombre_estacion', '=', $original)->where('id_empresa_sol', '=', $id_cliente)->first();
    //         if ($estacion_original != null) {
    //             $id_estacion_original = $estacion_original->id;
    //             $estacion_consolidada = Estaciones::select('id')->where('nombre_estacion', '=', $consolidada)->where('id_empresa_sol', '=', $id_cliente)->first();
    //             if ($estacion_consolidada != null) {
    //                 $id_estacion_consolidada = $estacion_consolidada->id;
    //             } else {
    //                 //creo la nueva estacion con el nombre de la matriz consolidada y la info de la estacion
    //                 $nueva_estacion = new Estaciones;
    //                 $nueva_estacion->id_empresa_sol = $id_cliente;
    //                 $nueva_estacion->id_empresa_con = $estacion_original->id_empresa_con;
    //                 $nueva_estacion->nombre_estacion = $consolidada;
    //                 $nueva_estacion->latitud_n = $estacion_original->latitud_n;
    //                 $nueva_estacion->longitud_e = $estacion_original->logitud_e;
    //                 $nueva_estacion->zona = $estacion_original->zona;
    //                 $nueva_estacion->hemisferio = $estacion_original->hemisferio;
    //                 $nueva_estacion->procedencia = $estacion_original->procedencia;
    //                 $nueva_estacion->save();
    //                 $id_estacion_consolidada = $nueva_estacion->id;
    //             }
    //             //actualiza las muestras
    //             $muestas = Muestras::where('id_empresa_sol', '=', $id_cliente)
    //                                 ->where('id_estacion', '=', $id_estacion_original)
    //                                 ->update(['id_estacion' => $id_estacion_consolidada]);

    //             $pre_respuesta['success'] = "success";
    //             $pre_respuesta['id_estacion_original'] = $id_estacion_original;
    //             $pre_respuesta['id_estacion_consolidada'] = $id_estacion_consolidada;
    //             array_push($respuesta, $pre_respuesta);
    //             $pre_respuesta = [];
    //         } else {
    //             $pre_respuesta['error'] = "error";
    //             $pre_respuesta['mensaje'] = "No se encontro la estacion original";
    //             $pre_respuesta['id_estacion_original'] = null;
    //             $pre_respuesta['id_estacion_consolidada'] = null;
    //             array_push($respuesta, $pre_respuesta);
    //             $pre_respuesta = [];
    //         }
}
