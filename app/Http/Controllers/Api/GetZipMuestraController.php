<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ClickBotones;
use Illuminate\Support\Facades\Storage;
use Throwable;
use ZipArchive;
use File;

class GetZipMuestraController extends Controller
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
        $usuario = $request->user();
        if($usuario->descargar_documento == 'N') {
            return response()->json(['error' => 'No tienes permisos para realizar esta acción.'], 403);
        }
        $id_muestra = $request->id_muestra;
        $tipo_archivo = $request->id_tipo_archivo;
        $analytic_click = new ClickBotones;
        $analytic_click->id_user = $usuario->id;
        $analytic_click->id_boton = 18;
        $analytic_click->save();

        try {
            $sql_grupo = DB::table('muestras as m')
                                ->select(DB::raw(
                                    "dg.id as id,
                                    dg.nombre_documento as nombre_documento,
                                    dg.ruta,
                                    dg.extension
                                    "
                                ))
                                ->leftjoin(
                                    DB::raw(
                                        '(SELECT id_muestra, MIN(id_grupo_muestra) id_grupo_muestra FROM muestra_grupo_muestras GROUP BY id_muestra) AS `mgm`'
                                    ),
                                    function ($join) {
                                        $join->on('mgm.id_muestra', '=', 'm.id');
                                    }
                                )
                                ->join('documentos_grupos as dg', 'dg.id_grupo_muestras', '=', 'mgm.id_grupo_muestra')
                                ->whereIn('m.id', $id_muestra);

            
            if ($tipo_archivo != null) {
                if (count($tipo_archivo) != 0 or $tipo_archivo != null) {
                    $sql_grupo = $sql_grupo->whereIn('dg.id_tipo_documento', $tipo_archivo);
                }
            }

            //->orderBy('c.orden')
            //->get();
            $sql_muestra = DB::table('muestras as m')
                                ->select(DB::raw(
                                    "dm.id as id,
                                    dm.nombre_documento as nombre_documento,
                                    dm.ruta,
                                    dm.extension
                                    "
                                ))
                                ->join('documentos_muestras as dm', 'dm.id_muestra', '=', 'm.id')
                                ->whereIn('m.id', $id_muestra);

            if ($tipo_archivo != null) {
                if (count($tipo_archivo) != 0 or $tipo_archivo != null) {
                    $sql_muestra = $sql_muestra->whereIn('dm.id_tipo_documento', $tipo_archivo);
                }
            }
            
            //->orderBy('c.orden')
            //->get();
            $sql_documentos = DB::table('muestras as m')
                                ->select(DB::raw(
                                    "c.id as id,
                                    c.nombre_documento as nombre_documento,
                                    c.ruta,
                                    c.extension
                                    "
                                ))
                                ->join('certificados as c', 'c.id_certificado', '=', 'm.id_certificado')
                                ->whereIn('m.id', $id_muestra)
                                ->union($sql_muestra)
                                ->union($sql_grupo);
            //->orderBy('c.orden')
            //->get();
            if ($tipo_archivo != null) {
                if (count($tipo_archivo) != 0 or $tipo_archivo != null) {
                    $sql_documentos = $sql_documentos->whereIn('c.id_tipo_documento', $tipo_archivo);
                }
            }


            $sql_documentos = $sql_documentos->get();

            $zip = new ZipArchive();
            if (count($id_muestra) == 1) {
                $fileName = $usuario->id.'_'.$id_muestra[0].'.zip';
            } else {
                $fileName = $usuario->id.'_muestras.zip';
            }
            
            //dd($sql_documentos);
            if (file_exists(storage_path('app/public/'.$fileName))) {
                unlink(storage_path('app/public/'.$fileName));
            }
            
            if ($zip->open(storage_path('app/public/'.$fileName), ZipArchive::CREATE)== true) {
                //$files = File::files(storage_path('app/public'));
                foreach ($sql_documentos as $documento) {
                    //$relativeName = basename($documento->ruta);
                    $relativeName = $documento->nombre_documento;
                    $fileContent = Storage::disk('s3')->get($documento->ruta);
                    $zip->addFromString($relativeName, $fileContent);
                }
                if (count($sql_documentos) == 0) {
                    $zip->addEmptyDir("vacio");
                }
                $zip->close();
            }
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        
        return response()->download(storage_path('app/public/'.$fileName), trans('texto.documentos_als'));
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
