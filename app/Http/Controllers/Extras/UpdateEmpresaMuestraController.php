<?php

namespace App\Http\Controllers\Extras;

use App\Http\Controllers\Controller;
use App\Models\Muestras;
use Illuminate\Http\Request;

class UpdateEmpresaMuestraController extends Controller
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
        $muestras = $request->muestras;
        $id_empresa = $request->id_empresa;

        $batchMuestras = array_chunk($muestras,50);
        
        foreach($batchMuestras as $muestra) {
            try {
                //return $muestra;
                Muestras::whereIn('id',$muestra)
                ->update(['id_empresa_con'=>$id_empresa, 'id_empresa_sol'=>$id_empresa]);
            } catch (\Throwable $e) {
                report($e);         
                return false;
            }
        }
        return "Ok";
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
