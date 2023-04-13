<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Empresas;

class GetEmpresasPorUsuarioController extends Controller
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
        $id_pais = DB::table('empresas as e')
                        ->select('id_pais')
                        ->leftjoin('users AS u', 'u.id_empresa', '=', 'e.id')
                        ->where('u.id', $user->id)->first();

        if ($user->id_rol == 2) {
            $empresas = Empresas::select(['id','nombre_empresa'])->where('con_historico', 'S')->orderBy('nombre_empresa')->get();
        } else {
            $empresas = Empresas::select(['id','nombre_empresa'])->where('con_historico', 'S')->where('id_pais',$id_pais->id_pais)->orderBy('nombre_empresa')->get();
        }
        
        return $empresas;
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
