<?php

namespace App\Http\Controllers\COC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cadenas;
use App\Models\CadenaLaboratorioParametros;
use App\Models\CadenaInSituParametros;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Throwable;

class SetDataCadenasController extends Controller
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
        try {
            #Sepramos para actualizar y para insertar
            #Insertar
            $insertar = $request->insertar;             
            foreach ($insertar as &$value) {
                if($value['fecha_muestreo'] != null) {
                    try {
                        $fechaCarbon = Carbon::createFromFormat('d/m/Y', $value['fecha_muestreo']);
                        $fechaFormateada = $fechaCarbon->toDateString();
                        $value['fecha_muestreo'] = $fechaFormateada;
                    } catch (\Throwable $th) {
                        $value['fecha_muestreo'] = null;
                    }
                    
                }
                $horaMuestreo = $value['hora_muestreo'];
                $horaMuestreo = $horaMuestreo . ':00';
                $value['hora_muestreo'] = $horaMuestreo;

                $value['created_at'] = Carbon::now();
                $value['updated_at'] = Carbon::now();
            }
            DB::table('cadenas')->insert($insertar);

            //Update
            foreach ($request->actualizar as $cadena) {
                $cadena['updated_at'] = Carbon::now();
                DB::table('cadenas')->where('codigo_laboratorio', $cadena['codigo_laboratorio'])->update($cadena);
            }

            $rpta["estado"] = "OK";
            $rpta["mensaje"] = "Insercion Correcta";

        } catch (Throwable $e) {
            report($e);
            $rpta["estado"] = "ERROR";
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
