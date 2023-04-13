<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Throwable;
use ZipArchive;
use File;

class GetDocumentoInformesController extends Controller
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
        $id_documento = $request->id_documento;
        try {
            $sql_documentos = DB::table('certificados as c')
                            ->select(DB::raw(
                                "c.id as id,
                                c.nombre_documento as nombre_documento,
                                c.ruta,
                                c.extension
                                "
                            ))
                            ->whereIn('c.id', $id_documento)
                            ->get();
            
            if (count($id_documento)==1) {
                foreach ($sql_documentos as $documento) {
                    //$relativeName = basename($documento->ruta);
                    $fileName = $documento->nombre_documento;
                    $ruta = $documento->ruta;
                }
            } else {
                $zip = new ZipArchive();
                $fileName = $usuario->id.'_Informes_ALS.zip';

                //dd($sql_documentos);
                if (file_exists(storage_path('app/public/'.$fileName))) {
                    unlink(storage_path('app/public/'.$fileName));
                }

                if ($zip->open(storage_path('app/public/'.$fileName), ZipArchive::CREATE)== true) {
                    $files = File::files(storage_path('app/public'));
                    foreach ($sql_documentos as $documento) {
                        //$relativeName = basename($documento->ruta);
                        $relativeName = $documento->nombre_documento;
                        $zip->addFile(storage_path('app/'.$documento->ruta), $relativeName);
                    }
                    $zip->close();
                    $ruta = "public/".$fileName;
                }
            }
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        
        return response()->download(storage_path('app/'.$ruta), trans('texto.informes_als'));
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
