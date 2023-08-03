<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Edd;
use App\Models\EddCampos;
use Throwable;

class SetEddController extends Controller
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
        $user_id = $request->user_id;
        $nombre_reporte = $request->nombre_reporte;
        $es_publico = $request->es_publico;
        $activo = $request->activo;
        $edd_campos = $request->campos;

        DB::beginTransaction();
        $reporte = new Edd();
        $reporte->user_id = $user_id;
        $reporte->nombre_reporte = $nombre_reporte;
        $reporte->es_publico = $es_publico;
        $reporte->activo = $activo;
        $reporte->save();

        foreach ($edd_campos as $value) {
            $campos = new EddCampos();
            $campos->id_edd = $reporte->id;
            $campos->nombre_tabla = $value["tabla"];
            $campos->nombre_campo = $value["campo"];
            $campos->nombre_mostrar = $value["nombre_mostrar"];
            $campos->orden_campo = $value["posicion"];
            $campos->activo = $value["activo"];
            $campos->save();
        }
        

        DB::commit();

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
