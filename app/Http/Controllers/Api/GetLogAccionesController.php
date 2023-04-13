<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetLogAccionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$data = ClickBotones::all();
        $data = DB::table('click_botones AS cb')
                ->select(DB::raw(
                    "cb.id as id,
                    b.id AS id_boton,
                    b.nombre_boton AS nombre_boton,
                    u.id AS id_usuario,
                    u.name AS nombre_usuario,
                    cb.created_at AS creado"                    
                ))
                ->leftjoin('botones AS b', 'b.id', '=', 'cb.id_boton')
                ->leftjoin('users AS u', 'u.id', '=', 'cb.id_user')
                ->where('u.id_rol','=','4')
                ->get();
        return $data;
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
