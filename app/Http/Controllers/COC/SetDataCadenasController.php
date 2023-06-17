<?php

namespace App\Http\Controllers\COC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cadenas;
use App\Models\CadenaLaboratorioParametros;
use App\Models\CadenaInSituParametros;
use Throwable;

class SetDataCadenasController extends Controller
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
            $cliente = $request->cliente;
            $contacto = $request->contacto;
            $correo = $request->correo;
            $lugar_procedencia = $request->lugar_procedencia;
            $proyecto = $request->proyecto;
            $periodico = $request->periodico;
            $numero_grupo = $request->numero_grupo;
            $numero_proceso = $request->numero_proceso;
            $numero_orden_servicio = $request->numero_orden_servicio;
            $plan_muestreo = $request->plan_muestreo;
            $estacion = $request->estacion;
            $fecha_inicio = $request->fecha_inicio;
            $hora_inicio = $request->hora_inicio;
            $fecha_fin = $request->fecha_fin;
            $hora_fin = $request->hora_fin;
            $codigo_laboratorio = $request->id_muestra;
            $tipo_muestra = $request->tipo_muestra;
            $coordenada_norte = $request->coordenada_norte;
            $coordenada_este = $request->coordenada_este;
            $zona = $request->zona;
            $altura = $request->altura;
            $cantidad_frascos = $request->cantidad_frascos;
            $observaciones = $request->observaciones;
            $equipos_empleados = $request->equipos_empleados;
            $firma_responsable_muestreo = $request->firma_responsable_muestreo;
            $nombre_responsable_muestreo = $request->nombre_responsable_muestreo;
            $fecha_responsable_muestreo = $request->fecha_responsable_muestreo;
            $firma_responsable_transporte = $request->firma_responsable_transporte;
            $nombre_responsable_transporte = $request->nombre_responsable_transporte;
            $fecha_responsable_transporte = $request->fecha_responsable_transporte;
            $firma_recepcion_muestra = $request->firma_recepcion_muestra;
            $nombre_recepcion_muestra = $request->nombre_recepcion_muestra;
            $fecha_recepcion_muestra = $request->fecha_recepcion_muestra;

            //parametros
            $parametros_laboratorio = $request->parametros_laboratorio;
            $parametros_in_situ = $request->parametros_in_situ;

            //Insert
            $cadena = new Cadenas;
            $cadena->cliente = $cliente;
            $cadena->contacto = $contacto;
            $cadena->correo = $correo;
            $cadena->lugar_procedencia = $lugar_procedencia;
            $cadena->proyecto = $proyecto;
            $cadena->periodico = $periodico;
            $cadena->numero_grupo = $numero_grupo;
            $cadena->numero_proceso = $numero_proceso;
            $cadena->numero_orden_servicio = $numero_orden_servicio;
            $cadena->plan_muestreo = $plan_muestreo;
            $cadena->estacion = $estacion;
            $cadena->fecha_inicio = $fecha_inicio;
            $cadena->hora_inicio = $hora_inicio;
            $cadena->fecha_fin = $fecha_fin;
            $cadena->hora_fin = $hora_fin;
            $cadena->codigo_laboratorio = $codigo_laboratorio;
            $cadena->tipo_muestra = $tipo_muestra;
            $cadena->coordenada_norte = $coordenada_norte;
            $cadena->coordenada_este = $coordenada_este;
            $cadena->zona = $zona;
            $cadena->altura = $altura;
            $cadena->cantidad_frascos = $cantidad_frascos;
            $cadena->observaciones = $observaciones;
            $cadena->equipos_empleados = $equipos_empleados;
            $cadena->firma_responsable_muestreo = $firma_responsable_muestreo;
            $cadena->nombre_responsable_muestreo = $nombre_responsable_muestreo;
            $cadena->fecha_responsable_muestreo = $fecha_responsable_muestreo;
            $cadena->firma_responsable_transporte = $firma_responsable_transporte;
            $cadena->nombre_responsable_transporte = $nombre_responsable_transporte;
            $cadena->fecha_responsable_transporte = $fecha_responsable_transporte;
            $cadena->firma_recepcion_muestra = $firma_recepcion_muestra;
            $cadena->nombre_recepcion_muestra = $nombre_recepcion_muestra;
            $cadena->fecha_recepcion_muestra = $fecha_recepcion_muestra;

            $cadena->save();

            $cadenaId = $cadena->id;

            foreach ($parametros_laboratorio as $parametro_laboratorio) {
                $nuevoParametro = new CadenaLaboratorioParametros;
                $nuevoParametro->id_cadena = $cadenaId;
                $nuevoParametro->parametro = $parametro_laboratorio['parametro'];
                $nuevoParametro->save();
            }

            foreach ($parametros_in_situ as $parametro_in_situ) {
                $nuevoParametro = new CadenaInSituParametros;
                $nuevoParametro->id_cadena = $cadenaId;
                $nuevoParametro->parametro = $parametro_in_situ['parametro'];
                $nuevoParametro->valor = $parametro_in_situ['valor'];
                $nuevoParametro->unidad = $parametro_in_situ['unidad'];
                $nuevoParametro->save();
            }

            $rpta["estado"] = "OK";
            $rpta["mensaje"] = "Insercion Correcta";

        } catch (Throwable $e) {
            report($e);
            $rpta["estado"] = "ERROR";
            $rpta["mensaje"] = $e->getMessage();
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
