<?php

namespace App\Http\Controllers\COC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\CadenaCustodiaExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        //Obtenemos el documento
        $id_documento = $request->id_plantilla;

        //Consultar la base de datos para obtener el blob
        $archivo = DB::table('cadena_plantillas')->where('id', $id_documento)->first();

        //Escribir el blob a un archivo
        $nombreArchivo = 'public/excel_' . $id_documento . '.'. $archivo->extension;
        Storage::disk('local')->put($nombreArchivo, $archivo->plantilla); 

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
            $info_adicional = json_decode($cadena->informacion_adicional);
            foreach ($info_adicional as $key => $value) {                
                $cadenas[$cadena->codigo_laboratorio][strtoupper($key)] = $value;
            }

            foreach($info_adicional->parametros_laboratorio as $param_laboratory) {
                foreach ($param_laboratory as $key => $value) {
                    if(($value != null) && $value != "None") {
                        $all_parametros_laboratorio[strtoupper($key)][] = $value;
                    }
                }
            }
            foreach($info_adicional->parametros_insitu as $param_laboratory) {
                foreach ($param_laboratory as $key => $value) {
                    if(($value != null) && $value != "None") {
                        $all_parametros_insitu[strtoupper($key)][] = $value;
                    }                    
                }
            }
        }
        $cadenas = array_values($cadenas);
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
        $export->setData($cadenas, $all_parametros_laboratorio, $all_parametros_insitu, $nombreArchivo);
        
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
