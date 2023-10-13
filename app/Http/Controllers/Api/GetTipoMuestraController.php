<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ClickBotones;
use Throwable;

class GetTipoMuestraController extends Controller
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
                        
            $fuera_limite = $request->fuera_limite;
            $fecha_inicio = $request->fecha_inicio;
            $fecha_fin = $request->fecha_fin;
            $id_matriz = $request->id_matriz;

            if ($fuera_limite) {
                $analytic_click = new ClickBotones;
                $analytic_click->id_user = $usuario->id;
                $analytic_click->id_boton = 24;
                $analytic_click->save();
                $sql_tipo_muestra = DB::table('muestras as m')
                            ->select(DB::raw(
                                "CONCAT('TA',tm.id) as id,
                                tm.nombre_tipo_muestra as nombre_tipo_muestra"
                            ))
                            ->leftjoin('muestra_parametros as mp', 'mp.id_muestra', '=', 'm.id')
                            ->leftjoin('tipo_muestras as tm', 'tm.id', '=', 'm.id_tipo_muestra')
                            ->where('m.fecha_muestreo', '>', $fecha_inicio)
                            ->where('m.fecha_muestreo', '<', $fecha_fin)
                            ->where('mp.id_parecer', '=', 3)
                            //->where('m.id_empresa_sol', '=', $id_empresa_sol)
                            ->distinct('id')->orderBy('nombre_tipo_muestra');
            } else {
                $analytic_click = new ClickBotones;
                $analytic_click->id_user = $usuario->id;
                $analytic_click->id_boton = 19;
                $analytic_click->save();
                //datos con grupo de matriz
                if (substr(strval($id_matriz),0,3) == "999") {
                    $id_matriz = str_replace('999','',$id_matriz);
                    $sql_tipo_muestra = DB::table('muestras as m')
                            ->select(DB::raw(
                                "CONCAT('TA',tm.id) as id,
                                tm.nombre_tipo_muestra as nombre_tipo_muestra"
                            ))
                            ->leftjoin('tipo_muestras as tm', 'tm.id', '=', 'm.id_tipo_muestra')
                            ->leftjoin('matriz_grupo_matrices as gm', 'gm.id_matriz', '=', 'm.id_matriz')
                            ->where('gm.id_grupo_matriz', '=', $id_matriz)->distinct('id')->orderBy('nombre_tipo_muestra');
                } elseif (substr(strval($id_matriz),0,3) == "998") { //sin grpo de matrices 
                    $id_matriz = str_replace('998','',$id_matriz);
                    $sql_tipo_muestra = DB::table('muestras as m')
                            ->select(DB::raw(
                                "CONCAT('TA',tm.id) as id,
                                tm.nombre_tipo_muestra as nombre_tipo_muestra"
                            ))
                            ->leftjoin('tipo_muestras as tm', 'tm.id', '=', 'm.id_tipo_muestra')
                            ->where('m.id_matriz', '=', $id_matriz)->distinct('id')->orderBy('nombre_tipo_muestra');
                }
            }

            $sql_tipo_muestra = filtroMuestrasQuery($sql_tipo_muestra,$usuario);
            $sql_tipo_muestra = $sql_tipo_muestra->where('tm.activo', '=', 'S');
            $sql_tipo_muestra = $sql_tipo_muestra->get();
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return $sql_tipo_muestra;
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
