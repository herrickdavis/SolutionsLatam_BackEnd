<?php

namespace App\Http\Controllers\Extras;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MatrizV2;
use Illuminate\Support\Facades\DB;

class UpdateMatricesV2Controller extends Controller
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
            if($request->id_matriz_v2 != 0) {
                DB::beginTransaction();
        
                // Aquí tu lógica de actualización
                DB::table('muestras')
                    ->where('id_tipo_muestra', $request->id_tipo_muestra)
                    ->update(['id_matriz_v2' => $request->id_matriz_v2]);
        
                DB::commit();
            }
            return response()->json(['success' => true, 'message' => 'Actualización exitosa.']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::info($e);
            return response()->json(['success' => false, 'message' => 'Error al actualizar.'], 404);
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
