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
use App\Models\TelemetriaCriteriosValidacion;
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

    public function setValidacionResultados(Request $request)
    {

    }

    function updateEstadoResultado(Request $request)
    {
        $resultados = $request->all();

        // Iniciar una transacción
        DB::beginTransaction();

        try {
            DB::table('telemetria_id_cambios')->truncate();
            DB::table('telemetria_id_cambios')->insert($resultados);
            DB::statement('
                UPDATE telemetria_resultados tr
                JOIN telemetria_id_cambios tc ON tr.muestra_id = tc.id
                SET tr.estado_id = tc.estado
            ');

            // Confirmar la transacción
            DB::commit();

            return response()->json(['message' => 'Actualización exitosa'], 200);
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json(['message' => 'Error en la actualización', 'error' => $e->getMessage()], 500);
        }
    }

    function updateEstadoNullResultado(Request $request)
    {
        set_time_limit(2400);
        DB::beginTransaction();
        try {
            DB::statement('
                UPDATE telemetria_resultados
                SET estado_id = 3
                WHERE (estado_id IS NULL OR estado_id = 1) AND resultado IS NULL
            ');

            DB::commit();

            return response()->json(['message' => 'Actualización exitosa'], 200);
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json(['message' => 'Error en la actualización', 'error' => $e->getMessage()], 500);
        }
    }

    function setNotificaciones(Request $request)
    {
        set_time_limit(2400);
        $resultados = $request->all();
        DB::beginTransaction();
        try {
            DB::table('notificacions')->insert($resultados);

            DB::commit();

            return response()->json(['message' => 'Actualización exitosa'], 200);
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json(['message' => 'Error en la actualización', 'error' => $e->getMessage()], 500);
        }
    }

    function setCriterioValidacion(Request $request)
    {   
        try {
            $id = $request->input('id');
            \Log::info($request);
            if ($id) {
                $telemetriaCriteriosValidacion = TelemetriaCriteriosValidacion::find($id);
                if (!$telemetriaCriteriosValidacion) {
                    return response()->json(['message' => 'Registro no encontrado'], 404);
                }
                \Log::info($telemetriaCriteriosValidacion);
            } else {
                $telemetriaCriteriosValidacion = new TelemetriaCriteriosValidacion();
            }
            $telemetriaCriteriosValidacion->empresa_id = 947;
            $telemetriaCriteriosValidacion->tipo_criterio = $request->input('tipo_criterio');
            $telemetriaCriteriosValidacion->tipo_estado = $request->input('tipo_estado');
            $telemetriaCriteriosValidacion->descripcion = $request->input('descripcion');
            $telemetriaCriteriosValidacion->variables = $request->input('variables');
            $telemetriaCriteriosValidacion->criterio = $request->input('criterio');
            $telemetriaCriteriosValidacion->aplicacion = $request->input('aplicacion');

            \Log::info($telemetriaCriteriosValidacion);
        
            $telemetriaCriteriosValidacion->save();
            return response()->json(['message' => 'Creación Correcta'], 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['message' => 'Error en la creación', 'error' => $e->getMessage()], 500);
        }
    }

    function setParametroTelemetria(Request $request)
    {
        try {
            $attributes = [
                'nombre_parametro' => $request->input('nombre_parametro'),
                'id_tipo_parametro' => $request->input('id_tipo_parametro')
            ];
            $telemetriaParametro = TelemetriaParametro::firstOrCreate($attributes);
            return response()->json(['message' => 'Operación exitosa', 'id' => $telemetriaParametro->id], 200);

        } catch(\Exception $e) {
            report($e);
            return response()->json(['message' => 'Error en la operación', 'error' => $e->getMessage()], 500);
        }
    }

    function setClearDataProcesada(Request $request){
        DB::table('telemetria_data_procesadas')->truncate();
    }

    function setDataProcesadaTelemetria(Request $request) {
        set_time_limit(2400);
        $resultados = $request->all();
        DB::beginTransaction();
        try {
            DB::table('telemetria_data_procesadas')->insert($resultados);

            DB::commit();

            return response()->json(['message' => 'Actualización exitosa'], 200);
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json(['message' => 'Error en la actualización', 'error' => $e->getMessage()], 500);
        }
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
