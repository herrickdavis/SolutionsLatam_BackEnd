<?php

namespace App\Http\Controllers\COC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CadenaPlantillas;
use Illuminate\Support\Facades\DB;

class GetArchivoPlantillaController extends Controller
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
        $id = $request->id;
        $registro = CadenaPlantillas::find($id);

        if (!$registro) {
            // Manejar el caso cuando el registro no existe
            abort(404);
        }

        $contenidoArchivo = $registro->plantilla;
        $nombreArchivo = $registro->nombre_plantilla;
        $extensionArchivo = $registro->extension;

        return response($contenidoArchivo)
            ->header('Content-Type', 'application/octet-stream')
            ->header('Content-Disposition', 'attachment; filename="' . $nombreArchivo.".".$extensionArchivo.'"');
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
        DB::table("cadena_plantillas")
            ->where('id', $id)
            ->update(['activo' => 'N']);

        return response()->json(['mensaje' => 'Registro Eliminado correctamente']);
    }
}
