<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Edd;
use App\Models\EddCampos;

use Throwable;

class SetPlanillaEddController extends Controller
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
        try {
            $user_id = $usuario->id;
            $nombre_reporte = $request->nombre_reporte;
            $configuracion = $request->configuracion;
            $es_publico = 'N';
            $activo = 'S';

            if($request->id != 0) {
                $reporte = Edd::find($request->id);
            } else {
                $reporte = new Edd();
            }
            $reporte->user_id = $user_id;
            $reporte->nombre_reporte = $nombre_reporte;
            $reporte->configuracion = json_encode($configuracion, true);
            $reporte->es_publico = $es_publico;
            $reporte->activo = $activo;
            $reporte->save();

            $rpta["success"] = "Ok";
            $rpta["mensaje"] = "Ok";
        } catch (\Throwable $e) {
            report($e);
            $rpta["error"] = "error";
            $rpta["mensaje"] = $e->getMessage();
        }

        return response()->json($rpta);
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
