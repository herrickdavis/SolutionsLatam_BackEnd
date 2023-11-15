<?php

namespace App\Http\Controllers\DataExterna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetInfoController extends Controller
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
        $parametros = DB::table('parametros as p')
                                ->select(DB::raw(
                                    "case when gp.grupo_parametros is null then CONCAT('P',p.id) else CONCAT('G',gp.id) end as id,
                                     case when gp.grupo_parametros is null then p.nombre_parametro else gp.grupo_parametros end as nombre_parametro
                                    "
                                ))                                                                                                
                                ->leftjoin('parametro_grupo_parametros AS pgp', 'pgp.id_parametro', '=', 'p.id')
                                ->leftjoin('grupo_parametros AS gp', 'gp.id', '=', 'pgp.id_grupo_parametro')
                                ->distinct()->get();

        $matrices = DB::table('matrices as mx')
                            ->select(DB::raw(
                                "CAST(CASE 
                                WHEN gm.nombre_grupo_matriz is null then CONCAT('998',mx.id) else CONCAT('999',gm.id) end AS UNSIGNED) as id,
                                CASE
                                WHEN gm.nombre_grupo_matriz is null then mx.nombre_matriz else gm.nombre_grupo_matriz end as nombre_matriz"
                            ))
                            ->leftjoin('matriz_grupo_matrices as mgm', 'mgm.id_matriz', '=', 'mx.id')
                            ->leftjoin('grupo_matrices as gm', 'gm.id', '=', 'mgm.id_grupo_matriz')
                            ->distinct()->get();

        $tipo_muestras = DB::table('tipo_muestras as tm')
                                ->select(DB::raw(
                                    "CONCAT('TA',tm.id) as id,
                                    tm.nombre_tipo_muestra as nombre_tipo_muestra"
                                ))
                                ->distinct()->get();
        
        $estaciones = DB::table('estaciones as e')
        ->select(DB::raw(
            "e.id as id,
            case when e.alias_estacion is null then e.nombre_estacion else e.nombre_estacion end as nombre_estacion"
        ))->get();

        $proyectos = DB::table('proyectos as p')
        ->select(DB::raw(
            "p.id as id,
            case when p.alias_proyecto is null then p.nombre_proyecto else p.nombre_proyecto end as nombre_proyecto"
        ))->get();

        $empresas = DB::table('empresas as e')
        ->select(DB::raw(
            "e.id as id,
            e.nombre_empresa as nombre_empresa"
        ))->get();
            
        $data = [];
        $data['parametros'] = $parametros;
        $data['matrices'] = $matrices;
        $data['tipo_muestras'] = $tipo_muestras;
        $data['estaciones'] = $estaciones;
        $data['proyectos'] = $proyectos;
        $data['empresas'] = $empresas;

        return $data;
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
