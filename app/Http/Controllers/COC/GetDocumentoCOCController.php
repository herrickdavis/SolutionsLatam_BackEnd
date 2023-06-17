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
        $excelFile = storage_path('app/public/Book3.xlsx');
        $pdfFile = storage_path('app/public/out.pdf');

        $spreadsheet = IOFactory::load($excelFile);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Pdf');
    $writer->save($pdfFile);

    return response()->download($pdfFile);
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
            ->select('c.*','lp.parametro as parametro_laboratorio', 'ip.parametro as parametro_in_situ', 'ip.unidad', 'ip.valor')
            ->leftJoin('cadena_laboratorio_parametros as lp', 'c.id', '=', 'lp.id_cadena')
            ->leftJoin('cadena_in_situ_parametros as ip', 'c.id', '=', 'ip.id_cadena')
            ->whereIn('c.id', $id_cadena)
            ->get();
        $cadenas = [];

        foreach($db_cadenas as $cadena) {
            $cadenas[$cadena->id]['CLIENTE'] = $cadena->id;
            $cadenas[$cadena->id]['CLIENTE'] = $cadena->cliente;
            $cadenas[$cadena->id]['CONTACTO'] = $cadena->contacto;
            $cadenas[$cadena->id]['CORREO'] = $cadena->correo;
            $cadenas[$cadena->id]['LUGAR_PROCEDENCIA'] = $cadena->lugar_procedencia;
            $cadenas[$cadena->id]['PROYECTO'] = $cadena->proyecto;
            $cadenas[$cadena->id]['PERIODICO'] = $cadena->periodico;
            $cadenas[$cadena->id]['ESTACION'] = $cadena->estacion;
            $cadenas[$cadena->id]['FECHA_INICIO'] = $cadena->fecha_inicio;
            $cadenas[$cadena->id]['HORA_INICIO'] = $cadena->hora_inicio;
            $cadenas[$cadena->id]['FECHA_FIN'] = $cadena->fecha_fin;
            $cadenas[$cadena->id]['HORA_FIN'] = $cadena->hora_fin;
            $cadenas[$cadena->id]['CODIGO_LABORATORIO'] = $cadena->codigo_laboratorio;
            $cadenas[$cadena->id]['TIPO_MUESTRA'] = $cadena->tipo_muestra;
            $cadenas[$cadena->id]['COORDENADA_NORTE'] = $cadena->coordenada_norte;
            $cadenas[$cadena->id]['COORDENADA_ESTE'] = $cadena->coordenada_este;
            $cadenas[$cadena->id]['ZONA'] = $cadena->zona;
            $pcadenasre_cadena[$cadena->id]['CANTIDAD_FRASCOS'] = $cadena->cantidad_frascos;
            $cadenas[$cadena->id]['OBSERVACIONES'] = $cadena->observaciones;
            $cadenas[$cadena->id]['NUMERO_GRUPO'] = $cadena->numero_grupo;
            $cadenas[$cadena->id]['NUMERO_PROCESO'] = $cadena->numero_proceso;
            $cadenas[$cadena->id]['NUMERO_ORDEN_SERVICIO'] = $cadena->numero_orden_servicio;
            $cadenas[$cadena->id]['PLAN_MUESTREO'] = $cadena->plan_muestreo;
            $cadenas[$cadena->id]['EQUIPOS_EMPLEADOS'] = $cadena->equipos_empleados;
            $cadenas[$cadena->id]['FIRMA_RESPONSABLE_MUESTREO'] = $cadena->firma_responsable_muestreo;
            $cadenas[$cadena->id]['NOMBRE_RESPONSABLE_MUESTREO'] = $cadena->nombre_responsable_muestreo;
            $cadenas[$cadena->id]['FECHA_RESPONSABLE_MUESTREO'] = $cadena->fecha_responsable_muestreo;
            $cadenas[$cadena->id]['FIRMA_RESPONSABLE_TRANSPORTE'] = $cadena->firma_responsable_transporte;
            $cadenas[$cadena->id]['NOMBRE_RESPONSABLE_TRANSPORTE'] = $cadena->nombre_responsable_transporte;
            $cadenas[$cadena->id]['FIRMA_RECEPCION_MUESTRA'] = $cadena->firma_recepcion_muestra;
            $cadenas[$cadena->id]['NOMBRE_RECEPCION_MUESTRA'] = $cadena->nombre_recepcion_muestra;
            $cadenas[$cadena->id]['FECHA_RECEPCION_MUESTRA'] = $cadena->fecha_recepcion_muestra;
            if(!array_key_exists('METODOS_LABORATORIO', $cadenas[$cadena->id])) {
                $cadenas[$cadena->id]['METODOS_LABORATORIO'] = [];
            }
            if(!in_array($cadena->parametro_laboratorio, $cadenas[$cadena->id]['METODOS_LABORATORIO'])) {
                $cadenas[$cadena->id]['METODOS_LABORATORIO'][] = $cadena->parametro_laboratorio;
            }
            $cadenas[$cadena->id]['PARAMETROS_IN_SITU'][$cadena->parametro_in_situ]['VALOR'] = $cadena->valor;
            $cadenas[$cadena->id]['PARAMETROS_IN_SITU'][$cadena->parametro_in_situ]['UNIDAD'] = $cadena->unidad;
            //le agrego los parametros in situ como dato
            $cadenas[$cadena->id][str_replace(" ","_",mb_strtoupper($cadena->parametro_in_situ, 'UTF-8'))] = $cadena->valor;
        }
        //obtengo todos los parametros de laboratorio
        $db_parametros_laboratorio = DB::table("cadena_laboratorio_parametros as lp")
            ->select('lp.parametro')
            ->leftJoin('cadenas as c', 'c.id', '=', 'lp.id_cadena')
            ->whereIn('lp.id_cadena', $id_cadena)
            ->distinct()
            ->orderBy('parametro', 'asc')
            ->get();
        $parametros_laboratorio = [];
        foreach($db_parametros_laboratorio as $parametro_laboratorio) {
            array_push($parametros_laboratorio, $parametro_laboratorio->parametro);
        }
        //dd($parametros_laboratorio);
        //obtengo todos los parametros de laboratorio
        $db_parametros_in_situ = DB::table("cadena_in_situ_parametros as ip")
            ->select('ip.parametro', 'ip.valor', 'ip.unidad')
            ->leftJoin('cadenas as c', 'c.id', '=', 'ip.id_cadena')
            ->whereIn('ip.id_cadena', $id_cadena)
            ->orderBy('parametro', 'asc')
            ->get();
        $parametros_in_situ = [];
        foreach($db_parametros_in_situ as $parametro_in_situ) {
            array_push($parametros_in_situ, $parametro_in_situ->parametro);
        }
        $parametros_in_situ = array_values(array_unique($parametros_in_situ));
        $cadenas = array_values($cadenas);
        $export = new CadenaCustodiaExport();
        $export->setData($cadenas, $parametros_laboratorio, $parametros_in_situ);
        
        return $export->export();
        //
        //return Excel::download($export, 'COC.xlsx');
        //return $users;
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
