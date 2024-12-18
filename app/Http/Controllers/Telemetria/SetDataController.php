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
        ini_set('memory_limit', '1024M');
        set_time_limit(2400);
        $tamañoDelChunk = 30000;
        $hubo_error = false;
        DB::beginTransaction(); // Iniciar una transacción
        try {
            foreach (array_chunk($request->all(), $tamañoDelChunk) as $chunk) {
                TelemetriaResultado::insertOrIgnore($chunk);
            }
            DB::commit(); // Confirmar la transacción si todo salió bien            
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);
            $hubo_error = true;
            $mensaje = $e->getMessage();
        }
        if ($hubo_error) {
            // Devolver una respuesta con el mensaje de error
            return response()->json([
                'error' => 'error',
                'mensaje' => $mensaje
            ], 500); // Devolver un código de estado 500 (Internal Server Error)
        } else {
            // Devolver una respuesta de éxito
            return response()->json([
                'success' => 'success',
                'mensaje' => 'Ok'
            ], 200); // Devolver un código de estado 200 (OK)
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
                $limite_inferior = $parametro['limite_inferior'] ?? '';
                $limite_superior = $parametro['limite_superior'] ?? '';

                $limite_parametros = TelemetriaLimiteParametro::updateOrCreate(
                    [
                        'limite_id' => $limite->id,
                        'parametro_id' => $parametro['id_parametro'],
                    ],
                    [
                        'limite_inferior' => $limite_inferior,
                        'limite_superior' => $limite_superior
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

    public function updateEstadoNullResultado(Request $request)
    {
        set_time_limit(2400);
        DB::beginTransaction();

        // Fecha actual menos 2 días
        $fecha_limite = Carbon::now()->subDays(2);

        try {
            DB::statement('
                UPDATE telemetria_resultados AS tr
                JOIN telemetria_muestras AS tm ON tr.muestra_id = tm.id
                SET tr.estado_id = 3
                WHERE (tr.estado_id IS NULL OR tr.estado_id = 1) 
                    AND tr.resultado IS NULL
                    AND tm.fecha_muestreo > ?
            ', [$fecha_limite]);

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
        $timestamp = now();
        foreach ($resultados as &$resultado) {
            $resultado['created_at'] = $timestamp;
            $resultado['updated_at'] = $timestamp;
        }
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
            if ($id) {
                $telemetriaCriteriosValidacion = TelemetriaCriteriosValidacion::find($id);
                if (!$telemetriaCriteriosValidacion) {
                    return response()->json(['message' => 'Registro no encontrado'], 404);
                }
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
            DB::table('telemetria_data_procesadas')->insertOrIgnore($resultados);

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
