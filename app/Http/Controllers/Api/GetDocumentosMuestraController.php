<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ClickBotones;
use Throwable;

class GetDocumentosMuestraController extends Controller
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
        try {
            $usuario = $request->user();
            if($usuario->descargar_documento == 'N') {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción.'], 403);
            }
            $idauxempresa = $usuario->id_empresa;
            $id_muestra = $request->id_muestra;
            $informe = trans('texto.Informe');
            $informe_adicional = trans('texto.Informe_adicional');
            $informe_muestreo = trans('texto.Informe_muestreo');
            $coc = trans('texto.COC');
            $anexo = trans('texto.Anexo');

            $analytic_click = new ClickBotones;
            $analytic_click->id_user = $usuario->id;
            $analytic_click->id_boton = 16;
            $analytic_click->save();

            $sql_grupo = DB::table('muestras as m')
                                ->select(DB::raw(
                                    "CONCAT('G',dg.id) as id,
                                    CASE 
                                    WHEN dg.id_tipo_documento = 2 THEN CONCAT(dg.nombre_documento,' ','(".$informe.")')
                                    WHEN dg.id_tipo_documento = 102 THEN CONCAT(dg.nombre_documento,' ','(".$coc.")')
                                    WHEN dg.id_tipo_documento = 128 THEN CONCAT(dg.nombre_documento,' ','(".$anexo.")')
                                    ELSE CONCAT(dg.nombre_documento,' ','(Otro)')  END as nombre_documento,
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
                                ->leftjoin('grupo_muestras AS gm', 'gm.id', '=', 'mgm.id_grupo_muestra')
                                ->join('documentos_grupos as dg', 'dg.id_grupo_muestras', '=', 'mgm.id_grupo_muestra')
                                ->where('dg.activo','=','S')
                                ->where('m.id', '=', $id_muestra);
            //->orderBy('c.orden')
            //->get();
            $sql_muestra = DB::table('muestras as m')
                                ->select(DB::raw(
                                    "CONCAT('M',dm.id) as id,
                                    CASE 
                                    WHEN dm.id_tipo_documento = 2 THEN CONCAT(dm.nombre_documento,' ','(".$informe.")')
                                    WHEN dm.id_tipo_documento = 102 THEN CONCAT(dm.nombre_documento,' ','(".$coc.")')
                                    WHEN dm.id_tipo_documento = 128 THEN CONCAT(dm.nombre_documento,' ','(".$anexo.")') 
                                    ELSE CONCAT(dm.nombre_documento,' ','(Otro)')  END as nombre_documento,
                                    dm.extension
                                    "
                                ))
                                ->join('documentos_muestras as dm', 'dm.id_muestra', '=', 'm.id')
                                ->where('dm.activo','=','S')
                                ->where('m.id', '=', $id_muestra);
            //->orderBy('c.orden')
            //->get();
            $sql_documentos = DB::table('muestras as m')
                                ->select(DB::raw(
                                    "CONCAT('I',c.id) as id,
                                    CASE 
                                    WHEN c.id_tipo_documento = 2 THEN CONCAT(c.nombre_documento,' ','(".$informe.")')
                                    WHEN c.id_tipo_documento = 102 THEN CONCAT(c.nombre_documento,' ','(".$coc.")')
                                    WHEN c.id_tipo_documento = 128 THEN CONCAT(c.nombre_documento,' ','(".$anexo.")')
                                    WHEN c.id_tipo_documento = 138 THEN CONCAT(c.nombre_documento,' ','(".$informe_adicional.")')
                                    WHEN c.id_tipo_documento = 139 THEN CONCAT(c.nombre_documento,' ','(".$informe_muestreo.")')
                                    ELSE CONCAT(c.nombre_documento,' ','(Otro)')  END as nombre_documento,
                                    c.extension
                                    "
                                ))
                                ->join('certificados as c', 'c.id_certificado', '=', 'm.id_certificado')
                                ->where('c.activo','=','S')
                                ->where('m.id', '=', $id_muestra)
                                ->union($sql_muestra)
                                ->union($sql_grupo)
                                //->orderBy('c.orden')
                                ->get();

            /*$sql_documentos = DB::table('muestras as m')
                                ->select(DB::raw(
                                    "dm.id as id,
                                    dm.nombre_documento as nombre_documento,
                                    dm.extension
                                    "
                                ))
                                ->leftjoin('documentos_muestras as dm', 'dm.id_certificado', '=', 'm.id_certificado')
                                ->where('m.id', '=', $id_muestra)
                                ->orderBy('dm.orden')
                                ->where('dm.id', '<>', null)
                                ->get();*/
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        
        /*$rpta = [];
        $pre_rpta['id'] = 1;
        $pre_rpta['nombre_documento'] = "Documento1.pdf";
        $pre_rpta['extension'] = "pdf";
        array_push($rpta, $pre_rpta);
        $pre_rpta['id'] = 2;
        $pre_rpta['nombre_documento'] = "imagen1.jpg";
        $pre_rpta['extension'] = "jpg";
        array_push($rpta, $pre_rpta);
        $pre_rpta['id'] = 3;
        $pre_rpta['nombre_documento'] = "excel1.xlsx";
        $pre_rpta['extension'] = "xlsx";
        array_push($rpta, $pre_rpta);*/


        return $sql_documentos;
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
