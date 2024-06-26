<?php

namespace App\Http\Controllers\DataExterna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataExternaTemporal;

class SetMuestrasDataExternaController extends Controller
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
        foreach ($request->all() as $muestra) {
            $muestra_query = DataExternaTemporal::find($muestra["id"]);
            $muestra_query->id_matriz = $muestra["id_matriz"];
            $muestra_query->id_tipo_muestra = $muestra["id_tipo_muestra"];
            $muestra_query->id_estacion = $muestra["id_estacion"];
            $muestra_query->id_proyecto = $muestra["id_proyecto"];
            $muestra_query->id_empresa_contratante = $muestra["id_empresa_contratante"];
            $muestra_query->id_empresa_solicitante = $muestra["id_empresa_solicitante"];
            if (strncmp($muestra["id_parametro"], "P", 1) === 0) {
                $id_parametro = substr($muestra["id_parametro"],1);
            } else if (strncmp($string, "g", 1) === 0) {
                echo "El string no comienza ni con 'p' ni con 'g'.";
            }
            $muestra_query->id_parametro = ;
            $muestra_query->save();
        }
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
