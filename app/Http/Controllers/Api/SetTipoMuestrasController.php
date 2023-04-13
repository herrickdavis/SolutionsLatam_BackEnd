<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TipoMuestras;

use Throwable;

class SetTipoMuestrasController extends Controller
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
        $tipo_muestras = $request->all();
        ini_set('max_execution_time', 180); //3 minutes

        
        try {
            foreach ($tipo_muestras as $tipo_muestra) {
                $sql_tipo_muestras = TipoMuestras::updateOrCreate(
                    ['id' => $tipo_muestra['id']],
                    [
                        'nombre_tipo_muestra' => $tipo_muestra['nombre_tipo_muestra'],
                        'activo' => $tipo_muestra['activo']
                    ]
                );
            }
            $rpta["success"] = "Ok";
            $rpta["mensaje"] = "Ok";
        } catch (Throwable $e) {
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
