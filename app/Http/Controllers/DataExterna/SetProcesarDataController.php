<?php

namespace App\Http\Controllers\DataExterna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MuestraExterna;

use Throwable;

class SetProcesarDataController extends Controller
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
        $user = $request->user();
        //Leo los datos a procesar por usuario
        try {
            DB::beginTransaction();
            $resultados = DB::table('data_externa_temporals')->select(
                                        'id_muestras',
                                        'id_matriz',
                                        'id_tipo_muestra',
                                        'id_proyecto',
                                        'id_estacion',
                                        'id_empresa_sol',
                                        'id_empresa_con')
                                    ->where('id_user', $user->id)->get()->toArray();;
            //inserto los registros en muestras
            MuestraExterna::insert($resultados);
            //inserto los registros en parametros
            $resultados = DB::table('data_externa_temporals')->select(
                'id_muestras AS id_muestra_externa',
                'id_parametro',
                'valor',
                'id_unidad')
            ->where('id_user', $user->id)->get()->toArray();;
            //elimino los registros
            DB::table('data_externa_temporals')->where('id_user', $user->id)->delete();

            DB::commit();

            $rpta["success"] = "Ok";
            $rpta["mensaje"] = "Ok";
        } catch (Throwable $e) {
            DB::rollBack();
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
