<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\ClickBotones;
use Throwable;
use ZipArchive;
use File;

class GetDocumentoMuestraController extends Controller
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

        $analytic_click = new ClickBotones;
        $analytic_click->id_user = $usuario->id;
        $analytic_click->id_boton = 17;
        $analytic_click->save();

        try {
            $tipo = substr($id_documento, 0, 1);
            if ($tipo == "M") {
                $documento = substr($id_documento, 1);
                $sql_documento = DB::table('documentos_muestras as dm')
                                ->select(DB::raw(
                                    "dm.id as id,
                                    dm.nombre_documento as nombre_documento,
                                    dm.ruta,
                                    dm.extension
                                    "
                                ))
                                ->where('dm.id', '=', $documento)
                                ->first();
            } elseif ($tipo == "G") {
                $documento = substr($id_documento, 1);
                $sql_documento = DB::table('documentos_grupos as dg')
                                ->select(DB::raw(
                                    "dg.id as id,
                                    dg.nombre_documento as nombre_documento,
                                    dg.ruta,
                                    dg.extension
                                    "
                                ))
                                ->where('dg.id', '=', $documento)
                                ->first();
            } elseif ($tipo == "I") {
                $documento = substr($id_documento, 1);
                $sql_documento = DB::table('certificados as c')
                                ->select(DB::raw(
                                    "c.id as id,
                                    c.nombre_documento as nombre_documento,
                                    c.ruta,
                                    c.extension
                                    "
                                ))
                                ->where('c.id', '=', $documento)
                                ->first();
            }
            $fileName = $sql_documento->nombre_documento;
            $ruta = $sql_documento->ruta;
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        //return storage_path('app/'.$ruta);
        #return response()->download(storage_path('app/'.$ruta), $fileName);
        #return response()->download(Storage::disk('s3')->get('informes/117fe96d-db3f-411d-95ab-967ba25b4a8d.pdf'), $fileName);
        #return Storage::disk('s3')->get('informes/117fe96d-db3f-411d-95ab-967ba25b4a8d.pdf');
        #return Response::make()
        #$storage = Storage::disk('s3')->get($ruta);
        $storage = Storage::disk('s3')->get($ruta);
        $preFile = Storage::disk('local')->put($ruta,$storage);
        if ($preFile == 1) {
            return response()->download(storage_path('app/'.$ruta), $fileName);
        } else {
            return response()->json(['message' => "Fallo al descargar el File"], 400);
        }
        #return Response::make($storage, 200, $header);
        
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
