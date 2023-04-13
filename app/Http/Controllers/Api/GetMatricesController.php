<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ClickBotones;
use Throwable;

class GetMatricesController extends Controller
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
            $analytic_click = new ClickBotones;
            $analytic_click->id_user = $usuario->id;
            $analytic_click->id_boton = 27;
            $analytic_click->save();
            
            $sql_matrices = DB::table('muestras as m')
                        ->select(DB::raw(
                            "mx.nombre_matriz as nombre_matriz,
                            CONCAT('TA',tm.id) as id_tipo_muestra,
                            tm.nombre_tipo_muestra as nombre_tipo_muestra
                            "
                        ))
                        ->leftjoin('matrices AS mx', 'mx.id', '=', 'm.id_matriz')
                        ->leftjoin('tipo_muestras AS tm', 'tm.id', '=', 'm.id_tipo_muestra')
                        //->where('m.id_empresa_sol', '=', $idauxempresa)
                        ->where('m.activo', '=', 'S')
                        ->where('tm.activo', '=', 'S')
                        ->distinct()->orderBy('nombre_matriz');
            
            $sql_matrices = filtroMuestrasQuery($sql_matrices,$usuario);
            $sql_matrices = $sql_matrices->get();

            foreach ($sql_matrices as $valor) {
                $pre = [];
                $pre['id'] = $valor->id_tipo_muestra;
                $pre['tipo_muestra'] = $valor->nombre_tipo_muestra;
                $rpta[$valor->nombre_matriz]['nombre'] = $valor->nombre_matriz;
                $rpta[$valor->nombre_matriz]['tipo_muestras'][] = $pre;
            }
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }

        $respuesta = [];
        foreach ($rpta as $value) {
            array_push($respuesta, $value);
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
