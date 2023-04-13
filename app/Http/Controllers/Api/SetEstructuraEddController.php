<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Models\Reportes;
use App\Models\UsuarioReportes;

use Throwable;

class SetEstructuraEddController extends Controller
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
            $configuracion = $request->columnas;
            $nombre_reporte = $request->nombre_reporte;

            DB::beginTransaction();
            $reporte = new Reportes();
            $reporte->nombre_reporte = $nombre_reporte;
            $reporte->activo = 'S';
            $reporte->configuracion = json_encode($configuracion);
            $reporte->save();

            $usuarioReporte = new UsuarioReportes();
            $usuarioReporte->id_user = $usuario->id;
            $usuarioReporte->id_reporte = $reporte->id;
            $usuarioReporte->save();
            DB::commit();
            $rpta['success'] = 'Ok';
            $rpta['mensaje'] = "Se creo el reporte correctamente";
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);
            $mensaje = $e->getMessage();
            $rpta['error'] = 'error';
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
}