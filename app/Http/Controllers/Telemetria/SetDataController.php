<?php

namespace App\Http\Controllers\Telemetria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TelemetriaUnidad;
use App\Models\TelemetriaParametro;
use App\Models\TelemetriaAbreviaturaProcesamiento;
use App\Models\TelemetriaEstacion;
use App\Models\TelemetriaMuestra;
use App\Models\TelemetriaResultado;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Throwable;

class SetDataController extends Controller
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
        set_time_limit(2400);
        $tamañoDelChunk = 5000;
        $hubo_error = false;
        try {
            foreach (array_chunk($request->all(), $tamañoDelChunk) as $chunk) {
                TelemetriaResultado::insert($chunk);
            }
            
        } catch (Throwable $e) {
            DB::rollBack();
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

    function limpiarCaracteresEspeciales($cadena) {
        // Reemplaza caracteres especiales con su equivalente sin acentos
        $cadena = iconv('UTF-8', 'ASCII//TRANSLIT', $cadena);
        
        // Elimina cualquier caracter que no sea alfanumérico, espacio, guión o guión bajo
        $cadena = preg_replace("/[^a-zA-Z0-9 _-]/", '', $cadena);
        
        // Opcionalmente, convierte espacios a guiones o realiza otras transformaciones necesarias
        // $cadena = str_replace(" ", "-", $cadena);
        
        return $cadena;
    }
}
