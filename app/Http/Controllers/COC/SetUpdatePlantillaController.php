<?php

namespace App\Http\Controllers\COC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CadenaPlantillas;
use Throwable;

class SetUpdatePlantillaController extends Controller
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
            $cadena_plantilla = CadenaPlantillas::find($request->id);
            $cadena_plantilla->nombre_plantilla = $request->nombre_plantilla;
            if ($request->hasFile('plantilla')) {
                // Se envió un archivo al request
                $file = $request->file('plantilla');
                $fileName = $request->nombre_plantilla;
                $fileContent = file_get_contents($file);
                $extension = $file->getClientOriginalExtension();
                $cadena_plantilla->nombre_plantilla = $fileName;
                $cadena_plantilla->plantilla = $fileContent;
                $cadena_plantilla->extension = $extension;
            }
            $cadena_plantilla->save();

            $rpta["estado"] = "OK";
            $rpta["mensaje"] = "Se actualizo correctamente";
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
