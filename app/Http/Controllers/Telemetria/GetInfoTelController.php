<?php

namespace App\Http\Controllers\Telemetria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class GetInfoTelController extends Controller
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
            $sql_info = DB::table('telemetria_resultados as tr')
                                ->select(DB::raw(
                                    "
                                    te.id as id_estacion,
                                    te.nombre_estacion as nombre_estacion,
                                    tp.id as id_parametro,
                                    tp.nombre_parametro as nombre_parametro
                                    "
                                ))
                                ->join('telemetria_muestras as tm', 'tm.id', '=', 'tr.muestra_id')
                                ->join('telemetria_estacions as te', 'te.id', '=', 'tm.estacion_id')
                                ->join('telemetria_parametros as tp', 'tp.id', '=', 'tr.parametro_id')
                                ->distinct()->orderBy('nombre_estacion', 'ASC')->get();
    
            // Reorganizar los datos para agruparlos por estación
            $sql_info_grouped = $sql_info->groupBy('id_estacion')
            ->map(function ($items, $id_estacion) {
                return [
                    'id_estacion' => $id_estacion,
                    'nombre_estacion' => $items->first()->nombre_estacion,
                    'parametros' => $items->map(function ($item) {
                        return [
                            'id_parametro' => $item->id_parametro,
                            'nombre_parametro' => $item->nombre_parametro,
                        ];
                    })->values()->all() // values()->all() para resetear las llaves del array
                ];
            })->values()->all(); // Convertir a un array y resetear las llaves

        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return $sql_info_grouped;
        
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
