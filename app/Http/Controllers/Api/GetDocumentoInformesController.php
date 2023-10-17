<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
                $documento = $sql_documentos->first();
                $contents = Storage::disk('s3')->get($documento->ruta);
                $fileName = $documento->nombre_documento;
                $tempPath = storage_path('app/public/' . $fileName);
                file_put_contents($tempPath, $contents);
                return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
            } else {
                $zip = new ZipArchive();
                $zipFileName = $usuario->id . '_Informes_ALS.zip';
                $tempZipPath = storage_path('app/public/' . $zipFileName);

                if ($zip->open($tempZipPath, ZipArchive::CREATE) == true) {
                    foreach ($sql_documentos as $documento) {
                        $contents = Storage::disk('s3')->get($documento->ruta);
                        $zip->addFromString($documento->nombre_documento, $contents);
                    }
                    $zip->close();
                }

                return response()->download($tempZipPath, $zipFileName)->deleteFileAfterSend(true);
            }
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
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
}
