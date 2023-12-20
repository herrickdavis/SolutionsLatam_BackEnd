<?php

namespace App\Http\Controllers\DataExterna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Estaciones;
use App\Models\Proyectos;
use Throwable;

class MuestraExternaController extends Controller
{
    public function eliminarMuestra(Request $request, $id)
    {
        $usuario = $request->user();
        DB::table('data_externa_temporals')->where('id_user', $usuario->id)->where('id',$id)->delete();
        $rpta["success"] = "Ok";
        $rpta["mensaje"] = "Se elimino correctamente";

        return $rpta;
    }

    public function agregarEstacion(Request $request)
    {
        $usuario = $request->user();
        $registros = DB::table('data_externa_temporals')->select('estacion','id_empresa_solicitante','id_empresa_contratante')
                                                        ->where('id_user', $usuario->id)
                                                        ->whereNull('id_estacion')
                                                        ->whereNotNull('id_empresa_solicitante')
                                                        ->whereNotNull('id_empresa_contratante')
                                                        ->distinct()
                                                        ->get();
        
        DB::beginTransaction();
        try {
            foreach ($registros as $registro) {
                $estacion = new Estaciones();
                $estacion->id_empresa_sol = $registro->id_empresa_solicitante;
                $estacion->id_empresa_con = $registro->id_empresa_contratante;
                $estacion->nombre_estacion = $registro->estacion;
                $estacion->save();
            }
            DB::commit();
            $rpta = [
                "success" => "Ok",
                "mensaje" => "Se creó correctamente las estaciones"
            ];
            return response()->json($rpta);
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function agregarProyecto(Request $request)
    {   
        $usuario = $request->user();
        $registros = DB::table('data_externa_temporals')->select('proyecto','id_empresa_solicitante','id_empresa_contratante')
                                                        ->where('id_user', $usuario->id)
                                                        ->whereNull('id_proyecto')
                                                        ->whereNotNull('id_empresa_solicitante')
                                                        ->whereNotNull('id_empresa_contratante')
                                                        ->distinct()
                                                        ->get();
        DB::beginTransaction();
        try {
            foreach ($registros as $registro) {
                $proyecto = new Proyectos();
                $proyecto->id_empresa_sol = $registro->id_empresa_solicitante;
                $proyecto->id_empresa_con = $registro->id_empresa_contratante;
                $proyecto->nombre_proyecto = $registro->proyecto;

                $proyecto->save();
            }
            
            DB::commit();
            $rpta = [
                "success" => "Ok",
                "mensaje" => "Se creó correctamente los proyectos"
            ];
    
            return response()->json($rpta);
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);
            
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
