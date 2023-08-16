<?php

namespace App\Http\Controllers\COC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CadenaCustodiaExport;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Html;

class GetDocumentoCOCController extends Controller
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
        //Leemos Datos
        $id_cadena = $request->id_muestras;
        //Terminamos de leer datos
        $db_cadenas = DB::table("cadenas as c")
            ->select('c.*')            
            ->whereIn('c.codigo_laboratorio', $id_cadena)
            ->get();
        $cadenas = [];

        $all_parametros_laboratorio = [];
        $all_parametros_insitu = [];
        foreach($db_cadenas as $cadena) {
            /*$cadenas[$cadena->codigo_laboratorio]['CODIGO'] = $cadena->codigo_laboratorio;
            $cadenas[$cadena->codigo_laboratorio]['NUMERO_GRUPO'] = $cadena->numero_grupo;
            $cadenas[$cadena->codigo_laboratorio]['NUMERO_PROCESO'] = $cadena->numero_proceso;
            $cadenas[$cadena->codigo_laboratorio]['NUMERO_ORDEN_SERVICIO'] = $cadena->numero_orden_servicio;
            $cadenas[$cadena->codigo_laboratorio]['ESTACION'] = $cadena->estacion;
            $cadenas[$cadena->codigo_laboratorio]['FECHA_MUESTREO'] = $cadena->fecha_muestreo;
            $cadenas[$cadena->codigo_laboratorio]['TIPO_MUESTRA'] = $cadena->tipo_muestra;
            $cadenas[$cadena->codigo_laboratorio]['INFO_ADICIONAL'] = $cadena->informacion_adicional;*/
            $info_adicional = json_decode($cadena->informacion_adicional);
            foreach ($info_adicional as $key => $value) {                
                $cadenas[$cadena->codigo_laboratorio][strtoupper($key)] = $value;
            }

            foreach($info_adicional->parametros_laboratorio as $param_laboratory) {
                foreach ($param_laboratory as $key => $value) {
                    if($value != null) {
                        $all_parametros_laboratorio[strtoupper($key)][] = $value;
                    }
                }
            }
            foreach($info_adicional->parametros_insitu as $param_laboratory) {
                foreach ($param_laboratory as $key => $value) {
                    if($value != null) {
                        $all_parametros_insitu[strtoupper($key)][] = $value;
                    }                    
                }
            }
        }
        #Quito duplicados
        foreach ($all_parametros_laboratorio as $key => $value) {
            $parametros_unicos = array_unique($value);
            sort($parametros_unicos);
            $all_parametros_laboratorio[$key] = $parametros_unicos;
        }
        foreach ($all_parametros_insitu as $key => $value) {
            $parametros_unicos = array_unique($value);
            sort($parametros_unicos);
            $all_parametros_insitu[$key] = $parametros_unicos;
        }
        $export = new CadenaCustodiaExport();
        $export->setData($cadenas, $all_parametros_laboratorio, $all_parametros_insitu);
        
        return $export->export();
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
