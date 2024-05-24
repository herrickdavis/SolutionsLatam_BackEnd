<?php

namespace App\Http\Controllers\Telemetria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TelemetriaUnidad;
use App\Models\TelemetriaParametro;
use App\Models\TelemetriaAbreviaturaProcesamiento;
use App\Models\TelemetriaEstacion;
use App\Models\TelemetriaGrupoParametro;
use App\Models\TelemetriaLimite;
use App\Models\TelemetriaLimiteParametro;
use App\Models\TelemetriaMuestra;
use App\Models\TelemetriaParametroGrupoParametro;
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
        $tamañoDelChunk = 2000;
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

    public function setLimites(Request $request)
    {
        $id_limite = $request->id_limite;
        $nombre_limite = $request->nombre_limite;
        $parametros = $request->parametros;
        try {
            if($id_limite != null & $id_limite != 0) {
                $limite = TelemetriaLimite::updateOrCreate(
                    ['id' => $id_limite],
                    ['nombre_limite' => $nombre_limite]
                );
                $speach = "Se actualizó correctamente el límite: ";
            } else {
                $limite = TelemetriaLimite::updateOrCreate(
                    ['nombre_limite' => $nombre_limite]
                );
                $speach = "Se creo correctamente el límite: ";
            }
            
            foreach($parametros as $parametro) {
                $limite_parametros = TelemetriaLimiteParametro::updateOrCreate(
                    [
                        'limite_id' => $limite->id,
                        'parametro_id' => $parametro['id_parametro'],
                    ],
                    [
                        'limite_inferior' => $parametro['limite_inferior'],
                        'limite_superior' => $parametro['limite_superior']
                    ]
                );
            }
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($speach.$nombre_limite);
    }

    public function setGrupoParametros(Request $request)
    {
        $id_grupo_parametro = $request->id_grupo_parametro;
        $nombre_grupo_parametro = $request->nombre_grupo_parametro;
        $id_empresa = 947;
        $parametros = $request->parametros;
        try {
            if($id_grupo_parametro != null & $id_grupo_parametro != 0) {
                $grupo_parametro = TelemetriaGrupoParametro::updateOrCreate(
                    [
                        'id' => $id_grupo_parametro,
                        'empresa_id' => $id_empresa
                    ],
                    ['nombre_grupo_parametro' => $nombre_grupo_parametro]
                );
                $speach = "Se actualizó correctamente el grupo de parámetros: ";
            } else {
                $grupo_parametro = TelemetriaGrupoParametro::updateOrCreate(
                    [
                        'empresa_id' => $id_empresa,
                        'nombre_grupo_parametro' => $nombre_grupo_parametro
                    ]
                );
                $speach = "Se creo correctamente el grupo de parámetros: ";
            }
            //primero desactivo todos los parametros del grupo y activo si existen y creo si no 
            TelemetriaParametroGrupoParametro::where('grupo_parametro_id',$grupo_parametro->id)                                                
                                                ->update(['estado' => 'N']);
            foreach($parametros as $parametro) {
                $parametro_grupo_parametro = TelemetriaParametroGrupoParametro::updateOrCreate(
                    [
                        'grupo_parametro_id' => $grupo_parametro->id,
                        'parametro_id' => $parametro['parametro_id'],
                    ],
                    [
                        'estado' => 'S'
                    ]
                );
            }
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($speach.$nombre_grupo_parametro);
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
