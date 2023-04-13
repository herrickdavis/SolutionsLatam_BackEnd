<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Throwable;

class GetProyectosController extends Controller
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
            //$id_empresa_sol = $request->user()->id_empresa;
            $usuario = $request->user();            

            $id_tipo_muestra = str_replace("TA", "", $request->id_tipo_muestra);
            $fecha_inicio = $request->fecha_inicio;
            $fecha_fin = $request->fecha_fin;
            $fuera_limite = $request->fuera_limite;
            DB::enableQueryLog();

            $sql_proyectos = DB::table('muestras as m')
                        ->select(DB::raw(
                            "case when gp.grupo_proyecto is null then CONCAT('P',p.id) else CONCAT('G',gp.id) end as id,
                            case when gp.grupo_proyecto is null then p.nombre_proyecto else gp.grupo_proyecto end as nombre_proyecto
                            "
                        ))
                        ->leftjoin('proyectos AS p', 'm.id_proyecto', '=', 'p.id')
                        ->leftjoin('proyecto_grupo_proyectos AS pgp', 'pgp.id_proyecto', '=', 'p.id')
                        ->leftjoin('grupo_proyectos AS gp', 'gp.id', '=', 'pgp.id_grupo_proyecto')
                        ->where('m.id_tipo_muestra', '=', $id_tipo_muestra)
                        ->whereIn('m.id_estado', [3,4])
                        ->where('m.activo', '=', 'S')
                        ->distinct('id')->orderBy('nombre_proyecto');            

            $sql_proyectos = filtroMuestrasQuery($sql_proyectos,$usuario);
            $sql_proyectos = $sql_proyectos->get();
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return $sql_proyectos;
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
