<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClickBotones;
use Illuminate\Support\Facades\DB;
use Throwable;

class SetColumnasUsuarioController extends Controller
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
        $orden = $request->orden;
        $array_orden = [];

        $analytic_click = new ClickBotones;
        $analytic_click->id_user = $usuario->id;
        $analytic_click->id_boton = 14;
        $analytic_click->save();
        
        foreach ($orden as $columna) {
            $columna = $this->quitar_tilde($columna);
            switch ($columna) {
                case strtolower(trans('texto.codigo_muestra')):
                    $a = [
                        'texto' => 'texto.codigo_muestra',
                        'tabla' => 'codigo_muestra'
                    ];
                    array_push($array_orden, $a);
                    break;
                case strtolower(trans('texto.numero_muestra')):
                    $a = [
                        'texto' => 'texto.numero_muestra',
                        'tabla' => 'numero_muestra'
                    ];
                    array_push($array_orden, $a);
                    break;
                case strtolower(trans('texto.numero_grupo')):
                    $a = [
                        'texto' => 'texto.numero_grupo',
                        'tabla' => 'numero_grupo'
                    ];
                    array_push($array_orden, $a);
                    break;
                case strtolower(trans('texto.Estado')):
                    $a = [
                        'texto' => 'texto.Estado',
                        'tabla' => 'estado'
                    ];
                    array_push($array_orden, $a);
                    break;
                case strtolower(trans('texto.Proyecto')):
                    $a = [
                        'texto' => 'texto.Proyecto',
                        'tabla' => 'proyecto'
                    ];
                    array_push($array_orden, $a);
                    break;
                case $this->quitar_tilde(strtolower(trans('texto.Estacion'))):
                    $a = [
                        'texto' => 'texto.Estacion',
                        'tabla' => 'estacion'
                    ];
                    array_push($array_orden, $a);
                    break;
                case strtolower(trans('texto.Tipo_Muestra')):
                    $a = [
                        'texto' => 'texto.Tipo_Muestra',
                        'tabla' => 'tipo_muestra'
                    ];
                    array_push($array_orden, $a);
                    break;
                case strtolower(trans('texto.Solicitante')):
                    $a = [
                        'texto' => 'texto.Solicitante',
                        'tabla' => 'solicitante'
                    ];
                    array_push($array_orden, $a);
                    break;
                case strtolower(trans('texto.Contratante')):
                    $a = [
                        'texto' => 'texto.Contratante',
                        'tabla' => 'contratante'
                    ];
                    array_push($array_orden, $a);
                    break;
                case strtolower(trans('texto.Fecha_Muestreo')):
                    $a = [
                        'texto' => 'texto.Fecha_Muestreo',
                        'tabla' => 'fecha_muestreo'
                    ];
                    array_push($array_orden, $a);
                    break;
                default:
                    # code...
                    break;
            }
        }

        try {
            DB::table('columnas_usuarios')
            ->updateOrInsert(
                ['id_user' => $usuario->id, 'numero_tabla' => $request->numero_tabla],
                ['orden' => json_encode($array_orden)]
            );

            $hubo_error = false;
        } catch (Throwable $e) {
            report($e);
            $hubo_error = true;
            $mensaje = $e->getMessage();
        }
        
        if ($hubo_error) {
            $rpta['error'] = "error";
            $rpta['mensaje'] = $mensaje;
            return $rpta;
        } else {
            $rpta['success'] = "success";
            $rpta['mensaje'] = "Ok";
            return $rpta;
        }
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

    private function quitar_tilde($texto)
    {
        $texto = strtolower($texto);
        $texto = str_replace('á', 'a', $texto);
        $texto = str_replace('é', 'e', $texto);
        $texto = str_replace('í', 'i', $texto);
        $texto = str_replace('ó', 'o', $texto);
        $texto = str_replace('ú', 'u', $texto);
        return $texto;
    }
}
