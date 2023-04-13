<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocumentosMuestra;
use App\Models\DocumentosGrupo;
use App\Models\Certificados;
use App\Models\Muestras;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

//use Illuminate\Http\UploadedFile;

class SetDocumentosMuestraController extends Controller
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
        $id_muestra = $request->id_muestra;
        $id_grupo = $request->id_grupo;
        $tipo = $request->tipo;
        $id_certificado = $request->id_certificado;
        $id_tipo_documento = $request->id_tipo_documento;
        $nombre_archivo = $request->nombre_archivo;
        $activo = $request->activo;
        $orden = $request->orden;
        $titulo_certificado = $request->titulo_certificado;
        $extension = $request->extension;
        $ruta = $request->ruta;
        //dd($request->json()->all());
        try {
            //$documento = $request->file('file');
            //$extension = $documento->getClientOriginalExtension();
            $nombre = $nombre_archivo;
            /*if ($nombre_archivo != null) {
                $nombre = $nombre_archivo;
            } else {
                $nombre = $documento->getClientOriginalName();
            }*/

            if ($tipo == "1") {
                //\Storage::disk('public')->put($tipo.'-'.$id_muestra.'-'.$orden.".".$extension, \File::get($documento));
                //$public_path = storage_path('app/public/'.$tipo.'-'.$id_muestra.'-'.$orden.".".$extension);
                //$public_path = Storage::putFile('/public', request()->file('file'));
                //$public_path = storage_path('app/'.$public_path);
                //$ruta = $public_path;

                $sql_muestras = Muestras::where('id', $id_muestra)
                                ->update(
                                    [
                                        //'id_certificado' => $id_certificado,
                                        'con_documentos' => 'S'
                                    ]
                                );
                    
                if ($id_certificado == null) {
                    $sql_documentos = DocumentosMuestra::updateOrCreate(
                        ['id_muestra' => $id_muestra, 'nombre_documento' => $nombre, 'id_tipo_documento' => $id_tipo_documento],
                        [
                                'ruta' => $ruta,
                                'extension' => $extension,
                                'activo' => $activo,
                                'orden' => $orden
                            ]
                    );
                } else {
                    $sql_documentos = DocumentosMuestra::updateOrCreate(
                        ['id_documento' => $id_certificado],
                        [
                            'id_muestra' => $id_muestra,
                            'nombre_documento' => $nombre,
                            'ruta' => $ruta,
                            'extension' => $extension,
                            'id_tipo_documento' => $id_tipo_documento,
                            'activo' => $activo,
                            'orden' => $orden
                        ]
                    );
                }
            } elseif ($tipo == "2") {
                //\Storage::disk('public')->put($tipo.'-'.$id_grupo.'-'.$orden.".".$extension, \File::get($documento));
                //$public_path = storage_path('app/public/'.$tipo.'-'.$id_grupo.'-'.$orden.".".$extension);
                //$public_path = Storage::putFile('/public', request()->file('file'));
                //$ruta = $public_path;
                $sql_id_muestras = DB::table('muestras AS m')
                                    ->select(DB::raw(
                                        "m.id as id"
                                    ))
                                    ->leftjoin('muestra_grupo_muestras AS mgm', 'mgm.id_muestra', '=', 'm.id')
                                    ->where('mgm.id_grupo_muestra', '=', $id_grupo)
                                    ->get();
                $id_muestras = [];
                foreach ($sql_id_muestras as $valor) {
                    array_push($id_muestras, $valor->id);
                };
                    
                $sql_muestras = Muestras::whereIn('id', $id_muestras)
                                ->update(
                                    [
                                        'con_documentos' => 'S'
                                    ]
                                );
                    
                if ($id_certificado == null) {
                    $sql_documentos = DocumentosGrupo::updateOrCreate(
                        ['id_grupo_muestras' => $id_grupo, 'nombre_documento' => $nombre, 'id_tipo_documento' => $id_tipo_documento],
                        [
                                'ruta' => $ruta,
                                'extension' => $extension,
                                'activo' => $activo,
                                'orden' => $orden
                            ]
                    );
                } else {
                    $sql_documentos = DocumentosGrupo::updateOrCreate(
                        ['id_documento' => $id_certificado],
                        [
                                'id_grupo_muestras' => $id_grupo,
                                'nombre_documento' => $nombre,
                                'ruta' => $ruta,
                                'extension' => $extension,
                                'id_tipo_documento' => $id_tipo_documento,
                                'activo' => $activo,
                                'orden' => $orden
                            ]
                    );
                }
            } elseif ($tipo == "I") {
                if (strpos($id_certificado, "G") !== false) {
                    $sql_id_certificado = DB::table('muestras AS m')
                                            ->select(DB::raw(
                                                "CONCAT(gm.numero_grupo,'/',gm.anho_grupo) AS numero_grupo,
                                                m.id as id
                                                "
                                            ))
                                            ->leftjoin('muestra_grupo_muestras AS mgm','mgm.id_muestra','=','m.id')
                                            ->leftjoin('grupo_muestras AS gm', 'gm.id', '=', 'mgm.id_grupo_muestra')
                                            ->where('gm.id', '=', str_replace("G", "", $id_certificado))
                                            ->distinct()
                                            ->get();
                    $id_muestras = [];
                    foreach ($sql_id_certificado as $valor) {
                        array_push($id_muestras, $valor->id);
                        $identificacion_certificado = $valor->numero_grupo;
                    }
                } else {
                    $id_muestras = [];
                    array_push($id_muestras, str_replace("M", "", $id_certificado));
                    $identificacion_certificado = str_replace("M", "", $id_certificado);
                }
                
                if (strval($id_tipo_documento) == "2") {
                    $sql_muestras = Muestras::whereIn('id', $id_muestras)
                                ->update(
                                    [
                                        'id_certificado' => $id_certificado,
                                        'con_documentos' => 'S',
                                        'id_estado' => 4
                                    ]
                                );
                } else {
                    $sql_muestras = Muestras::whereIn('id', $id_muestras)
                                ->update(
                                    [
                                        'con_documentos' => 'S',
                                        'id_estado' => 4
                                    ]
                                );
                }

                if (strval($id_tipo_documento) == "2") {
                    $sql_documentos = Certificados::updateOrCreate(
                        [
                            'id_certificado' => $id_certificado,
                            'id_tipo_documento' => $id_tipo_documento,
                        ],
                        [
                                'nombre_documento' => $nombre,
                                'ruta' => $ruta,
                                'extension' => $extension,
                                'identificacion_certificado' => $identificacion_certificado,
                                'titulo_certificado' => $titulo_certificado,
                                'activo' => $activo
                            ]
                    );
                } else {
                    $sql_documentos = Certificados::updateOrCreate(
                        [
                            'id_certificado' => $id_certificado,
                            'id_tipo_documento' => $id_tipo_documento,
                            'nombre_documento' => $nombre
                        ],
                        [                                
                                'ruta' => $ruta,
                                'extension' => $extension,
                                'identificacion_certificado' => $identificacion_certificado,
                                'titulo_certificado' => $titulo_certificado,
                                'activo' => $activo
                            ]
                    );
                }
            }
            
            $rpta["success"] = "Ok";
            $rpta["mensaje"] = "Ok";
        } catch (\Throwable $e) {
            report($e);
            $rpta["error"] = "error";
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
