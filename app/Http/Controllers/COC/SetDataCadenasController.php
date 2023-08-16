<?php

namespace App\Http\Controllers\COC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cadenas;
use App\Models\CadenaLaboratorioParametros;
use App\Models\CadenaInSituParametros;
use Illuminate\Support\Facades\DB;
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
            $chunks = array_chunk($insertar, 500);

            foreach ($chunks as $chunk) {
                DB::table('cadenas')->insert($chunk);
            }

            //Update
            foreach ($request->actualizar as $cadena) {
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
