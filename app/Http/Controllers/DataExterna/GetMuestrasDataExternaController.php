<?php

namespace App\Http\Controllers\DataExterna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GetMuestrasDataExternaController extends Controller
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
        $user = $request->user();
        $pagina = $request->pagina;
        if(!isset($pagina)) {
            $pagina = 1;
        }
        $porPagina = 1000; // Número de registros por página
        $skip = ($pagina - 1) * $porPagina;
        $registros = DB::table('data_externa_temporals')
                ->where('id_user', $user->id)
                ->skip($skip)
                ->take($porPagina)
                ->get()
                ->map(function ($registro) {
                    if (isset($registro->fecha_muestreo)) {
                        $registro->fecha_muestreo = Carbon::parse($registro->fecha_muestreo)->format('d-m-Y');
                    }
                    return $registro;
                });

        return response()->json($registros);
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
