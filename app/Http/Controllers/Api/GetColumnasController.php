<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class GetColumnasController extends Controller
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
        $tabla = $request->tabla;
        switch ($tabla) {
            case 1:
                $all_columnas = ['texto.Contratante','texto.Solicitante','texto.Tipo_Muestra','texto.Estacion',
                'texto.Proyecto','texto.Estado','texto.numero_grupo','texto.numero_muestra','texto.codigo_muestra',
                'texto.Fecha_Muestreo'];
                sort($all_columnas);

                $columnas = DB::table('columnas_usuarios as cu')->where('cu.id_user', '=', $usuario->id)
                                ->where('cu.numero_tabla', '=', '1')->first();

                $respuesta = [];
                if (isset($columnas->orden)) {
                    $columnas = json_decode($columnas->orden);
                    foreach ($columnas as $columna) {
                        $array_columnas[] = $columna->texto;
                    }

                    foreach ($all_columnas as $columna) {
                        if (in_array($columna, $array_columnas)) {
                            $pre['columna'] = trans($columna);
                            $pre['estado'] = true;
                            $respuesta[] = $pre;
                        } else {
                            $pre['columna'] = trans($columna);
                            $pre['estado'] = false;
                            $respuesta[] = $pre;
                        }
                    }
                } else {
                    foreach ($all_columnas as $columna) {
                        $pre['columna'] = trans($columna);
                            $pre['estado'] = true;
                            $respuesta[] = $pre;
                    }
                }

                
                
                
                break;
            case 2:
                # code...
                break;
            default:
                # code...
                break;
        }

        return $respuesta;
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
